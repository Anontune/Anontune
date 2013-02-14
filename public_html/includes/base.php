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
	//echo "<script>alert(document.cookie);</script>";
	$_SESSION['auth_password'] = $password;
	$_SESSION['auth_username'] = $username;
	$_SESSION['username']      = $username;
}

if(empty($_SESSION['prefered_locale']))
{
	$_SESSION['prefered_locale'] = "english";
}

$locale->Load($_SESSION['prefered_locale']);
