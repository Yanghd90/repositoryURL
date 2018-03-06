<?php

function add_user($USER_ARRAY)
{
	$USER_KEY_STR = "";
	$USER_KEY_VALUE = "";
	foreach ($USER_ARRAY as $key => $value ) {
		if ($key == "NOT_LOGIN") {
			$NOT_LOGIN = $value;
			// 2017-6-14 17:17:20 Yhd下一行代码造成新增的用户都不能登录
			// $value = 1;
		}

		if ($key == "DEPT_ID") {
			$DEPT_ID = $value;
			// 2017-6-14 17:17:20 Yhd下一行代码造成新增的用户都在离职这个部门下边，不能保存为选择的部门，所以注释掉
			// $value = 0;
		}

		$USER_KEY_STR .= $key . ",";
		$USER_KEY_VALUE .= "'" . $value . "',";

		if ($key == "USER_ID") {
			$USER_ID = $value;
		}
	}

	$USER_KEY_STR = td_trim($USER_KEY_STR);
	$USER_KEY_VALUE = td_trim($USER_KEY_VALUE);
	$query = "insert into USER ($USER_KEY_STR) values($USER_KEY_VALUE)";
	// echo $query;exit;
	exequery(TD::conn(), $query);
	$UID = mysql_insert_id();
	// 2017-2-28 20:50:37
	// $UID = $USER_ARRAY['BYNAME'];
	$BYNAME = $USER_ARRAY['BYNAME'];
	// echo $BYNAME;exit;
	$query = "UPDATE user SET USER_ID='$BYNAME' WHERE UID='$UID'";
	exequery(TD::conn(), $query);
	$SYS_PARA_ARRAY = get_sys_para("DINGDING_CORPID,DINGDING_SECRET,WEIXINQY_CORPID,WEIXINQY_SECRET");
	$DINGDING_CORPID = $SYS_PARA_ARRAY["DINGDING_CORPID"];
	$DINGDING_SECRET = $SYS_PARA_ARRAY["DINGDING_SECRET"];
	$WEIXINQY_CORPID = $SYS_PARA_ARRAY["WEIXINQY_CORPID"];
	$WEIXINQY_SECRET = $SYS_PARA_ARRAY["WEIXINQY_SECRET"];
	if (($DINGDING_CORPID != "") && ($DINGDING_SECRET != "")) {
		$sync_info = array("qy_type" => "dd,", "type" => "create_user", "dd_user_id" => $UID);
		sync_oa2qy($sync_info);
	}

	if (($WEIXINQY_CORPID != "") && ($WEIXINQY_SECRET != "")) {
		$sync_info = array("qy_type" => "wx,", "type" => "create_user", "wx_user_id" => $UID);
		sync_oa2qy($sync_info);
	}

	if ($NOT_LOGIN_SIGN != 1) {
		if (!file_exists(MYOA_ATTACH_PATH . "new_sms/" . $UID . ".sms")) {
			new_sms_remind($UID, 0);
		}
	}

	set_uid_menu_priv($UID, $UID, $USER_ARRAY["USER_PRIV"] . "," . $USER_ARRAY["USER_PRIV_OTHER"]);
	add_log(6, $UID, $_SESSION["LOGIN_USER_ID"]);
	return $UID;
}

function set_user($USER_ARRAY, $USER_ID, $UID)
{
	if (!is_array($USER_ARRAY) || (sizeof($USER_ARRAY) < 1) || ($USER_ID == "")) {
		return NULL;
	}

	$SQL = "";

	foreach ($USER_ARRAY as $key => $value ) {
		if ($key == "NOT_LOGIN") {
			$NOT_LOGIN = $value;
			$value = 1;
		}

		if ($key == "DEPT_ID") {
			$DEPT_ID = $value;
			$value = 0;
		}

		$SQL .= $key . "='" . $value . "',";
		if (($key == "NOT_LOGIN") && ($value == 1)) {
			$NOT_LOGIN_SIGN = 1;
		}
		else {
			$NOT_LOGIN_SIGN = 0;
		}
	}

	$SQL = td_trim($SQL);
	$query = "update USER set " . $SQL . " where UID='$UID'";
	exequery(TD::conn(), $query);

	if ($NOT_LOGIN_SIGN != 1) {
		if (!file_exists(MYOA_ATTACH_PATH . "new_sms/" . $UID . ".sms")) {
			new_sms_remind($UID, 0);
		}
	}

	$SYS_PARA_ARRAY = get_sys_para("DINGDING_CORPID,DINGDING_SECRET,WEIXINQY_CORPID,WEIXINQY_SECRET");
	$DINGDING_CORPID = $SYS_PARA_ARRAY["DINGDING_CORPID"];
	$DINGDING_SECRET = $SYS_PARA_ARRAY["DINGDING_SECRET"];
	$WEIXINQY_CORPID = $SYS_PARA_ARRAY["WEIXINQY_CORPID"];
	$WEIXINQY_SECRET = $SYS_PARA_ARRAY["WEIXINQY_SECRET"];
	if (($DINGDING_CORPID != "") && ($DINGDING_SECRET != "")) {
		$open_id = "";
		$query = "select * from user_dingding where user_id='$USER_ID'";
		$cursor = exequery(TD::conn(), $query);

		if ($row = mysql_fetch_array($cursor)) {
			$open_id = $row["open_id"];
		}

		$sync_info = array("qy_type" => "dd,", "type" => $open_id ? "update_user" : "create_user", "dd_user_id" => $USER_ID);
		sync_oa2qy($sync_info);
	}

	if (($WEIXINQY_CORPID != "") && ($WEIXINQY_SECRET != "")) {
		$open_id = "";
		$query = "select * from user_weixinqy where user_id='$USER_ID'";
		$cursor = exequery(TD::conn(), $query);

		if ($row = mysql_fetch_array($cursor)) {
			$open_id = $row["open_id"];
		}

		$sync_info = array("qy_type" => "wx,", "type" => $open_id ? "update_user" : "create_user", "wx_user_id" => $USER_ID);
		sync_oa2qy($sync_info);
	}

	set_uid_menu_priv($UID, $USER_ID, $USER_ARRAY["USER_PRIV"] . "," . $USER_ARRAY["USER_PRIV_OTHER"]);
	add_log(7, $USER_ID, $_SESSION["LOGIN_USER_ID"]);
}

