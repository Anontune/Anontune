<?php

/*
Module used to pass server-side variables to Javascript.
*/

//echo $_SESSION["auth_username"];
//echo $_SESSION["auth_password"];


require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "../global.php");
require_once("../include/mysql_con.php");
require_once("../include/function.php");
require_once("../include/auth_token.php");

session_start();
//echo $_SESSION["auth_username"];
//echo $_SESSION["auth_password"];

$username = isset($_GET["username"]) ? htmlspecialchars(double_quote_escape(urldecode($_GET["username"]))) : "";
$auth_username = isset($_SESSION["auth_username"]) ? htmlspecialchars(double_quote_escape($_SESSION["auth_username"])) : "";
$auth_password = "";
if(isset($_SESSION["auth_password"]))
{
	if(!empty($_SESSION["auth_password"]))
	{
		$auth_password = "present";
	}
}
//$auth_password = isset($_SESSION["auth_password"]) ? htmlspecialchars(double_quote_escape($_SESSION["auth_password"])) : "";
$ip_address = $_SERVER['REMOTE_ADDR'];

$options = "";
$options["ip_addr"] = "this";
$options["expiry"] = 2 * 24 * 60 * 60; //2 days.
//$options["session_id"] = "this";
$options["user_agent"] = "this";
$options["uses"] = "1";
$options["referer"] = "this";
//$options["cookie_name"] = "abc";
//$options["cookie_value"] = "this";
$options["auth_username"] = $_SESSION["auth_username"];
$options["auth_password"] = $_SESSION["auth_password"];
$auth_token = create_token($options);
//die("xxx");
/*
Todo: Don't generate new token if old one is still usable.
I suppose it should be done based on session ID.
*/

header('Content-type: text/javascript');

/*
TODO: Fix this shit
*/

echo("
var var_auth_token = '{$auth_token}';
var var_username = '{$username}';
var var_auth_username = '{$auth_username}';
var var_auth_password = '{$auth_password}';
var var_api_url = '{$api_url}';
var var_ip_address = '{$ip_address}';
var var_this_root_url = '{$this_root_url}';
var var_image_path = '/player/images/';
");
?>
