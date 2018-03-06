<?php

include_once ("inc/auth.inc.php");
include_once ("inc/td_core.php");
include_once ("inc/utility_org.php");
include_once ("inc/utility_all.php");
include_once ("inc/utility_secu.php");
include_once ("inc/utility_email_audit.php");
$secu_arr = check_secure();
$secu = $secu_arr["SWITCH"];
$secu_user_priv = $secu_arr["USER_PRIV"];
if (($secu == 1) && (get_secure_priv("sys_user_edit") != 1)) {
	message(_("提示"), _("无新建权限"));
	button_back();
	exit();
}

$PARA_ARRAY = get_sys_para("SEC_PASS_MIN,SEC_PASS_MAX,SEC_PASS_SAFE,LOGIN_USE_DOMAIN,LOGIN_KEY");

while (list($PARA_NAME, $PARA_VALUE) = each($PARA_ARRAY)) {
	$$PARA_NAME = $PARA_VALUE;
}

if ($USER_NO == "") {
	$USER_NO = 10;
}

$HTML_PAGE_TITLE = _("新建用户");
include_once ("inc/header.inc.php");
echo "\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"";
echo MYOA_STATIC_SERVER;
echo "/static/theme/";
echo $_SESSION["LOGIN_THEME"];
echo "/index.css\" />\r\n<script type=\"text/javascript\" src=\"";
echo MYOA_JS_SERVER;
echo "/static/js/jquery-1.5.1/jquery.min.js";
echo $GZIP_POSTFIX;
echo "\"></script>\r\n<script type=\"text/javascript\" src=\"";
echo MYOA_JS_SERVER;
echo "/static/js/jquery-1.5.1/jquery-ui.custom.min.js";
echo $GZIP_POSTFIX;
echo "\"></script>\r\n<script type=\"text/javascript\" src=\"";
echo MYOA_JS_SERVER;
echo "/static/js/jquery-1.5.1/jquery.ui.autocomplete.min.js";
echo $GZIP_POSTFIX;
echo "\"></script>\r\n<script src=\"";
echo MYOA_JS_SERVER;
echo "/static/js/module.js?v=";
echo MYOA_SYS_VERSION;
echo "\"></script>\r\n<script type=\"text/javascript\" src=\"";
echo MYOA_JS_SERVER;
echo "/static/js/utility.js\"></script>\r\n<script src=\"";
echo MYOA_JS_SERVER;
echo "/module/DatePicker/WdatePicker.js\"></script>\r\n<style>\r\n*{padding:0px;margin:0px auto;}    \r\n#user_priv:hover\r\n{\r\n    border:1px solid #C0BBB4;\r\n}\r\n.bodycolor{overflow:auto;}\r\n</style>\r\n<script Language=\"JavaScript\">\r\njQuery.noConflict();\r\n\r\nfunction refreshPriv(){\r\n    var user_priv_str = jQuery(\"#USER_PRIV0\").val() + \",\" + jQuery(\"#PRIV_ID\").val();\r\n    jQuery.ajax({\r\n        type: 'POST',\r\n        url:'check_org.php',\r\n        data:{\r\n            user_priv_str: user_priv_str\r\n        },\r\n        async: true,\r\n        success:function(d){\r\n            var data = d;\r\n            if(jQuery(\"#org_manage\") && data.org_id!=\"\")\r\n            {\r\n                jQuery(\"#org_manage\").show();\r\n                jQuery(\"#USER_MANAGE_ORGS\").val(data.org_id);\r\n                jQuery(\"#USER_MANAGE_ORGS_NAME\").val(data.org_name);\r\n            }\r\n            else if(jQuery(\"#org_manage\")){\r\n                jQuery(\"#USER_MANAGE_ORGS\").val('');\r\n                jQuery(\"#USER_MANAGE_ORGS_NAME\").val('');\r\n                jQuery(\"#org_manage\").hide();\r\n            }\r\n            jQuery(\"#USER_PRIV_TYPE\").val(data.user_priv_type);\r\n        }\r\n    });\r\n}\r\n\r\n(function($){\r\n    $(function(){\r\n        jQuery(\"[name='orgClear']\").click(function(){\r\n            var timer = null;\r\n            timer && clearTimeout(timer);\r\n            timer = setTimeout(refreshPriv, 300);\r\n        });\r\n        \r\n        jQuery(\".orgAdd\").click(function(){\r\n            if(jQuery(this).attr(\"name\") == \"orgAdd\")\r\n            {\r\n                org_select_callbacks = {};\r\n                org_select_callbacks.add = org_select_callbacks.remove = org_select_callbacks.clear = (function(){\r\n                    var timer = null;\r\n                    return function(){\r\n                        timer && clearTimeout(timer);\r\n                        timer = setTimeout(refreshPriv, 300);\r\n                    }\r\n                })();\r\n            }\r\n            else{\r\n                org_select_callbacks = {};\r\n            }\r\n        });\r\n    })\r\n})(jQuery);\r\n\r\nfunction select_main_priv(id)\r\n{\r\n    var tt = jQuery(\"#USER_PRIV1\").find('option:selected').text();\r\n    jQuery(\"#user_priv\").val(tt); \r\n    jQuery(\"#USER_PRIVS\").val(id);  \r\n}\r\nfunction CheckForm()\r\n{\r\n    if(document.form1.BYNAME.value==\"\")\r\n    {\r\n        alert(\"";
echo _("用户名不能为空！");
echo "\");\r\n        return (false);\r\n    }\r\n    \r\n    if(document.form1.USER_PRIV.value==\"\")\r\n    { \r\n        alert(\"";
echo _("角色不能为空！");
echo "\");\r\n        return (false);\r\n    }\r\n    \r\n    if(document.form1.USER_NAME.value==\"\")\r\n    { \r\n        alert(\"";
echo _("真实姓名不能为空！");
echo "\");\r\n        return (false);\r\n    }\r\n    \r\n    if(document.form1.THEME.value==\"\")\r\n    { \r\n        alert(\"";
echo _("界面主题不能为空！");
echo "\");\r\n        return (false);\r\n    }\r\n    \r\n    if(document.form1.EMAIL.value!=\"\")\r\n    {\r\n        var emailExp = /[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}/;\r\n        if(!document.form1.EMAIL.value.match(emailExp))\r\n        {\r\n            alert(\"";
echo _("请输入有效的E-mail地址！");
echo "\");\r\n            return (false);\r\n        }\r\n    }\r\n}\r\nfunction change_type()\r\n{\r\n    if(document.form1.IS_WEBMAIL.checked)\r\n    {\r\n        internet1.style.display='none';\r\n        internet2.style.display='none';\r\n    }\r\n    else\r\n    {\r\n        internet1.style.display='';\r\n        internet2.style.display='';\r\n    }\r\n}\r\n//通讯白名单按人员设置\r\nfunction changeRange()\r\n{\r\n    if(document.getElementById(\"rang_user\").style.display==\"none\")\r\n    {\r\n        document.getElementById(\"rang_user\").style.display=\"\";\r\n        document.getElementById(\"rang_dept\").style.display=\"\";\r\n        document.getElementById(\"href_txt\").innerText=\"";
echo _("隐藏按人员、部门设置");
echo "\";\r\n    }\r\n    else\r\n    {\r\n        document.getElementById(\"rang_user\").style.display=\"none\";\r\n        document.getElementById(\"rang_dept\").style.display=\"none\";\r\n        document.getElementById(\"href_txt\").innerText=\"";
echo _("按人员、部门设置");
echo "\";\r\n    }\r\n}\r\nfunction select_dept()\r\n{\r\n    if(form1.POST_PRIV.value==\"2\")\r\n        dept.style.display='';\r\n    else\r\n        dept.style.display=\"none\";\r\n}\r\n\r\nfunction select_priv()\r\n{\r\n    if(priv.style.display==\"none\")\r\n        priv.style.display=\"\";\r\n    else\r\n        priv.style.display=\"none\";\r\n}\r\nfunction select_dept_other()\r\n{\r\n    if(dept_other.style.display==\"none\")\r\n        dept_other.style.display='';\r\n    else\r\n        dept_other.style.display=\"none\";\r\n}\r\nfunction check_user(id)\r\n{\r\n    if(id==\"\")\r\n        return;\r\n    \r\n    byname_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/loading_16.gif' align='absMiddle'> ";
echo _("检查中，请稍候……");
echo "\";\r\n    _get(\"check_user.php\",\"BYNAME=\"+encodeURI(id), show_msg);\r\n}\r\nfunction check_user_name(id)\r\n{\r\n    if(id==\"\")\r\n        return;\r\n    \r\n    user_name_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/loading_16.gif' align='absMiddle'> ";
echo _("检查中，请稍候……");
echo "\";\r\n    _get(\"check_user.php\",\"USER_NAME=\"+encodeURI(id), show_msg_name);\r\n}\r\nfunction show_msg_name(req)\r\n{\r\n    if(req.status==200)\r\n    {\r\n        if(req.responseText.substring(0,3)==\"+OK\")\r\n        {\r\n            document.form1.USER_NAME.value=req.responseText.substring(3);\r\n\t\t\tuser_name_msg.innerHTML=\" \";\r\n            user_name_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/correct.gif' align='absMiddle'>\";\r\n        }\r\n        else\r\n        {\r\n            user_name_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/error.gif' align='absMiddle'> ";
echo _("该真实姓名已存在");
echo "\";\r\n        }\r\n    }\r\n    else\r\n    {\r\n        user_name_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/error.gif' align='absMiddle'> ";
echo _("错误：");
echo "\"+req.status;\r\n    }\r\n}\r\nfunction show_msg(req)\r\n{\r\n    if(req.status==200)\r\n    {\r\n        if(req.responseText.substring(0,3)==\"+OK\")\r\n        {\r\n            document.form1.BYNAME.value=req.responseText.substring(3);\r\n\t\t\tbyname_msg.innerHTML=\" \";\r\n            byname_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/correct.gif' align='absMiddle'>\";\r\n        }\r\n        else\r\n        {\r\n            byname_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/error.gif' align='absMiddle'> ";
echo _("该用户名已存在");
echo "\";\r\n            document.form1.BYNAME.focus();\r\n        }\r\n    }\r\n    else\r\n    {\r\n        byname_msg.innerHTML=\"<img src='";
echo MYOA_STATIC_SERVER;
echo "/static/images/error.gif' align='absMiddle'> ";
echo _("错误：");
echo "\"+req.status;\r\n    }\r\n}\r\n\r\nfunction check_userNo()\r\n{\r\n    var userNo=document.form1.USER_NO.value;\r\n    if(!IsNumber(document.form1.USER_NO.value))\r\n        alert(\"";
echo _("用户排序号应为数字");
echo "\");\r\n    if(document.form1.USER_NO.value>65535)\r\n        alert(\"";
echo _("用户排序号应为小于65535的数字");
echo "\");\r\n}\r\nfunction IsNumber(str)\r\n{\r\n    return str.match(/^[0-9]*$/)!=null;\r\n}\r\n</script>\r\n\r\n<body class=\"bodycolor\">\r\n";
$query = "SELECT * FROM user WHERE UID='" . $_SESSION["LOGIN_UID"] . "'";
$cursor = exequery(TD::conn(), $query);

