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
