<?php
if($_ANONTUNE !== true) { die(); }

$recaptcha_publickey = "6LcVTs4SAAAAACpv7qr0TzAOGr1co613qR2iI900";
$recaptcha_privatekey = "6LcVTs4SAAAAAD3MCA1Mm9n1Tjlj-PAzFvLipx2c";

$_CPHP = true;
require("cphp/base.php");
require("recaptchalib.php");

require("classes/class.user.php");
require("classes/class.page.php");

function login($username, $password)
{
    setcookie("auth_username", $username, time() + ((3600 * 24) * 365), "/");
    setcookie("auth_password", $password, time() + ((3600 * 24) * 365), "/");
}

?>
