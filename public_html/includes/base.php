<?php
/*
 *  This file is part of Anontune.
 *
 *  Anontune is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Anontune is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Anontune.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *  (c) 2011 Anontune developers
 * 
 */
 
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

define("ANONTUNE_API_ERROR",	101);
define("ANONTUNE_API_WARNING",	102);
define("ANONTUNE_API_SUCCESS",	103);

if(empty($_SESSION['prefered_locale']))
{
	$_SESSION['prefered_locale'] = "english";
}

$locale->Load($_SESSION['prefered_locale']);

?>