if ($ROW = mysql_fetch_array($cursor)) {
	$POST_PRIV = $ROW["POST_PRIV"];
	$POST_DEPT = $ROW["POST_DEPT"];
}

$query = "SELECT * from USER_PRIV where USER_PRIV='" . $_SESSION["LOGIN_USER_PRIV"] . "'";
$cursor = exequery(TD::conn(), $query);

if ($ROW = mysql_fetch_array($cursor)) {
	$PRIV_NO = $ROW["PRIV_NO"];
}

$DEPT_ID = intval($DEPT_ID);
$DEPT_NAME = ($DEPT_ID == 0 ? _("离职人员/外部人员") : td_trim(getdeptnamebyid($DEPT_ID)));
$SYS_INTERFACE = TD::get_cache("SYS_INTERFACE");
$THEME = $SYS_INTERFACE["THEME"];
$TD_REG = TD::get_cache("TD_REG");
$USER_LIMIT = $TD_REG["USER_LIMIT"];
if (tdoa_check_reg() && ($USER_LIMIT == 0)) {
	$NEW_USER_COUNT = 0;
}
else {
	$query = "SELECT count(*) from USER where NOT_LOGIN='0'";
	$cursor = exequery(TD::conn(), $query);

	if ($ROW = mysql_fetch_array($cursor)) {
		$NEW_USER_COUNT = $TD_USER_LIMIT - $ROW[0];
	}
}

