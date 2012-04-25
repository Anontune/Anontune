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
 */
 
$_ANONTUNE = true;
$root_dir = "../";

require("../includes/base.php");

$query = $_SERVER['REQUEST_URI'];
list($empty, $version, $path) = explode("/", $query, 3);

$sStatus = ANONTUNE_API_ERROR;
$sErrorMessage = "No matching actions found.";
$sData = array();

if(!empty($_GET['reference']))
{
	$reference = $_GET['reference'];
}
elseif(!empty($_POST['reference']))
{
	$reference = $_POST['reference'];
}
else
{
	$reference = "";
}

$request_type = ($_SERVER['REQUEST_METHOD'] == "POST") ? "post" : "get";
$datasource = ($request_type == "post") ? $_POST : $_GET;

$api_public = $datasource['pubkey'];
$api_private = $datasource['privkey'];
$format = isset($_GET['format']) ? $_GET['format'] : "jsonp";

if(!empty($version))
{
	try
	{
		$router = new CPHPRouter();
		
		$router->custom_query = $path;
		$router->allow_slash = true;
		$router->ignore_query = true;
		$router->routes = array(
			0 => array(
				'^crossdomain/?$'				=> "api.crossdomain.php",
				'^artist/([0-9]+)/?$'			=> "api.get.artist.php",
				'^album/([0-9]+)/?$'			=> "api.get.album.php",
				'^track/([0-9]+)/?$'			=> "api.get.track.php",
				'^playlist/([0-9]+)/?$'			=> "api.get.playlist.php",
				'^playlist/item/([0-9]+)/?$'	=> "api.get.playlist.item.php"
			)
		);
		
		$router->RouteRequest();
	}
	catch (NotFoundException $e)
	{
		$sStatus = ANONTUNE_API_ERROR;
		$sErrorMessage = "The specified item was not found.";
	}
}
else
{
	$sStatus = ANONTUNE_API_ERROR;
	$sErrorMessage = "No API version specified.";
}

if($sStatus == ANONTUNE_API_ERROR)
{
	/* An error occurred and no data is being returned. */
	$sReturnObject = array(
		'status'	=> "error",
		'message'	=> $sErrorMessage,
		'data'		=> array()
	);
}
elseif($sStatus == ANONTUNE_API_WARNING)
{
	/* An issue occurred, but data is still sent. */
	$sReturnObject = array(
		'status'	=> "warning",
		'message'	=> $sErrorMessage,
		'data'		=> $sData
	);
}
elseif($sStatus == ANONTUNE_API_SUCCESS)
{
	/* All went as expected, data is returned. */
	$sReturnObject = array(
		'status'	=> "success",
		'message'	=> "",
		'data'		=> $sData
	);
}
else
{
	// Umm, yeah, this is never supposed to happen. If this happens,
	// you suck and you didn't use the API constants.
	die("PANIC!");
}

$sJsonObject = json_encode($sReturnObject);

if($format == "jsonp")
{
	echo("run_callback(\"{$reference}\", {$sJsonObject});");
}
elseif($format == "json")
{
	echo($sJsonObject);
}
?>
