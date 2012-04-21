<?php
$sUsername = mysql_real_escape_string($router->uParameters[1]);
$result = mysql_query_cached("SELECT * FROM user WHERE `username` = '{$sUsername}'");

if($result)
{
	$render_template = false;
	$_GET['username'] = $router->uParameters[1];
	include("user_page.php");
}
else
{
	$sErrorCode = 404;
}
?>
