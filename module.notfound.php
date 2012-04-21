<?php
if($_ANONTUNE !== true) { die(); }

$sParameter = mysql_real_escape_string($router->uParameters[1]);

if($result = mysql_query_cached("SELECT * FROM user WHERE `username` = '{$sParameter}'"))
{
	header("Location: /user/{$router->uParameters[1]}/");
	die();
}

$sErrorCode = 404;
?>