function bin2guid($bin)
{
	$hex_guid = bin2hex($bin);
	$hex_guid_to_guid_str = "";

	for ($k = 1; $k <= 4; ++$k) {
		$hex_guid_to_guid_str .= substr($hex_guid, 8 - (2 * $k), 2);
	}

	$hex_guid_to_guid_str .= "-";

	for ($k = 1; $k <= 2; ++$k) {
		$hex_guid_to_guid_str .= substr($hex_guid, 12 - (2 * $k), 2);
	}

	$hex_guid_to_guid_str .= "-";

	for ($k = 1; $k <= 2; ++$k) {
		$hex_guid_to_guid_str .= substr($hex_guid, 16 - (2 * $k), 2);
	}

	$hex_guid_to_guid_str .= "-" . substr($hex_guid, 16, 4);
	$hex_guid_to_guid_str .= "-" . substr($hex_guid, 20);
	return strtoupper($hex_guid_to_guid_str);
}

function get_org_array($folder_list, $base_dn)
{
	$org_dn_array = array();
	$org_guid_array = array();

	for ($i = 0; $i < $folder_list["count"]; $i++) {
		$dn = $folder_list[$i]["dn"];

		if (substr($dn, 0, 22) == "OU=Domain Controllers,") {
			continue;
		}

		$org_dn_array[] = iconv("utf-8", MYOA_CHARSET, $dn);
		$org_guid_array[] = bin2guid($folder_list[$i]["objectguid"][0]);
	}

	$org_dn_new_array = array();

	for ($i = 0; $i < count($org_dn_array); $i++) {
		$string = substr($org_dn_array[$i], 0, -strlen($base_dn));
		$array = get_ou_array($string);
		$array = array_reverse($array);
		$org_dn_new_array[$i] = implode(",", $array);
	}

	asort($org_dn_new_array);
	$org_array = array();
	$tmp_array = array();

	while (list($key, $value) = each($org_dn_new_array)) {
		$array = get_ou_array($value);

		for ($j = 0; $j < count($array); $j++) {
			$parent = ($j == 0 ? -1 : array_search(($j - 1) . "_" . $parent . "_" . $array[$j - 1], $tmp_array));

			if (in_array($j . "_" . $parent . "_" . $array[$j], $tmp_array)) {
				continue;
			}

			$org_array[] = array("name" => $array[$j], "level" => $j, "parent" => $parent, "islast" => 1, "line" => "", "dn" => $org_dn_array[$key], "guid" => $org_guid_array[$key]);
			$tmp_array[] = $j . "_" . $parent . "_" . $array[$j];
		}
	}

	for ($i = 0; $i < count($org_array); $i++) {
		for ($j = $i + 1; $j < count($org_array); $j++) {
			if ($org_array[$j]["level"] < $org_array[$i]["level"]) {
				break;
			}

			if ($org_array[$i]["level"] == $org_array[$j]["level"]) {
				$org_array[$i]["islast"] = 0;
				break;
			}
		}

		$org_array[$i]["line"] = ($org_array[$i]["islast"] ? "└" : "├");
		$parent = $org_array[$i]["parent"];

		while (0 <= $parent) {
			$org_array[$i]["line"] = ($org_array[$parent]["islast"] ? _("　") : "│") . $org_array[$i]["line"];
			$parent = $org_array[$parent]["parent"];
		}
	}

	return $org_array;
}

function ldap_slashes($str)
{
	return preg_replace("/([\\x00-\\x1F\*\(\)\\\\])/e", "\"\\\\\\\".join(\"\",unpack(\"H2\",\"\$1\"))", $str);
}

function get_ldap_option($config)
{
	$dc_array = explode(".", $config["DOMAIN_NAME"]);
	$base_dn = "DC=" . implode(",DC=", $dc_array);
	return array(
	"account_suffix"     => "@" . $config["DOMAIN_NAME"],
	"base_dn"            => $base_dn,
	"domain_controllers" => array($config["DOMAIN_CONTROLLERS"])
	);
}

function get_ou_array($string, $separator = ",")
{
	$array = array();
	$count = 0;

	for ($i = 0; $i < strlen($string); $i++) {
		if (($string[$i] == $separator) && ($string[$i - 1] != "\\")) {
			$count++;
			continue;
		}

		$array[$count] .= $string[$i];
	}

	for ($i = 0; $i < count($array); $i++) {
		if (stristr($array[$i], "=")) {
			$array[$i] = substr($array[$i], strpos($array[$i], "=") + 1);
		}
	}

	return $array;
}

include_once ("inc/conn.php");
include_once ("inc/utility.php");
include_once ("inc/utility_cache.php");
?>
