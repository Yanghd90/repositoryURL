<?php

include_once ("inc/auth.inc.php");
include_once ("inc/utility_all.php");
include_once ("inc/utility_org.php");
include_once ("inc/utility_file.php");
include_once ("inc/check_type.php");
include_once ("inc/utility_field.php");
include_once ("inc/utility_cache.php");
include_once ("inc/td_core.php");
if (strstr($BYNAME, "/") || strstr($BYNAME, "\\") || strstr($BYNAME, "..")) {
	message(_("错误"), _("OA用户名包含非法字符。"));
	exit();
}
// 2018-3-6 17:13:01此处USER_ID值为空，新增表单中并没有USER_ID字段
// 所以会按照下发函数生成一个随机数值赋给USER_ID,此值会作为临时USER_ID保存到user表中，然后查询UID的值并更新USER_ID
if ($USER_ID == "") {
	$has_user_id_str = "";
	$query = "SELECT UID,USER_ID FROM user";
	$cursor = exequery(TD::conn(), $query);

	while ($row = mysql_fetch_array($cursor)) {
		$has_user_id_str .= $row["USER_ID"] . ",";
	}
	function get_user_id($has_user_id_str, $byname) {
		$user_id = rand(1, 100000);
		if (find_id($has_user_id_str, $user_id) || ($byname == $user_id)) {
			$user_id = get_user_id($has_user_id_str);
		}
		return $user_id;
	}

	$USER_ID = get_user_id($has_user_id_str, $BYNEME);
	if (find_id($has_user_id_str, $BYNEME)) {
		message(_("错误"), _("此用户名已存在。"));
		button_back();
		exit();
	}
}

$HTML_PAGE_TITLE = _("新建人事档案");
include_once ("inc/header.inc.php");
echo "\r\n<body class=\"bodycolor\">\r\n";
$IS_LUNAR = ($IS_LUNAR == "1" ? "1" : "0");
$PHOTO_NAME0 = $_FILES["ATTACHMENT"]["name"];
$ATTACHMENT = $_FILES["ATTACHMENT"]["tmp_name"];

if ($PHOTO_NAME0 != "") {
	$FULL_PATH = MYOA_ATTACH_PATH . "hrms_pic";

	if (!file_exists($FULL_PATH)) {
		@mkdir($FULL_PATH, 448);
	}

	$PHOTO_NAME = $USER_ID . substr($PHOTO_NAME0, strrpos($PHOTO_NAME0, "."));
	$FILENAME = MYOA_ATTACH_PATH . "hrms_pic/" . $PHOTO_NAME;
	td_copy($ATTACHMENT, $FILENAME);
	unlink($ATTACHMENT);

	if (!file_exists($FILENAME)) {
		message(_("附件上传失败"), _("原因：附件文件为空或文件名太长，或附件大于30兆字节，或文件路径不存在！"));
		button_back();
		exit();
	}
}

if (1 < count($_FILES)) {
	$ATTACHMENTS = upload("ATTACHMENT1");
	$ATTACHMENT_ID = $ATTACHMENT_ID_OLD . $ATTACHMENTS["ID"];
	$ATTACHMENT_NAME = $ATTACHMENT_NAME_OLD . $ATTACHMENTS["NAME"];
} else {
	$ATTACHMENT_ID = $ATTACHMENT_ID_OLD;
	$ATTACHMENT_NAME = $ATTACHMENT_NAME_OLD;
}

$ATTACHMENT_ID .= copy_sel_attach($ATTACH_NAME1, $ATTACH_DIR1, $DISK_ID1);
$ATTACHMENT_NAME .= $ATTACH_NAME1;
$C = preg_match("/<img.*?\ssrc=\\\\\"\/inc\/attach.php\?(.*)MODULE=upload_temp/i", $RESUME);
$RESUME = replace_attach_url($RESUME);

if ($C == 1) {
	$ATTACHMENT_ID = move_attach($ATTACHMENT_ID, $ATTACHMENT_NAME, "", "", true) . ",";
}

$CUR_TIME = date("Y-m-d H:i:s", time());

if ($YES_OR_NOT == "on") {
	$YES_OR_NOT = 1;
	$NOT_LOGIN = 0;
	$NOT_MOBILE_LOGIN = 0;
	login_check("[TDCORE_ADDUSER]", "[TDCORE_ADDUSER]");
} else {
	$YES_OR_NOT = 0;
	$NOT_LOGIN = 1;
	$NOT_MOBILE_LOGIN = 1;
}

if ($batch == "1") {
	$IS_EXPERTS = 1;
} else {
	$IS_EXPERTS = 0;
}

if (find_id($COPY_TO_ID1, $USER_ID) || find_id($COPY_TO_ID, $USER_ID)) {
	message(_("错误"), _("直属上级和直属下级中包含有本人。"));
	button_back();
	exit();
}

$COPY_TO_ID_ARRAY = explode(",", trim($COPY_TO_ID, ","));

