<?php

function get_user_id($has_user_id_str)
{
	$user_id = rand(1, 100000);

	if (find_id($has_user_id_str, $user_id)) {
		$user_id = get_user_id($has_user_id_str);
	}
	return $user_id;
}

include_once ("inc/auth.inc.php");
include_once ("inc/utility_all.php");
include_once ("inc/utility_org.php");
include_once ("inc/check_type.php");
include_once ("inc/td_core.php");
include_once ("inc/utility_user.php");
include_once ("inc/utility_secu.php");
include_once ("inc/utility_email_audit.php");
$secu_arr = check_secure();
$secu = $secu_arr["SWITCH"];
if (($secu == 1) && (get_secure_priv("sys_user_edit") != 1)) {
	message(_("提示"), _("无管理权限"));
	button_back();
	exit();
}

$HTML_PAGE_TITLE = _("新建用户");
include_once ("inc/header.inc.php");
echo "\r\n<body class=\"bodycolor\">\r\n\r\n";
$has_user_id_str = "";
$query = "SELECT UID,USER_ID FROM user";
$cursor = exequery(TD::conn(), $query);

while ($row = mysql_fetch_array($cursor)) {
	$has_user_id_str .= $row["USER_ID"] . ",";
}

$USER_ID = get_user_id($has_user_id_str);
$BIND_IP = str_replace("\r\n", ",", $BIND_IP);
$BIND_IP = str_replace("\n", ",", $BIND_IP);
$BIND_IP = trim($BIND_IP);
$BYNAME = trim($BYNAME);
$USER_NAME = trim($USER_NAME);

if ($BYNAME == "") {
	message(_("错误"), _("用户名不能为空"));
	button_back();
	exit();
}

if ($USER_NAME == "") {
	message(_("错误"), _("用户姓名不能为空"));
	button_back();
	exit();
}

$DEPT_ID = intval($DEPT_ID);

if (!is_dept_priv($DEPT_ID)) {
	message(_("错误"), _("您没有建立该部门用户的权限"));
	button_back();
	exit();
}

$USER_PRIV = intval($USER_PRIV);

if ($USER_PRIV <= 0) {
	message(_("错误"), _("角色无效"));
	button_back();
	exit();
}

$query = "SELECT * from USER_PRIV where USER_PRIV='" . $_SESSION["LOGIN_USER_PRIV"] . "'";
$cursor = exequery(TD::conn(), $query);

if ($ROW = mysql_fetch_array($cursor)) {
	$PRIV_NO = $ROW["PRIV_NO"];
}

if ($_SESSION["LOGIN_USER_PRIV_TYPE"] == "2") {
	$query = "SELECT USER_PRIV from USER_PRIV where USER_PRIV='$USER_PRIV'";
} else if ($_SESSION["LOGIN_USER_PRIV"] != "1") {
	$query = "SELECT USER_PRIV from USER_PRIV where PRIV_NO>'$PRIV_NO' and USER_PRIV='$USER_PRIV'";
} else {
	$query = "SELECT USER_PRIV from USER_PRIV where USER_PRIV='$USER_PRIV'";
}

$cursor = exequery(TD::conn(), $query);

if (mysql_num_rows($cursor) <= 0) {
	message(_("错误"), _("您没有建立该角色用户的权限"));
	button_back();
	exit();
}

$BYNAME = str_replace(array(",", "\\\"", "\'", "\"", "'", "\t", "\\", "\\\\"), array("", "", "", "", "", "", "", ""), $BYNAME);
$USER_NAME = str_replace(array(",", "\\\"", "\'", "\"", "'", "\t", "\\", "\\\\"), array("", "", "", "", "", "", "", ""), $USER_NAME);

if ($NOT_LOGIN == 0) {
	login_check("[TDCORE_ADDUSER]", "[TDCORE_ADDUSER]");
}

if ((strstr($BYNAME, "\'") != false) || (strstr($BYNAME, ",") != false)) {
	message(_("错误"), _("用户名中含有非法字符"));
	button_back();
	exit();
}

if (strstr($PASSWORD, "\'") != false) {
	message(_("错误"), _("密码中含有非法字符"));
	button_back();
	exit();
}

if (($BIRTHDAY != "") && !is_date($BIRTHDAY)) {
	message(_("错误"), sprintf(_("生日格式不合法，应形如：%s"), date("Y-m-d", time())));
	button_back();
	exit();
}

