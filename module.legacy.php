<?php
if($_ANONTUNE !== true) { die(); }

if($router->uParameters[1] == "at-login-2" || $router->uParameters[1] == "login_register.php")
{
	$new_location = "/login/";
}
elseif($router->uParameters[1] == "at-register")
{
	$new_location = "/register/";
}
elseif($router->uParameters[1] == "import-ipod")
{
	$new_location = "/tools/ipod/";
}
elseif($router->uParameters[1] == "logout")
{
	$new_location = "/logout/";
}
else
{
	$new_location = "/";
}

header("Location: {$new_location}");
die();
?>