foreach ($COPY_TO_ID_ARRAY as $value ) {
	if (find_id($COPY_TO_ID1, $value)) {
		message(_("错误"), _("直属上级和直属下级有重复的数据。"));
		button_back();
		exit();
	} else {
		$query_info = "select * from hr_staff_info where USER_ID='$value'";
		$cursor_info = exequery(TD::conn(), $query_info);

		if ($ROW = mysql_fetch_array($cursor_info)) {
			$DIRECTLY_UNDER = $ROW["DIRECTLY_UNDER"];
			$DIRECTLY_UNDER_ARRAY = explode(",", trim($DIRECTLY_UNDER, ","));

			foreach ($DIRECTLY_UNDER_ARRAY as $value1 ) {
				if (find_id($COPY_TO_ID1, $value)) {
					message(_("错误"), _("直属上级和下级的下级有重复的数据。"));
					button_back();
					exit();
				}
			}
		}
	}
}

$query = "select * from USER where USER_ID='$USER_ID'";
$cursor = exequery(TD::conn(), $query);
if (!$ROW = mysql_fetch_array($cursor)) {
	$query2 = "SELECT PRIV_NO,PRIV_NAME FROM USER_PRIV WHERE USER_PRIV='$USER_PRIV'";
	$cursor2 = exequery(TD::conn(), $query2);
	if ($ROW2 = mysql_fetch_array($cursor2)) {
		$USER_PRIV_NO = $ROW2["PRIV_NO"];
		$USER_PRIV_NAME = $ROW2["PRIV_NAME"];
	}

	$USER_NAME_INDEX = getchnprefix($STAFF_NAME);
	$PASSWORD = crypt("");
	$SYS_INTERFACE = TD::get_cache("SYS_INTERFACE");
	$THEME = $SYS_INTERFACE["THEME"];
	$query = "insert into USER (USER_ID,USER_NAME,USER_NAME_INDEX,SEX,PASSWORD,USER_PRIV,USER_PRIV_NO,USER_PRIV_NAME,POST_PRIV,POST_DEPT,DEPT_ID,AVATAR,CALL_SOUND,SMS_ON,MENU_TYPE,USER_PRIV_OTHER,USER_NO,NOT_LOGIN,BYNAME,BIRTHDAY,IS_LUNAR,THEME,MOBIL_NO,MOBIL_NO_HIDDEN,NOT_MOBILE_LOGIN)values ('$USER_ID','$STAFF_NAME','$USER_NAME_INDEX','$STAFF_SEX','$PASSWORD','$USER_PRIV','$USER_PRIV_NO','$USER_PRIV_NAME','0','','$DEPT_ID','$STAFF_SEX','1','1','2','','','$NOT_LOGIN','$BYNAME','$STAFF_BIRTH','$IS_LUNAR','$THEME','','','$NOT_MOBILE_LOGIN')";
	exequery(TD::conn(), $query);
	$UID = mysql_insert_id();
// ===================================  修改  ===============================
	// 2018-3-6 17:21:06此处可以看到为什么USER_ID和UID一致
	// $USER_ID = $UID;
	// $query = "update user set USER_ID='$UID' where UID='$UID'";
	// 现做以下修改，让USER_ID的值与BYNAME（填写到用户名表单中的值）保持一致
	$USER_ID = $BYNAME;
	$query = "update user set USER_ID='$USER_ID' where UID='$UID'";
	exequery(TD::conn(), $query);
	$query = "insert into USER_EXT(UID,USER_ID,EMAIL_CAPACITY,FOLDER_CAPACITY,DUTY_TYPE) values('$UID','$USER_ID',0,0,'1')";
	exequery(TD::conn(), $query);
	set_uid_menu_priv($UID, $USER_ID, $USER_PRIV);

	if (!file_exists(MYOA_ATTACH_PATH . "new_sms/" . $UID . ".sms")) {
		new_sms_remind($UID, 0);
	}

	if ($NOT_LOGIN == 0) {
		set_sys_para(array("ORG_UPDATE" => date("Y-m-d H:i:s")));
	}
} else {
	$where_str = "";
	$sql = "SELECT UID FROM user WHERE (AVATAR=0 or AVATAR=1) and USER_ID='$USER_ID'";
	$cur = exequery(TD::conn(), $sql);

	if (0 < mysql_affected_rows()) {
		$where_str = ",AVATAR='$STAFF_SEX' ";
	}
	$query = "update USER set SEX='$STAFF_SEX',BIRTHDAY='$STAFF_BIRTH',IS_LUNAR='$IS_LUNAR'" . $where_str . " where USER_ID='$USER_ID'";
	exequery(TD::conn(), $query);
}