if (substr($BYNAME, -1) == "\\") {
	$BYNAME = substr($BYNAME, 0, -1);
}

if (substr($USER_NAME, -1) == "\\") {
	$USER_NAME = substr($USER_NAME, 0, -1);
}

$query = "select * from USER where BYNAME='$BYNAME'";
$cursor = exequery(TD::conn(), $query, true);

if ($ROW = mysql_fetch_array($cursor)) {
	message(_("错误"), sprintf(_("用户名 %s 已存在"), $BYNAME));
	button_back();
	exit();
}

if ($USER_NO == "") {
	$USER_NO = 10;
}

if (!is_number($USER_NO)) {
	message(_("错误"), _("用户排序号应为数字"));
	button_back();
	exit();
}

if (($secu == 1) && (get_secure_priv("sys_user_edit") == 1)) {
	$NOT_LOGIN = 1;
}

if ($NOT_VIEW_USER == "on") {
	$NOT_VIEW_USER = 1;
}
else {
	$NOT_VIEW_USER = 0;
}

if ($NOT_VIEW_TABLE == "on") {
	$NOT_VIEW_TABLE = 1;
} else {
	$NOT_VIEW_TABLE = 0;
}

if ($DEPT_ID == 0) {
	$NOT_MOBILE_LOGIN = 1;
}

if ($MOBIL_NO_HIDDEN == "on") {
	$MOBIL_NO_HIDDEN = "1";
} else {
	$MOBIL_NO_HIDDEN = "0";
}

if ($USEING_KEY == "on") {
	$USEING_KEY = 1;
} else {
	$USEING_KEY = 0;
}

if ($USING_FINGER == "on") {
	$USING_FINGER = 1;
} else {
	$USING_FINGER = 0;
}

if ($IS_LUNAR == "on") {
	$IS_LUNAR = 1;
} else {
	$IS_LUNAR = 0;
}

$EMAIL_CAPACITY = intval($EMAIL_CAPACITY);
$FOLDER_CAPACITY = intval($FOLDER_CAPACITY);

if ($IS_WEBMAIL == "on") {
	$WEBMAIL_NUM = -1;
} else {
	$WEBMAIL_NUM = intval($WEBMAIL_NUM);
}

$WEBMAIL_CAPACITY = intval($WEBMAIL_CAPACITY);
if (($IS_WEBMAIL != "on") && (!is_int($WEBMAIL_NUM) || ($WEBMAIL_NUM < 0))) {
	message(_("错误"), _("Internet邮箱数量应为整数！"));
	button_back();
	exit();
}

if (($IS_WEBMAIL != "on") && (!is_int($WEBMAIL_CAPACITY) || ($WEBMAIL_CAPACITY < 0))) {
	message(_("错误"), _("每个Internet邮箱容量应为整数！"));
	button_back();
	exit();
}

if (($EMAIL_CAPACITY != "") && (!is_int($EMAIL_CAPACITY) || ($EMAIL_CAPACITY < 0))) {
	message(_("错误"), _("内部邮箱容量应为整数！"));
	button_back();
	exit();
}

if (($FOLDER_CAPACITY != "") && (!is_int($FOLDER_CAPACITY) || ($FOLDER_CAPACITY < 0))) {
	message(_("错误"), _("个人文件柜容量应为整数！"));
	button_back();
	exit();
}

if ($USE_POP3 != "on") {
	$USE_POP3 = 0;
} else {
	$USE_POP3 = 1;
}

if ($USE_EMAIL != "on") {
	$USE_EMAIL = 0;
} else {
	$USE_EMAIL = 1;
}

$query = "SELECT * from USER_PRIV where USER_PRIV='$USER_PRIV'";
$cursor = exequery(TD::conn(), $query);

if ($ROW = mysql_fetch_array($cursor)) {
	$FUNC_ID_STR = $ROW["FUNC_ID_STR"];
	$PRIV_NO = $ROW["PRIV_NO"];
	$PRIV_NAME = $ROW["PRIV_NAME"];
	$USER_PRIV_TYPE = $ROW["PRIV_TYPE"];
}

