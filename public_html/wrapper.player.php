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