$query = "insert into HR_STAFF_INFO (CREATE_USER_ID,CREATE_DEPT_ID,USER_ID,DEPT_ID,STAFF_NO,WORK_NO,WORK_TYPE,STAFF_NAME,BEFORE_NAME,STAFF_E_NAME,STAFF_CARD_NO,STAFF_SEX,BLOOD_TYPE,STAFF_BIRTH,STAFF_NATIVE_PLACE,STAFF_NATIVE_PLACE2,STAFF_DOMICILE_PLACE,STAFF_NATIONALITY,STAFF_MARITAL_STATUS,STAFF_POLITICAL_STATUS,JOIN_PARTY_TIME,STAFF_PHONE,STAFF_MOBILE,STAFF_LITTLE_SMART,STAFF_EMAIL,STAFF_MSN,STAFF_QQ,HOME_ADDRESS,OTHER_CONTACT,JOB_BEGINNING,WORK_AGE,STAFF_HEALTH,STAFF_HIGHEST_SCHOOL,STAFF_HIGHEST_DEGREE,GRADUATION_DATE,GRADUATION_SCHOOL,STAFF_MAJOR,COMPUTER_LEVEL,FOREIGN_LANGUAGE1,FOREIGN_LEVEL1,FOREIGN_LANGUAGE2,FOREIGN_LEVEL2,FOREIGN_LANGUAGE3,FOREIGN_LEVEL3,STAFF_SKILLS,STAFF_OCCUPATION,ADMINISTRATION_LEVEL,JOB_POSITION,PRESENT_POSITION,DATES_EMPLOYED,JOB_AGE,BEGIN_SALSRY_TIME,RECORD_DATE,WORK_STATUS,STAFF_CS,STAFF_CTR,REMARK,STAFF_COMPANY,PHOTO_NAME,ATTACHMENT_ID,ATTACHMENT_NAME,RESUME,LEAVE_TYPE,STAFF_TYPE,YES_OR_NOT,USERDEF1,USERDEF2,USERDEF3,USERDEF4,USERDEF5,CERTIFICATE,SURETY,INSURE,BODY_EXAMIM,ADD_TIME,LAST_UPDATE_TIME,WORK_LEVEL,WORK_JOB,IS_LUNAR,IS_EXPERTS,EXPERTS_INFO,DIRECTLY_UNDER,DIRECTLY_SUPERIOR,PART_TIME,RESEARCH_RESULTS,BANK1,BANK_ACCOUNT1,BANK2,BANK_ACCOUNT2) values ('" . $_SESSION["LOGIN_USER_ID"] . "','" . $_SESSION["LOGIN_DEPT_ID"] . "','$USER_ID','$DEPT_ID','$STAFF_NO','$WORK_NO','$WORK_TYPE','$STAFF_NAME','$BEFORE_NAME','$STAFF_E_NAME','$STAFF_CARD_NO','$STAFF_SEX','$BLOOD_TYPE','$STAFF_BIRTH','$STAFF_NATIVE_PLACE','$STAFF_NATIVE_PLACE2','$STAFF_DOMICILE_PLACE','$STAFF_NATIONALITY','$STAFF_MARITAL_STATUS','$STAFF_POLITICAL_STATUS','$JOIN_PARTY_TIME','$STAFF_PHONE','$STAFF_MOBILE','$STAFF_LITTLE_SMART','$STAFF_EMAIL','$STAFF_MSN','$STAFF_QQ','$HOME_ADDRESS','$OTHER_CONTACT','$JOB_BEGINNING','$WORK_AGE','$STAFF_HEALTH','$STAFF_HIGHEST_SCHOOL','$STAFF_HIGHEST_DEGREE','$GRADUATION_DATE','$GRADUATION_SCHOOL','$STAFF_MAJOR','$COMPUTER_LEVEL','$FOREIGN_LANGUAGE1','$FOREIGN_LEVEL1','$FOREIGN_LANGUAGE2','$FOREIGN_LEVEL2','$FOREIGN_LANGUAGE3','$FOREIGN_LEVEL3','$STAFF_SKILLS','$STAFF_OCCUPATION','$ADMINISTRATION_LEVEL','$JOB_POSITION','$PRESENT_POSITION','$DATES_EMPLOYED','$JOB_AGE','$BEGIN_SALSRY_TIME','$RECORD_DATE','$WORK_STATUS','$STAFF_CS','$STAFF_CTR','$REMARK','$STAFF_COMPANY','$PHOTO_NAME','$ATTACHMENT_ID','$ATTACHMENT_NAME','$RESUME','$LEAVE_TYPE','$STAFF_TYPE','$YES_OR_NOT','$USERDEF1','$USERDEF2','$USERDEF3','$USERDEF4','$USERDEF5','$CERTIFICATE','$SURETY','$INSURE','$BODY_EXAMIM','$CUR_TIME','$CUR_TIME','$WORK_LEVEL','$WORK_JOB','$IS_LUNAR','$IS_EXPERTS','$EXPERTS_INFO','$COPY_TO_ID','$COPY_TO_ID1','$PART_TIME','$RESEARCH_RESULTS','$BANK1','$BANK_ACCOUNT1','$BANK2','$BANK_ACCOUNT2')";
exequery(TD::conn(), $query);
save_field_data("HR_STAFF_INFO", $USER_ID, $_POST);
cache_users();
message(_("提示"), _("人事档案信息录入成功！"));
echo "<br><center><input type=\"button\" value=\"";
echo _("返回");
echo "\" class=\"BigButton\" onClick=\"location.href='new.php?DEPT_ID=";
echo $DEPT_ID;
echo "&SOURCE=new_hrms'\"></center>\r\n</body>\r\n</html>\r\n";

?>
