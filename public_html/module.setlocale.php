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

$render_template = false;

$new_locale = $router->uParameters[1];

switch($new_locale)
{
	case "french":
		$_SESSION['prefered_locale'] = "french";
		break;
	case "dutch":
		$_SESSION['prefered_locale'] = "dutch";
		break;
	case "danish":
		$_SESSION['prefered_locale'] = "danish";
		break;
	case "spanish":
		$_SESSION['prefered_locale'] = "spanish";
		break;
	case "bulgarian":
		$_SESSION['prefered_locale'] = "bulgarian";
		break;
	default:
		$_SESSION['prefered_locale'] = "english";
		break;
}

header("Location: /");
die();
?>
