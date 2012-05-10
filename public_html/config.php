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

$not_set = "v0id";
$xmlstr = @file_get_contents("/etc/anontune/config.xml");
$config = new SimpleXMLElement($xmlstr);

$script_home_path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "..");
$this_root_url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$this_root_url = substr($this_root_url, 0, strrpos($this_root_url,'/'));
$this_root_url = "http://" . $this_root_url;
$len = strlen($this_root_url);
if($this_root_url[$len - 1] == "/" or $this_root_url[$len - 1] == "\\")
{
	$this_root_url = substr($this_root_url, 0, $len - 1); 
}
//echo $this_root_url;$config->database->name
$url_root_part = $_SERVER['HTTP_HOST'] . $config->anontune->url_root_part;
if($url_root_part[strlen($url_root_part) - 1] != "/")
{
	$url_root_part .= "/";   
}
$root_url = "http://" . $url_root_part;
$api_url = $root_url . "api.php";