echo "\r\n<body class=\"bodycolor\" onload=\"document.form1.BYNAME.focus();\" style=\"position:relative\">\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" class=\"small\">\r\n    <tr>\r\n        <td class=\"Big\"><img src=\"";
echo MYOA_STATIC_SERVER;
echo "/static/images/notify_new.gif\" align=\"absmiddle\">\r\n            <span class=\"big3\">\r\n";
echo sprintf(_("新建用户（%s）"), $DEPT_NAME);

if (0 < $NEW_USER_COUNT) {
	echo "<span style='font-weight:lighter;font-size:13px;padding-left: 10px;'>" . sprintf(_("您已经创建%s个用户,还能创建%s个用户"), "[<span style='color:red'>" . $ROW[0] . "</span>]", "[<span style='color:red'>" . $NEW_USER_COUNT . "</span>]") . "</span>";
}

echo "            </span>\r\n        </td>\r\n    </tr>\r\n</table>\r\n\r\n<form action=\"add.php\"  method=\"post\" name=\"form1\" onSubmit=\"return CheckForm();\">\r\n<table class=\"TableBlock\" width=\"95%\" align=\"center\">\r\n    <tr>\r\n        <td nowrap class=\"TableHeader\" colspan=\"2\"><img src=\"";
echo MYOA_STATIC_SERVER;
echo "/static/images/green_arrow.gif\" align=\"absMiddle\"> ";
echo _("用户基本信息");
echo "</td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\" width=\"120\">";
echo _("用户名：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"BYNAME\" class=\"BigInput\" size=\"10\" maxlength=\"20\" onBlur=\"check_user(this.value)\">&nbsp;<span id=\"byname_msg\"></span>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("真实姓名：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"USER_NAME\" class=\"BigInput\" size=\"10\" maxlength=\"30\" onBlur=\"check_user_name(this.value)\">&nbsp;<span id=\"user_name_msg\"></span>\r\n        </td>\r\n    </tr>\r\n";

if ($LOGIN_USE_DOMAIN == "1") {
	echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
	echo _("绑定域用户：");
	echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"DOMAIN_USER\" class=\"BigInput\" size=\"10\" maxlength=\"30\">\r\n        </td>\r\n    </tr>\r\n";
}

if ($secu == 0) {
	$priv_count = 0;
	$priv_select_str = "";

	if ($_SESSION["LOGIN_USER_PRIV"] != "1") {
		$query = "SELECT * from USER_PRIV where PRIV_NO>'$PRIV_NO' and USER_PRIV!=1 order by PRIV_NO desc";
	}
	else {
		$query = "SELECT * from USER_PRIV order by PRIV_NO desc";
	}

	$cursor = exequery(TD::conn(), $query);

	while ($ROW = mysql_fetch_array($cursor)) {
		$USER_PRIV = $ROW["USER_PRIV"];
		$PRIV_NAME = $ROW["PRIV_NAME"];

		if ($priv_count == 0) {
			$USER_PRIV2 = $ROW["USER_PRIV"];
			$PRIV_NAME2 = $ROW["PRIV_NAME"];
		}

		$priv_select_str .= "<option value=\"" . $USER_PRIV . "\">" . $PRIV_NAME . "</option>";
		$priv_count++;
	}

	echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
	echo _("主角色：");
	echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <span style=\"position: relative;\">    \r\n                <input type=\"hidden\" name=\"USER_PRIV\" id=\"USER_PRIV0\" value=\"\">\r\n                <input type=\"text\" name=\"PRIV_NAME0\" class=\"BigInput\" value=\"\" style=\"width: 230px;\" readonly>\r\n                <a href=\"javascript:;\" class=\"orgAdd\" name=\"orgAdd\" onClick=\"SelectPrivSingle('','USER_PRIV','PRIV_NAME0','1','','33')\">";
	echo _("添加");
	echo "</a>\r\n                <a href=\"javascript:;\" class=\"orgClear\" name=\"orgClear\" onClick=\"ClearUser('USER_PRIV', 'PRIV_NAME0')\">";
	echo _("清空");
	echo "</a>\r\n            </span>&nbsp;&nbsp;\r\n            <a href=\"javascript:select_priv()\">";
	echo _("指定辅助角色");
	echo "</a>\r\n        </td>\r\n    </tr>\r\n    <tr id=\"priv\" style=\"display:none;\">\r\n        <td nowrap class=\"TableData\">";
	echo _("辅助角色：");
	echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"PRIV_ID\" id=\"PRIV_ID\" value=\"";
	echo $USER_PRIV_OTHER;
	echo "\">\r\n            <textarea cols=30 name=\"PRIV_NAME\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly>";
	echo $USER_PRIV_OTHER_NAME;
	echo "</textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" name=\"orgAdd\" onClick=\"SelectPriv('','','','1','','33')\">";
	echo _("添加");
	echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" name=\"orgClear\" onClick=\"ClearUser('PRIV_ID', 'PRIV_NAME')\">";
	echo _("清空");
	echo "</a>\r\n            <br>";
	echo _("辅助角色仅用于扩展主角色的模块权限");
	echo "        </td>\r\n    </tr>\r\n\r\n";

	if ($_SESSION["MYOA_IS_GROUP"] == "1") {
		echo "\r\n    <tr id=\"org_manage\" style=\"display: none;\">\r\n        <td nowrap class=\"TableData\">";
		echo _("指定机构：");
		echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"USER_MANAGE_ORGS\" id=\"USER_MANAGE_ORGS\" value=\"";
		echo $USER_MANAGE_ORGS;
		echo "\">\r\n            <textarea cols=30 name=\"USER_MANAGE_ORGS_NAME\" id=\"USER_MANAGE_ORGS_NAME\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly>";
		echo $USER_MANAGE_ORGS_NAME;
		echo "</textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" onClick=\"SelectOrg('2','USER_MANAGE_ORGS','USER_MANAGE_ORGS_NAME','1')\">";
		echo _("选择");
		echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" onClick=\"ClearUser('USER_MANAGE_ORGS','USER_MANAGE_ORGS_NAME')\">";
		echo _("清空");
		echo "</a>\r\n        </td>\r\n    </tr>\r\n";
	}
}
else {
	if (($secu == 1) && (get_secure_priv("sys_user_edit") == 1)) {
		echo "    <input type=\"hidden\" name=\"USER_PRIV\" value=\"";
		echo $secu_user_priv;
		echo "\">\r\n";
	}
}

echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("部门：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"DEPT_ID\" class=\"BigSelect\">\r\n";

if ($_SESSION["MYOA_IS_GROUP"] == "1") {
	if ($_SESSION["LOGIN_USER_PRIV_TYPE"] == "1") {
		echo my_dept_tree(0, $DEPT_ID, 1);
	}
	else if ($_SESSION["LOGIN_USER_PRIV_TYPE"] == "2") {
		$query = "SELECT USER_MANAGE_ORGS FROM user WHERE UID='" . $_SESSION["LOGIN_UID"] . "'";
		$cursor = exequery(TD::conn(), $query);

		if ($ROW = mysql_fetch_array($cursor)) {
			$ORG_IDS = rtrim($ROW["USER_MANAGE_ORGS"], ",");
		}

		if ($ORG_IDS) {
			$ORG_ID = explode(",", $ORG_IDS);
			$count = 0;

			while ($ORG_IDS[$count]) {
				echo my_dept_tree($ORG_IDS[$count], $DEPT_ID, 1);
				$count++;
			}
		}
	}
}
else {
	echo my_dept_tree(0, $DEPT_ID, 1);
}

if ($POST_PRIV == "1") {
	echo "                <option value=\"0\" ";

	if ($DEPT_ID == 0) {
		echo "selected";
	}

	echo ">";
	echo _("离职人员/外部人员");
	echo "</option>\r\n";
}

echo "            </select>&nbsp;&nbsp;\r\n            <a href=\"javascript:select_dept_other()\">";
echo _("指定其它所属部门");
echo "</a>\r\n";

if ($DEPT_ID == 0) {
	echo "<br>" . _("如设置为离职人员/外部人员，将对其他用户不可见");
}

echo "        </td>\r\n    </tr>\r\n    <tr id=\"dept_other\" style=\"display:none;\">\r\n        <td nowrap class=\"TableData\">";
echo _("所属部门：");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"DEPT_ID_OTHER\" value=\"";
echo $DEPT_ID_OTHER;
echo "\">\r\n            <textarea cols=30 name=\"DEPT_NAME_OTHER\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly>";
echo $USER_PRIV_OTHER_NAME;
echo "</textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" onClick=\"SelectDept('2','DEPT_ID_OTHER','DEPT_NAME_OTHER','1')\">";
echo _("选择");
echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" onClick=\"ClearUser('DEPT_ID_OTHER','DEPT_NAME_OTHER')\">";
echo _("清空");
echo "</a>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("用户排序号：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"USER_NO\" class=\"BigInput\" size=\"10\"  value=\"";
echo $USER_NO;
echo "\" onBlur=\"check_userNo();\">&nbsp;\r\n            ";
echo _("用于同角色用户的排序，不能是大于65535的数字");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableHeader\" colspan=\"2\"><img src=\"";
echo MYOA_STATIC_SERVER;
echo "/static/images/green_arrow.gif\" align=\"absMiddle\"> ";
echo _("用户权限信息");
echo "</td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\" width=\"120\">";
echo _("管理范围：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"POST_PRIV\" class=\"BigSelect\" OnChange=\"select_dept()\">\r\n                <option value=\"0\">";
echo _("本部门");
echo "</option>\r\n";

if ($POST_PRIV == "1") {
	echo "                <option value=\"1\">";
	echo _("全体");
	echo "</option>\r\n                <option value=\"2\">";
	echo _("指定部门");
	echo "</option>\r\n";
	$ORG_COUNT = getorgnum();

	if (0 < $ORG_COUNT) {
		if (getorgidbydeptid($DEPT_ID) != 0) {
			echo "    \r\n                <option value=\"6\" ";

			if ($POST_PRIV1 == "6") {
				echo "selected";
			}

			echo ">";
			echo _("本机构");
			echo "</option>\r\n";
		}
	}
}
else if ($POST_PRIV == "2") {
	echo "                <option value=\"2\">";
	echo _("指定部门");
	echo "</option>\r\n";
}

echo "            </select>\r\n            ";
echo _("在管理型模块中起约束作用");
echo "        </td>\r\n    </tr>\r\n";

if ($POST_PRIV != "0") {
	echo "    <tr id=\"dept\" style=\"display:none;\">\r\n        <td nowrap class=\"TableData\">";
	echo _("管理范围（部门）：");
	echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"TO_ID\" value=\"";
	echo $TO_ID;
	echo "\">\r\n            <textarea cols=30 name=\"TO_NAME\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly>";
	echo $TO_NAME;
	echo "</textarea>&nbsp;\r\n            <input type=\"button\" value=\"";
	echo _("选择");
	echo "\" class=\"SmallButton\" onClick=\"SelectDept('','','','1')\" title=\"";
	echo _("选择部门");
	echo "\" name=\"button\">&nbsp;\r\n            <input type=\"button\" value=\"";
	echo _("清空");
	echo "\" class=\"SmallButton\" onClick=\"ClearUser()\" title=\"";
	echo _("清空部门");
	echo "\" name=\"button\">\r\n        </td>\r\n    </tr>\r\n";
}

if (check_email_audit(3)) {
	echo "    <tr>\r\n        <td nowrap class=\"TableData\" width=\"120\">";
	echo _("密级：");
	echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"SECRET_LEVEL\" class=\"BigSelect\">\r\n                <option value=\"\" >";
	echo _("--请选择--");
	echo "</option>\r\n";

	foreach ($email_audit_level_config as $level_key => $level_option ) {
		$level_selected = "";

		if ($level_key == $secret_level) {
			$level_selected = " selected ";
		}

		echo "                <option value=\"";
		echo $level_key;
		echo "\" ";
		echo $level_selected;
		echo ">";
		echo $level_option;
		echo "</option>\r\n";
	}

	echo "            </select>\r\n        </td>\r\n    </tr>\r\n";
}

echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("访问控制 ：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"checkbox\" name=\"NOT_VIEW_USER\" id=\"NOT_VIEW_USER\" ";

if ($NOT_VIEW_USER) {
	echo "checked";
}

echo "><label for=\"NOT_VIEW_USER\">";
echo _("禁止查看用户列表");
echo "</label>&nbsp;\r\n            <input type=\"checkbox\" name=\"NOT_VIEW_TABLE\" id=\"NOT_VIEW_TABLE\" ";

if ($NOT_VIEW_TABLE) {
	echo "checked";
}

echo "><label for=\"NOT_VIEW_TABLE\">";
echo _("禁止显示桌面");
echo "</label>\r\n            <input type=\"checkbox\" name=\"USEING_KEY\" id=\"USEING_KEY\"   ";

if ($LOGIN_KEY == 0) {
	echo "disabled";
}

echo "><label for=\"USEING_KEY\">";
echo _("使用USB KEY登录");
echo "</label>\r\n            <input type=\"checkbox\" name=\"USING_FINGER\" id=\"USING_FINGER\" ";

if ($USING_FINGER) {
	echo "checked";
}

echo "><label for=\"USING_FINGER\">";
echo _("使用指纹验证");
echo "</label><br/>\r\n";

if ($secu == 0) {
	echo "            <input type=\"radio\" name=\"NOT_LOGIN\" id=\"NOT_LOGIN_0\" value=\"0\" ";

	if ($NOT_LOGIN == 0) {
		echo "checked";
	}

	echo "><label for=\"NOT_LOGIN_0\">";
	echo _("允许登录OA系统");
	echo "</label>\r\n            <input type=\"radio\" name=\"NOT_LOGIN\" id=\"NOT_LOGIN_1\" value=\"1\" ";

	if ($NOT_LOGIN == 1) {
		echo "checked";
	}

	echo "><label for=\"NOT_LOGIN_1\">";
	echo _("禁止登录OA系统");
	echo "</label>\r\n            <input type=\"radio\" name=\"NOT_MOBILE_LOGIN\" id=\"NOT_MOBILE_LOGIN_0\" value=\"0\" ";

	if ($NOT_MOBILE_LOGIN == 0) {
		echo "checked";
	}

	echo "><label for=\"NOT_MOBILE_LOGIN_0\">";
	echo _("允许登录手机客户端（不受OA用户数限制）");
	echo "</label>\r\n            <input type=\"radio\" name=\"NOT_MOBILE_LOGIN\" id=\"NOT_MOBILE_LOGIN_1\" value=\"1\" ";

	if ($NOT_MOBILE_LOGIN == 1) {
		echo "checked";
	}

	echo "><label for=\"NOT_MOBILE_LOGIN_1\">";
	echo _("禁止登录手机客户端");
	echo "</label>\r\n";
}

echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\" width=\"120\">";
echo _("即时通讯使用权限：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"IM_RANGE\" class=\"BigSelect\">\r\n                <option value=\"1\" >";
echo _("允许使用");
echo "</option>\r\n                <option value=\"2\" >";
echo _("禁止使用");
echo "</option>\r\n            </select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("通讯白名单：");
echo "<br>";
echo _("按角色设置");
echo "<br><a href=\"javascript:;\" id=\"href_txt\" onClick=\"changeRange();\">";
echo _("按人员设置");
echo "</a></td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"PRIV_ID1\" value=\"\">\r\n            <textarea cols=40 name=\"PRIV_NAME1\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly></textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" onClick=\"SelectPriv('','PRIV_ID1','PRIV_NAME1','1','','33')\">";
echo _("添加");
echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" onClick=\"ClearUser('PRIV_ID1', 'PRIV_NAME1')\">";
echo _("清空");
echo "</a><br>\r\n            ";
echo _("属于以上所选角色的人员可以给此用户发送邮件和微讯，");
echo "<b>";
echo _("角色和人员设置均为空则不限制");
echo "</b>\r\n        </td>\r\n    </tr>\r\n    <tr id=\"rang_user\" style=\"display:none\">\r\n        <td nowrap class=\"TableData\">";
echo _("通讯白名单：");
echo "<br>";
echo _("按人员设置");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"USER_ID1\" value=\"\">\r\n            <textarea cols=40 name=\"USER_NAME1\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly></textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" onClick=\"SelectUser('','5','USER_ID1', 'USER_NAME1')\">";
echo _("添加");
echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" onClick=\"ClearUser('USER_ID1', 'USER_NAME1')\">";
echo _("清空");
echo "</a><br>\r\n            ";
echo _("属于以上所选人员可以给此用户发送邮件和微讯，");
echo "<b>";
echo _("角色和人员设置均为空则不限制");
echo "</b>\r\n        </td>\r\n    </tr>\r\n    <tr id=\"rang_dept\" style=\"display:none\">\r\n        <td nowrap class=\"TableData\">";
echo _("通讯白名单：");
echo "<br>";
echo _("按部门设置");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"hidden\" name=\"DEPT_ID_MSG\" value=\"\">\r\n            <textarea cols=40 name=\"DEPT_NAME_MSG\" rows=2 class=\"BigStatic\" wrap=\"yes\" readonly></textarea>\r\n            <a href=\"javascript:;\" class=\"orgAdd\" onClick=\"SelectDept('','DEPT_ID_MSG', 'DEPT_NAME_MSG','1')\">";
echo _("添加");
echo "</a>\r\n            <a href=\"javascript:;\" class=\"orgClear\" onClick=\"ClearUser('DEPT_ID_MSG', 'DEPT_NAME_MSG')\">";
echo _("清空");
echo "</a><br>\r\n            ";
echo _("属于以上所选部门可以给此用户发送邮件和微讯，");
echo "<b>";
echo _("角色、人员和部门设置均为空则不限制");
echo "</b>\r\n        </td>\r\n    </tr>\r\n    <tr onClick=\"if(option1.style.display=='none') option1.style.display=''; else option1.style.display='none';\" title=\"";
echo _("点击展开/收缩选项");
echo "\">\r\n        <td nowrap class=\"TableHeader\" colspan=\"2\" style=\"cursor:pointer;\"><img src=\"";
echo MYOA_STATIC_SERVER;
echo "/static/images/green_arrow.gif\" align=\"absMiddle\"> ";
echo _("其它选项");
echo "</td>\r\n    </tr>\r\n    <tbody id=\"option1\" style=\"display:none;\">\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("考勤排班类型：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"DUTY_TYPE\" class=\"BigSelect\">\r\n";
$query = "SELECT * from ATTEND_CONFIG order by DUTY_TYPE";
$cursor = exequery(TD::conn(), $query);

while ($ROW = mysql_fetch_array($cursor)) {
	$DUTY_TYPE = $ROW["DUTY_TYPE"];
	$DUTY_NAME = $ROW["DUTY_NAME"];
	echo "                <option value=\"";
	echo $DUTY_TYPE;
	echo "\">";
	echo $DUTY_NAME;
	echo "</option>\r\n";
}

echo "                <option value=\"99\">";
echo _("轮班制");
echo "</option>\r\n            </select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("内部邮箱容量：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"EMAIL_CAPACITY\" class=\"BigInput\" size=\"5\" maxlength=\"11\" value=\"500\">&nbsp;MB\r\n            ";
echo _("为空则表示不限制大小");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("个人文件柜容量：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"FOLDER_CAPACITY\" class=\"BigInput\" size=\"5\" maxlength=\"11\" value=\"500\">&nbsp;MB\r\n            ";
echo _("为空则表示不限制大小");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("是否启用POP3功能：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"checkbox\" name=\"USE_POP3\" id=\"USE_POP3\" ><label for=\"USE_POP3\">";
echo _("是");
echo "</label>\r\n        </td>\r\n    </tr>\r\n    ";

if ($USER_ID != "admin") {
	echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
	echo _("是否启用邮件发送限制：");
	echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"checkbox\" name=\"USE_EMAIL\" id=\"USE_EMAIL\"  ";

	if ($USE_EMAIL == 1) {
		echo "checked";
	}

	echo " ><label for=\"USE_EMAIL\">";
	echo _("是");
	echo "</label>&nbsp;开启后将会限制内部收件人数一次最多20人\r\n        </td>\r\n    </tr>\r\n    ";
}

echo "    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("禁用Internet邮箱：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"checkbox\" name=\"IS_WEBMAIL\" id=\"IS_WEBMAIL\" onClick=\"change_type()\"><label for=\"IS_WEBMAIL\">";
echo _("禁止使用Internet邮件功能");
echo "</label>\r\n        </td>\r\n    </tr>\r\n    <tr id=\"internet1\">\r\n        <td nowrap class=\"TableData\">Internet";
echo _("邮箱数量：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"WEBMAIL_NUM\" class=\"BigInput\" size=\"5\" maxlength=\"11\" value=\"\">&nbsp;";
echo _("个");
echo "            ";
echo _("为空则表示不限制数量");
echo "        </td>\r\n    </tr>\r\n    <tr id=\"internet2\">\r\n        <td nowrap class=\"TableData\">";
echo _("每个Internet邮箱容量：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"WEBMAIL_CAPACITY\" class=\"BigInput\" size=\"5\" maxlength=\"11\" value=\"\">&nbsp;MB\r\n            ";
echo _("为空则表示不限制大小");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("绑定IP地址：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <textarea name=\"BIND_IP\" class=\"BigInput\" cols=\"50\" rows=\"2\"></textarea><br>\r\n            ";
echo _("为空则该用户不绑定固定的IP地址，绑定多个IP地址用英文逗号(,)隔开");
echo "<br>";
echo _("也可以绑定IP段，如“192.168.0.60,192.168.0.100-192.168.0.200”");
echo "<br>";
echo _("表示192.168.0.60或192.168.0.100到192.168.0.200这个范围内都可以登录");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("备注：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <textarea name=\"REMARK\" class=\"BigInput\" cols=\"50\" rows=\"2\"></textarea>\r\n        </td>\r\n    </tr>\r\n";

if (file_exists("fis_acset.php")) {
	include_once ("fis_acset.php");
}

echo "    </tbody>\r\n    <tr onClick=\"if(option2.style.display=='none') option2.style.display=''; else option2.style.display='none';\" title=\"";
echo _("点击展开/收缩选项");
echo "\">\r\n        <td nowrap class=\"TableHeader\" colspan=\"2\" style=\"cursor:pointer;\"><img src=\"";
echo MYOA_STATIC_SERVER;
echo "/static/images/green_arrow.gif\" align=\"absMiddle\"> ";
echo _("用户可自定义选项");
echo "</td>\r\n    </tr>\r\n    <tbody id=\"option2\" style=\"display:none;\">\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("密码：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"password\" name=\"PASSWORD\" class=\"BigInput\" size=\"20\" maxlength=\"";
echo $SEC_PASS_MAX;
echo "\"> ";
echo $SEC_PASS_MIN;
echo "-";
echo $SEC_PASS_MAX;
echo _("位");

if ($SEC_PASS_SAFE == "1") {
	echo _("，必须同时包含字母和数字");
}

echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("性别：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <select name=\"SEX\" class=\"BigSelect\">\r\n                <option value=\"0\">";
echo _("男");
echo "</option>\r\n                <option value=\"1\">";
echo _("女");
echo "</option>\r\n            </select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("生日：");
echo "</td>\r\n        <td nowrap class=\"TableData\">\r\n            <input type=\"text\" name=\"BIRTHDAY\" size=\"10\" maxlength=\"10\" class=\"BigInput\" onClick=\"WdatePicker()\"/>&nbsp;&nbsp;\r\n            <input type=\"checkbox\" name=\"IS_LUNAR\" id=\"IS_LUNAR\"><label for=\"IS_LUNAR\">";
echo _("是农历生日");
echo "</label>     \r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\">";
echo _("界面主题：");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <select name=\"THEME\" class=\"BigSelect\">\r\n                ";
echo get_theme_list($THEME);
echo "            </select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\"> ";
echo _("手机：");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"text\" name=\"MOBIL_NO\" size=\"16\" maxlength=\"23\" class=\"BigInput\" value=\"";
echo $MOBIL_NO;
echo "\">\r\n            <input type=\"checkbox\" name=\"MOBIL_NO_HIDDEN\" id=\"MOBIL_NO_HIDDEN\"><label for=\"MOBIL_NO_HIDDEN\">";
echo _("手机号码不公开");
echo "</label><br>\r\n            ";
echo _("填写后可接收OA系统发送的手机短信，手机号码不公开仍可接收短信");
echo "        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\"> ";
echo _("电子邮件：");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"text\" name=\"EMAIL\" size=\"25\" maxlength=\"50\" class=\"BigInput\" value=\"";
echo $EMAIL;
echo "\">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td nowrap class=\"TableData\"> ";
echo _("工作电话：");
echo "</td>\r\n        <td class=\"TableData\">\r\n            <input type=\"text\" name=\"TEL_NO_DEPT\" size=\"16\" maxlength=\"23\" class=\"BigInput\" value=\"";
echo $TEL_NO_DEPT;
echo "\">\r\n        </td>\r\n    </tr>\r\n   </tbody>\r\n    <tr>\r\n        <td nowrap  class=\"TableControl\" colspan=\"2\" align=\"center\">\r\n            <input type=\"hidden\" value=\"\" name=\"USER_PRIV_TYPE\" id=\"USER_PRIV_TYPE\">\r\n            <input type=\"submit\" value=\"";
echo _("新建");
echo "\" class=\"BigButton\" title=\"";
echo _("新建用户");
echo "\" name=\"button\">&nbsp;&nbsp;\r\n            <input type=\"button\" value=\"";
echo _("关闭");
echo "\" class=\"BigButton\" title=\"";
echo _("关闭窗口");
echo "\" onClick=\"window.close();\">\r\n        </td>\r\n    </tr>\r\n</table>\r\n</form>\r\n</body>\r\n</html>";

?>