$SHORTCUT = check_id($FUNC_ID_STR, "1,2,3,42,4,147,8,9,16,130,5,131,132,182,183,24,15,76,", true);
$DEPT_ID_OTHER = check_id($DEPT_ID, $DEPT_ID_OTHER, false);
$PASSWORD = crypt($PASSWORD);
$USER_NAME_INDEX = getchnprefix($USER_NAME);
$USER_MANAGE_ORGS = ($_SESSION["MYOA_IS_GROUP"] == "1" ? $USER_MANAGE_ORGS : "");
// echo $USER_ID;exit;
$ARRAY = array("USER_ID" => $USER_ID, "BYNAME" => $BYNAME, "USER_NAME" => $USER_NAME, "USER_NAME_INDEX" => $USER_NAME_INDEX, "SEX" => $SEX, "PASSWORD" => $PASSWORD, "USER_PRIV" => $USER_PRIV, "POST_PRIV" => $POST_PRIV, "POST_PRIV" => $POST_PRIV, "IM_RANGE" => $IM_RANGE, "DEPT_ID" => $DEPT_ID, "DEPT_ID_OTHER" => $DEPT_ID_OTHER, "AVATAR" => $SEX, "CALL_SOUND" => 1, "SMS_ON" => $SMS_ON, "USER_PRIV_OTHER" => $PRIV_ID, "USER_NO" => $USER_NO, "NOT_LOGIN" => $NOT_LOGIN, "NOT_VIEW_USER" => $NOT_VIEW_USER, "NOT_VIEW_TABLE" => $NOT_VIEW_TABLE, "NOT_MOBILE_LOGIN" => $NOT_MOBILE_LOGIN, "BIRTHDAY" => $BIRTHDAY, "THEME" => $THEME, "SHORTCUT" => $SHORTCUT, "MOBIL_NO" => $MOBIL_NO, "MOBIL_NO_HIDDEN" => $MOBIL_NO_HIDDEN, "BIND_IP" => $BIND_IP, "KEY_SN" => $KEY_SN, "USEING_KEY" => $USEING_KEY, "REMARK" => $REMARK, "TEL_NO_DEPT" => $TEL_NO_DEPT, "EMAIL" => $EMAIL, "USING_FINGER" => $USING_FINGER, "IS_LUNAR" => $IS_LUNAR, "USER_PRIV_NO" => $PRIV_NO, "USER_PRIV_NAME" => $PRIV_NAME, "USER_PRIV_TYPE" => $USER_PRIV_TYPE, "USER_MANAGE_ORGS" => $USER_MANAGE_ORGS);
$email_audit_flag = check_email_audit(3);
if ($email_audit_flag) {
	$ARRAY["SECRET_LEVEL"] = $SECRET_LEVEL;
}
// 2017-2-28 20:38:11 add_user()函数在inc/utility_user.php中，
// 实现了把用户信息添加到user数据库表中，并返回自增的UID，的功能
// V2015添加用户的user_id是自增的，不满足业务需求，已在此函数下做了修改（）。
	$UID = add_user($ARRAY);
if ($email_audit_flag && (intval($SECRET_LEVEL) != intval($SECRET_LEVEL_OLD))) {
	$log_data = array("src" => intval($SECRET_LEVEL_OLD), "des" => intval($SECRET_LEVEL), "change_user" => $USER_ID);
	addemailauditlog(72, $_SESSION["LOGIN_UID"], $log_data);
}

// 2017-2-28 20:38:11修改字段USER_ID的属性变量$USER_ID为$BYNAME
$query = "insert into USER_EXT(UID,USER_ID,USE_POP3,EMAIL_CAPACITY,FOLDER_CAPACITY,WEBMAIL_CAPACITY,WEBMAIL_NUM,DUTY_TYPE,USE_EMAIL) values('$UID','$BYNAME','$USE_POP3','$EMAIL_CAPACITY','$FOLDER_CAPACITY','$WEBMAIL_CAPACITY','$WEBMAIL_NUM','$DUTY_TYPE','$USE_EMAIL')";
exequery(TD::conn(), $query);

if ($NOT_LOGIN == 0) {
	set_sys_para(array("ORG_UPDATE" => date("Y-m-d H:i:s")));
}

