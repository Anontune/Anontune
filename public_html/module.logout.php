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

setcookie("auth_username", "",  time() - ((3600 * 24) * 370), "/");
setcookie("auth_password", "", time() - ((3600 * 24) * 370), "/");

$_SESSION['user_id'] = 0;

$sPageContents = "<h2>{$locale->strings['logout-header']}</h2>";

$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['logout-title'], $locale->strings['logout-message']);
$sPageContents .= $err->Render();

$sPageTitle = $locale->strings['title-logout'];
?>
