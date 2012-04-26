<?php
require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "../global.php");

$username = isset($_GET["username"]) ? htmlspecialchars(double_quote_escape(urldecode($_GET["username"]))) : "";
$auth_username = isset($_COOKIE["auth_username"]) ? htmlspecialchars(double_quote_escape($_COOKIE["auth_username"])) : "";
$auth_password = isset($_COOKIE["auth_password"]) ? htmlspecialchars(double_quote_escape($_COOKIE["auth_password"])) : "";
$ip_address = $_SERVER['REMOTE_ADDR'];

header('Content-type: text/javascript');

echo("
var var_username = '{$username}';
var var_auth_username = '{$auth_username}';
var var_auth_password = '{$auth_password}';
var var_api_url = '{$api_url}';
var var_ip_address = '{$ip_address}';
var var_this_root_url = '{$this_root_url}';
var var_image_path = '/player/images/';
");
?>