if ($DOMAIN_USER != "") {
	include_once ("inc/ldap/adLDAP.php");
	$SYNC_CONFIG = get_sys_para("DOMAIN_SYNC_CONFIG");
	$SYNC_CONFIG = unserialize($SYNC_CONFIG["DOMAIN_SYNC_CONFIG"]);
	$option = get_ldap_option($SYNC_CONFIG);
	$adldap = new adLDAP($option);

	if ($adldap->authenticate($SYNC_CONFIG["AD_USER"], $SYNC_CONFIG["AD_PWD"])) {
		$user_info = $adldap->user_info(iconv(MYOA_CHARSET, "utf-8", $DOMAIN_USER), array("objectguid"));

		if ($user_info === false) {
			message("", _("获取域用户[$DOMAIN_USER]信息出错(") . $adldap->get_last_error() . ")");
			$ERR_FLAG = 1;
		} else {
			$user_info = $user_info[0];
			if (!is_array($user_info) || !is_array($user_info["objectguid"]) || ($user_info["objectguid"][0] == "")) {
				message("", _("域用户[$DOMAIN_USER]不存在"));
				$ERR_FLAG = 1;
			} else {
				$USER_GUID = bin2guid($user_info["objectguid"][0]);
				$query = "select * from USER_MAP where USER_ID='$USER_ID'";
				$cursor = exequery(TD::conn(), $query);

				if ($ROW = mysql_fetch_array($cursor)) {
					$query = "update USER_MAP set USER_GUID='$USER_GUID' where USER_ID='$USER_ID';";
					exequery(TD::conn(), $query);
				} else {
					$query = "insert into USER_MAP (USER_ID,USER_GUID) values ('$USER_ID','$USER_GUID');";
					exequery(TD::conn(), $query);
				}
			}
		}
	} else {
		message("", _("域相关参数设置有误") . "(" . $adldap->get_last_error() . ")");
		$ERR_FLAG = 1;
	}
}

if (($PRIV_ID1 != "") || ($USER_ID1 != "") || ($DEPT_ID_MSG != "")) {
	$query = "select * from MODULE_PRIV where UID='$UID' and MODULE_ID='0'";
	$cursor = exequery(TD::conn(), $query);

	if ($ROW = mysql_fetch_array($cursor)) {
		$query = "update MODULE_PRIV set PRIV_ID='$PRIV_ID1',USER_ID='$USER_ID1',DEPT_ID='$DEPT_ID_MSG' where UID='$UID' and MODULE_ID='0'";
		exequery(TD::conn(), $query);
	} else {
		$query = "insert into MODULE_PRIV (UID,MODULE_ID,DEPT_PRIV,ROLE_PRIV,PRIV_ID,USER_ID,DEPT_ID) values('$UID','0','1','2','$PRIV_ID1','$USER_ID1','$DEPT_ID_MSG')";
		exequery(TD::conn(), $query);
	}
} else {
	$query = "delete from MODULE_PRIV where UID='$UID' and MODULE_ID='0'";
	exequery(TD::conn(), $query);
}

if (file_exists("fis_acset_update.php")) {
	include_once ("fis_acset_update.php");
}

if (($secu == 1) && (get_secure_priv("sys_user_edit") == 1)) {
	$CONTENT = _("添加用户：$USER_NAME");
	add_secure_log(1, $CONTENT);
}

message("", _("用户增加成功"));
cache_users();
echo "\r\n<script>\r\n//opener.parent.user_list.location.reload();\r\nwindow.parent.opener.location.reload(); \r\n";

if (!$ERR_FLAG) {
	echo "    opener.location=\"user_new.php?DEPT_ID=";
	echo $DEPT_ID;
	echo "&IS_MAIN=1\";\r\n";
}

echo "</script>\r\n\r\n<div align=\"center\">\r\n    <input type=\"button\" value=\"";
echo _("继续新建用户");
echo "\" class=\"BigButton\" title=\"";
echo _("继续新建用户");
echo "\" onClick=\"location='new.php?DEPT_ID=";
echo $DEPT_ID;
echo "'\">&nbsp;&nbsp;\r\n    <input type=\"button\" value=\"";
echo _("建立档案");
echo "\" class=\"BigButton\" title=\"";
echo _("建立档案");
echo "\" onClick=\"location='../../hr/manage/staff_info/staff_info.php?USER_ID=";
echo $UID;
echo "&DEPT_ID=";
echo $DEPT_ID;
echo "&STAFF_BIRTH=";
echo $BIRTHDAY;
echo "&IS_LUNAR=";
echo $IS_LUNAR;
echo "'\">&nbsp;&nbsp;\r\n    <input type=\"button\" value=\"";
echo _("关闭");
echo "\" class=\"BigButton\" title=\"";
echo _("关闭窗口");
echo "\" onClick=\"window.close();\">\r\n</div>\r\n</body>\r\n</html>\r\n";

?>
