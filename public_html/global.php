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

require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "config.php");
//require("include/mysql_con.php");
require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "include/function.php");
$action = empty($_GET["action"]) ? "" : $_GET["action"];

$domain = explode(".", $_SERVER['HTTP_HOST']);
$domain = "." . $domain[count($domain) - 2] . "." . $domain[count($domain) - 1];
session_name('ANONTUNE');
session_set_cookie_params(time() + (2 * 24 * 60 * 60), '/', $domain, false, false);
session_start();
