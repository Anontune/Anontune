<?php
if($_ANONTUNE !== true) { die(); }

setcookie("auth_username", "",  time() - ((3600 * 24) * 370), "/");
setcookie("auth_password", "", time() - ((3600 * 24) * 370), "/");

$sPageContents = "<h2>{$locale->strings['logout-header']}</h2>";

$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['logout-title'], $locale->strings['logout-message']);
$sPageContents .= $err->Render();

$sPageTitle = $locale->strings['title-logout'];
?>
