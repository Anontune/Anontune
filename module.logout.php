<?php
if($_ANONTUNE !== true) { die(); }

setcookie("auth_username", "",  time() - ((3600 * 24) * 370), "/");
setcookie("auth_password", "", time() - ((3600 * 24) * 370), "/");

$sPageContents = "<h2>Logged out.</h2>";

$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Successfully logged out.", "You have been logged out of AnonTune, and you are now
browsing the site as a guest.");
$sPageContents .= $err->Render();

$sPageTitle = "Logged out";
?>
