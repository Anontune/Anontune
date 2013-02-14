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
 

/*
This module provides the API for Anontune. It consists of a series of functions, each of which is probably an API call. All API calls go to this module e.g. api.php?c=apicallname&parm1=test&param2=test. A table of cases is used to handle each API call name based on the c parameter. Hence this allows for API calls to have variable names and arguments for flexibility. An API call may be privileged or unprivileged. If it's privileged it will need a way to authenticate against a user. Each user has a different access level and there is a simple access matrix for privileged API calls. At the bottom of this module is the handler part which decides which API call to make. Each API call can return api_failure in which case there will be an error, or api_success which may or may not include data. All data (if any) is returned as JSON. API calls here may also be called as standalone functions. 

This module needs to be fixed to use sessions and API keys. Another problem with it has been described as "register globals light" referring to the way API parameters are placed into globally defined variables in this module. Not a good idea. Currently there are also security issues including CSRF. It's possible this module will be thrown out in the future, however it's advisable to try fix it rather than throw it out considering this file is more than a thousand lines long. There is also the eval()s here which need to be removed. This module really needs to be object orientated. The API functions also need to be reprototyped to take a params variable (should be easy.)

Guidelines: 
* Naming is from the perspective of call. For example get_artist doesn't take a parameter called artist_name, no, it takes something called name, because it obviously refers to the artist name.
* All insert, get, and update calls should support the possibility of optional arguments where applicable.
* Update calls should fail if it doesn't exist/apply.
* Insert calls that require a specific set of inserted values to be unique must fail if they are not. They must not use this set to identify existing records in order to update them.
* All calls that fail will return debugging information.
* All insert calls should return the ID of the row on success.
* A call does NOT have to return anything on success.
* Optional fields for insert calls will may use the default defined in the database.

Todo:
* Error table.
* Refractor.
* Developer keys.
* Params parameter for api functions.
* Sessions

*/
set_time_limit(0);
require_once("global.php");
require_once("include/mysql_con.php");
require_once("include/function.php");
require_once("include/auth_token.php");

@session_start(); 

//Hack
//$_GET["username"] = isset($_SESSION["username"]) ? $_SESSION["username"] : $_GET["username"];
//$_GET["auth_username"] = isset($_COOKIE["auth_username"]) ? $_COOKIE["auth_username"] : $_GET["auth_username"];
//$_GET["auth_password"] = isset($_COOKIE["auth_password"]) ? $_COOKIE["auth_password"] : $_GET["auth_password"];

/*
if(empty($_GET["username"]))
{
	$_GET["username"] = $_GET["auth_username"];
}
*/

//Import functions that aren't API calls.
function api_success($value="", $c="")
{
	/*
	Takes an array and returns it as JSON.
	The output is "pretty" and allows humans to read it.
	Recent versions of PHP provide a function to do this but
	that wasn't always the case.
	*/

	//Value must be an array.
	if(!is_array($value))
	{
		return "";
	}
	
	if(!isset($value[0]))
	{
		//Must be an array of arrays.
		$temp = $value;
		$value = "";
		$value[0] = $temp;
	}
	else
	{
		//It's possible something could exist at 0.
		//but it still must be an array.
		if(!is_array($value[0]))
		{
			$temp = $value;
			$value = "";
			$value[0] = $temp;
		}
	}
	
		
	if(empty($c))
	{
		return $value;
	}
	else
	{
		return assoc_array_to_json($value);
	}
}
function api_failure($value="", $c="")
{
	/*
	Takes an array holding an error message and
	returns that as JSON.
	*/

	//Value must be an array.
	if(!is_array($value))
	{
		return "";
	}

	$temp = $value;
	unset($value);
	$value["errors"] = $temp;
	if(empty($c))
	{
		return $value;
	}
	else
	{
		$buf = "var at_json = {\r\n\t\"error\": [";
		foreach($value["errors"] as $error)
		{
			$buf .= '"' . double_quote_escape($error) . '", ';
		}
		$buf = substr($buf, 0, strlen($buf) - 2);
		$buf .= "]\r\n};";
		return $buf;
	}
}

#All possible parameters.
/*
Used by "register globals light" . . . These variables receive
the value of the value connected to a key in an associative array. Currently
this is used to easily extract data from the super globals $_GET array to pluck
the API call's data and make it accessible bellow.
*/
$auth_token = "";
$c = "";
$username = "";
$password = "";
$name = "";
$user_id = "";
$playlist_id = "";
$artist_id = "";
$json_name = "";
$json_output = "";
$title = "";
$album_id = "";
$artist_id = "";
$artist_name = "";
$album_title = "";
$genre = "";
$year = "";
$time_played = "";
$play_count = "";
$skip_count = "";
$time_skipped = "";
$rating_amount = "";
$auth_username = "";
$auth_password = "";
$id = "";
$type = "";
$target_id = "";
$music_id = "";
$number = "";
$service_provider = "";
$service_resource = "";

//API functions.
$return_value = api_failure(array("Invalid API call"), "main");
/*
Our first API function. Notice the $c parameter. It's used to indicate whether the function was
called by the API or from an internal part of the code. If it's called from the API then the output should
be JSON otherwise the output should be a normal PHP array. This allows the API functions to be used internally
and externally. $c will contain the name of the API call -- c=api_call_name if it was called from
the API, otherwise it will be empty "".
*/
function get_track($title, $artist_name, $playlist_id, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $user_id, $music_id, $service_provider, $service_resource, $number, $id, $c="")
{
	/*
	Return information for a track based on a track id. Here such information may be defined by using "qualifiers."
	A qualifier is where you explicitly set which information to return e.g. title=1. By define, all of the
	information is returned but if title were set to 1 then only the title would be returned. In the API handling
	code qualifiers often appear for the get functions, and they are optional. What is not optional, however, is
	the ID.

	Used by the get_track API call.
	*/
	$table_name = "track";
	$fields = array("id" => $id);
	$id = mysql_exist_ab($table_name, $fields);
	if($id != "0")
	{
		unset($fields["id"]);
		$table_name = "track";
		$fields["title"] = $title;
		$fields["artist_name"] = $artist_name;
		$fields["album_title"] = $album_title;
		$fields["genre"] = $genre;
		$fields["year"] = $year;
		$fields["time_played"] = $time_played;
		$fields["play_count"] = $play_count;
		$fields["skip_count"] = $skip_count;
		$fields["time_skipped"] = $time_skipped;
		$fields["time_added"] = $time_added;
		$fields["service_provider"] = $service_provider;
		$fields["service_resource"] = $service_resource;
		$fields["number"] = $number;
		$fields = apply_qualifiers($fields);
		if(isset($fields["artist_name"]))
		{
			unset($fields["artist_name"]);
			$fields["artist_id"] = "1"; 
		}
		if(isset($fields["album_title"]))
		{
			unset($fields["album_title"]);
			$fields["album_id"] = "1";   
		}
		
		$criteria = "`id`='$id'";
		$rows = mysql_get_ab($table_name, $fields, $criteria);
		if(!eval(is_empty('$rows')))
		{
			foreach($rows as &$row)
			{
				if(isset($fields["artist_id"]))
				{
					$result = get_artist($row["artist_id"]);
					$row["artist_name"] = $result[0]["name"];
					unset($row["artist_id"]);
				}
				if(isset($fields["album_id"]))
				{
					$result = get_album($row["album_id"]);
					$row["album_title"] = $result[0]["title"];
					unset($row["album_id"]);
				}
			}
			return api_success($rows, $c);
		}
		else
		{
			return api_success(array(), $c);
		}
	}
	else
	{
		return api_failure(array("This does not exist."), $c);
	}
}

function get_artist($id, $c="")
{
	/*
	Get an artist name based on an ID.

	Used by the get_artist API call.
	*/
	if(is_numeric($id) == 0)
	{
		$name = $id;
		return api_success(array("name" => $name), $c);
	}
	$sql = "SELECT `name` FROM `artist` WHERE `id`='$id'";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		mysql_free_result($result);
		return api_success(array("name" => $row["name"]), $c);
	}
	mysql_free_result($result);
	return api_failure(array("This does not exist."), $c);
}

function get_album($id, $c="")
{
	/*
	Get an album name based on an ID.

	Used by the get_album API call.
	*/

	if(is_numeric($id) == 0)
	{
		$title = $id;
		return api_success(array("title" => $title), $c);
	}
	$sql = "SELECT `title` FROM `album` WHERE `id`='$id'";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		mysql_free_result($result);
		return api_success(array("title" => $row["title"]), $c);
	}
	mysql_free_result($result);
	return api_failure(array("This does not exist."), $c);
}


function add_artist($name, $c="")
{
	/*
	Anontune retains two main sets of information for music:
	The music table and the track table. The music table is all of our
	unique information for music in the database. The track table is the
	user's copy of their music. If a user adds a track we don't already
	know about to their playlist then it is added to our music table, also.

	What add_artist does is adds a unique artist to the artist table.

	Used by the add_artist API call and the insert_update_track function.
	*/

	/*
	No direct access for users.
	*/
	//$name = clean_data($name);
	if($name != "Unknown")
	{
		$table_name = "artist";
		$fields = array("name" => $name);
		$id = mysql_exist_ab($table_name, $fields);
		//Only insert if it doesn't exist.
		if($id == "0")
		{
			$id = mysql_insert_ab($table_name, $fields);
		}
		return api_success(array("id" => "$id"), $c);
	}
	return api_success(array("id" => "1"), $c);
}

function add_album($title, $artist_id, $c="")
{
	/*
	Adds a unique album to the album table. An album is owned by an
	artist, hence this information is associated.

	Used by the add_album API call and the insert_update_track function.
	*/

	/*
	No direct access for users.
	*/
	//$title = clean_data($title);
	if($title != "Unknown")
	{
		//Check artist id.
		$sql = "SELECT `id` FROM `artist` WHERE `id`='$artist_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("Artist does not exist."), $c);
		}
	
		$table_name = "album";
		$fields = array("title" => $title, "artist_id" => $artist_id);
		$id = mysql_exist_ab($table_name, $fields);
		//Only insert if it doesn't exist.
		if($id == "0")
		{
			$fields["is_valid"] = "0";
			$id = mysql_insert_ab($table_name, $fields);
		}
		return api_success(array("id" => "$id"), $c);
	}
	return api_success(array("id" => "1"), $c);
}

function add_music($title, $artist_id, $album_id, $c="")
{
	/*
	A track is owned by an artist, and may be contained in an album.
	This function adds a unique track to the music table.

	Used by the add_music API call and the insert_update_track function.
	*/

	/*
	No direct access for users.
	*/
	//$title = clean_data($title);
	if($title != "Unknown")
	{
		//Check artist id.
		$sql = "SELECT `id` FROM `artist` WHERE `id`='$artist_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("Artist does not exist."), $c);
		}
		
		//Check album id.
		$sql = "SELECT `id` FROM `album` WHERE `id`='$album_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("Album does not exist."), $c);
		}
	
		$table_name = "music";
		$fields = array("title" => $title, "artist_id" => $artist_id, "album_id" => $album_id);
		$id = mysql_exist_ab($table_name, $fields);
		//Only insert if it doesn't exist.
		if($id == "0")
		{
			$fields["is_valid"] = "0";
			$fields["total_rating"] = "0";
			$fields["total_rater"] = "0";
			$fields["total_like"] = "0";
			$fields["total_dislike"] = "0";
			$fields["play_count"] = "0";
			$fields["skip_count"] = "0";
			$id = mysql_insert_ab($table_name, $fields);
		}
		return api_success(array("id" => "$id"), $c);
	}
	return api_success(array("id" => "$id"), $c);
}

function insert_update_rating($type, $target_id, $amount, $id="", $c="")
{
	/*
	Users may rate their own tracks, however such a rating isn't against their music.
	It's against our table of unique music which also includes a track such as their own.
	Ratings shouldn't be done against user data. The main website should make reference to
	user data but rather a main repository of such data. If we were to do it the other
	way, when a user deletes their track, all the information refering to such a track
	would be broken.

	So: When a user rates a track their rating is against music not the track.

	Used by insert_rating, update_rating, delete_rating API calls and insert_update_track function.
	*/
	global $user_id;
	
	//Check amount.
	if(!eval(is_empty('$amount')) && !eval(is_empty('$id')))
	{
		if(is_numeric($amount) == 0)
		{
			return api_failure(array("Amount must be between 1 and 100."), $c);
		}
		else
		{
			if($amount < 1 || $amount > 100)
			{
				return api_failure(array("Amount must be between 1 and 100."), $c);
			}
		}
	}
	
	//Check type and target_id.
	if(!eval(is_empty('$type')) && !eval(is_empty('$target_id')) && !eval(is_empty('$id')))
	{
		if($type != "music")
		{
			//For now . . .
			return api_failure(array("Invalid type."), $c);
		}
	
		//Does it exist?
		$sql = "SELECT * FROM `$type` WHERE `id`='$target_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("This does not exist."), $c);
		}
	}
	
	$fields = array("user_id" => $user_id, "type" => $type, "target_id" => $target_id);
	if($type == "music")
	{
		$music_id = $target_id;
	}
	
	//Update rating.
	if(!eval(is_empty('$id')))
	{
		//If they are changing a rating is it theirs?
		$sql = "SELECT * FROM `rating` WHERE `id`='$id' AND `user_id`='$user_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("This does not exist."), $c);
		}
		
		//Store old rating amount.
		$old_amount = $result[0]["amount"];
		
		//Use old type if not set.
		if(eval(is_empty('$type')))
		{
			$type = $result[0]["type"];
		}
		
		//Use old target id if not set.
		if(eval(is_empty('$target_id')))
		{
			$target_id = $result[0]["target_id"];
		}
		
		//Use old amount if not set.
		if(eval(is_empty('$amount')))
		{
			$amount = $result[0]["amount"];   
		}
		
		//Undo music totals done by old rating.
		if($type == "music")
		{
			$music_id = $target_id;
			update_music_totals($music_id, $old_amount, "-");
		}
		
		//Update rating.
		$fields["id"] = $id;
		$fields["amount"] = $amount;
		$fields = apply_qualifiers($fields);
		mysql_update_ab("rating", $fields);
		
		//Apply new music totals.
		if($type == "music")
		{
			update_music_totals($music_id, $amount, "+");
		}
		return api_success("", $c);
	}
	else //Insert rating.
	{
		//Check if it exists.
		$id = mysql_exist_ab("rating", $fields);
		
		//It doesn't exist, insert it.
		if($id == "0")
		{
			$fields["amount"] = $amount;
			$id = mysql_insert_ab("rating", $fields);
			
			//Apply new music totals.
			if($type == "music")
			{
				update_music_totals($music_id, $amount, "+");
			}
			return api_success(array("id" => $id), $c);
		}
		else
		{
			return api_failure(array("This already exists"), $c, $c);
		}
	}
}


function delete_rating($id, $c="")
{
	/*
	Deletes a user's rating against a main track. This function also adjusts averages and
	stuff like that.

	Used by delete_rating API call and insert_update_track function.
	*/
	global $user_id;
	
	//Get rating.
	$sql = "SELECT * FROM `rating` WHERE `id`='$id' AND `user_id`='$user_id'";
	$result = mysql_get($sql);
	if(eval(is_empty('$result')))
	{
		return api_failure(array("This does not exist."), $c);
	}
	$type = $result[0]["type"];
	$target_id = $result[0]["target_id"];
	$amount = $result[0]["amount"];
		
	//Undo music totals.
	if($type == "music")
	{
		update_music_totals($target_id, $amount, "-");
	}
	
	//Delete rating.
	$sql = "DELETE FROM `rating` WHERE `id`='$id' AND `user_id`='$user_id'";
	query($sql);
 
	return api_success("", $c);
}

function get_playlists($c="")
{
	/*
	A user has a collection of lists for music called playlists and each playlist
	may or may not have music associated with it. This function returns the names
	of those lists based on a username. These names and IDs may then be used to
	retrieve the playlist's actual content.

	Used by the get_playlists API call.
	*/

	global $user_id;
	$sql = "SELECT * FROM `playlist` WHERE `user_id`='$user_id'";
	$row = mysql_get($sql);
	if(eval(is_empty('$row')))
	{
		//return api_failure(array("This does not exist."), $c);
		return api_success(array(), $c);
	}
	return api_success($row, $c);
}


function get_playlist($title, $artist_name, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $service_provider, $service_resource, $number, $id, $c="")
{
	/*
	Given a playlist ID return the tracks in it.

	Used by the get_playlist API call.
	*/
	$table_name = "playlist";
	$fields = array("id" => $id);
	$id = mysql_exist_ab($table_name, $fields);
	if($id != "0")
	{
		unset($fields["id"]);
		
		
		$table_name = "track";
		$fields["title"] = $title;
		$fields["artist_name"] = $artist_name;
		$fields["album_title"] = $album_title;
		$fields["genre"] = $genre;
		$fields["year"] = $year;
		$fields["time_played"] = $time_played;
		$fields["play_count"] = $play_count;
		$fields["skip_count"] = $skip_count;
		$fields["time_skipped"] = $time_skipped;
		$fields["time_added"] = $time_added;
		$fields["service_provider"] = $service_provider;
		$fields["service_resource"] = $service_resource;
		$fields["number"] = $number;
		$fields["id"] = "1";
		$fields = apply_qualifiers($fields);
		if(isset($fields["artist_name"]))
		{
			unset($fields["artist_name"]);
			$fields["artist_id"] = "1"; 
		}
		if(isset($fields["album_title"]))
		{
			unset($fields["album_title"]);
			$fields["album_id"] = "1";   
		}
		
		$criteria = "`playlist_id`='$id'";
		$rows = mysql_get_ab($table_name, $fields, $criteria);
		if(!eval(is_empty('$rows')))
		{
			foreach($rows as &$row)
			{
				if(isset($fields["artist_id"]))
				{
					$result = get_artist($row["artist_id"]);
					$row["artist_name"] = $result[0]["name"];
					unset($row["artist_id"]);
				}
				if(isset($fields["album_id"]))
				{
					$result = get_album($row["album_id"]);
					$row["album_title"] = $result[0]["title"];
					unset($row["album_id"]);
				}
			}
			return api_success($rows, $c);
		}
		else
		{
			return api_success(array(), $c);
		}
	}
	else
	{
		return api_failure(array("This does not exist."), $c);
	}
}

function insert_update_playlist($name, $parent_id="0", $cmd="0", $id="", $c="")
{
	/*
	Allows new playlists to be created, deleted, or updated.

	This function breaks the API standard by returning
	an ID if it exists and not an error.

	Used by the insert_playlist, update_playlist, delete_playlist API calls.
	*/
	global $user_id;
	
	//Update playlist if it exists and belongs to user.
	$table_name = "playlist";
	$fields = array("name" => $name, "parent_id" => $parent_id, "cmd" => $cmd, "user_id" => $user_id);
	if(!eval(is_empty('$id')))
	{
		$sql = "SELECT `id` FROM `playlist` WHERE `id`='$id' AND `user_id`='$user_id'";
		$result = mysql_get($sql);
		if(!eval(is_empty('$result')))
		{
				//Only use parents that belong to the user.
				$sql = "SELECT `id` FROM `playlist` WHERE `id`='$id' AND `user_id`='$user_id'";
				$result = mysql_get($sql);
				if(!eval(is_empty('$result')))
				{
					$fields["id"] = $id;
					mysql_update_ab($table_name, $fields);
					return api_success("", $c);
				}
				else
				{
					return api_failure(array("Invalid parent_id."), $c);   
				}
		}
		else
		{
			return api_failure(array("This does not exist"), $c);
		}
	}
	else //Insert playlist.
	{
		//Check if it exists.
		$id = mysql_exist_ab($table_name, $fields);
		
		//It doesn't exist, insert it.
		if($id == "0")
		{
			$id = mysql_insert_ab($table_name, $fields);
			return api_success(array("id" => "$id"), $c);
		}
		else
		{
			return api_success(array("id" => "$id", "warning" => "This already exists."), $c);
			//return api_failure(array("This already exists."), $c); 
		}
	}
}

function delete_track($id, $c="")
{
	/*
	Delete's a user's track from a playlist and any ratings that were made against it.
	(Thereby updating the global track rating information.)

	Used by the delete_track API call.
	*/
	global $user_id;
	
	//Check track exists.
	$sql = "SELECT * FROM `track` WHERE `id`='$id' AND `user_id`='$user_id'";
	$result = mysql_get($sql);
	if(eval(is_empty('$result')))
	{
		return api_failure(array("This does not exist."), $c);
	}
	
	//Get music id.
	$music_id = $result[0]["music_id"];
	
	//Get rating id.
	$sql = "SELECT `id` FROM `rating` WHERE `type`='music' AND `target_id`='$music_id' AND `user_id`='$user_id'";
	$result = mysql_get($sql);
	if(!eval(is_empty('$result')))
	{
		$rating_id = $result[0]["id"];
		
		//Delete rating.
		delete_rating($rating_id);
	}
	
	//Finally, delete track.
	$sql = "DELETE FROM `track` WHERE `id`='$id' AND `user_id`='$user_id'";
	query($sql);
	
	//Delete associated comments.
	return api_success("", $c);
}

function delete_playlist($id, $c="")
{
	/*
	Deletes a playlist given it's ID.

	Used by the delete_playlist API call.
	*/
	global $user_id;
	
	//Check it exists.
	$sql = "SELECT `id` FROM `playlist` WHERE `id`='$id' AND `user_id`='$user_id'";
	$result = mysql_get($sql);
	if(eval(is_empty('$result')))
	{
		return api_failure(array("This does not exist."), $c);
	}
	
	//Delete tracks.
	$sql = "SELECT `id` FROM `track` WHERE `playlist_id`='$id' AND `user_id`='$user_id'";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		delete_track($row["id"]);
	}
	
	//Delete playlist.
	$sql = "DELETE FROM `playlist` WHERE `id`='$id' AND `user_id`='$user_id'";
	query($sql);

	return api_success("", $c);
}


function insert_update_track($title, $artist_name, $playlist_id, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $rating_amount, $service_provider, $service_resource, $number, $id="", $c="")
{
	/*
	Allows a new track to be inserted or updated in an existing playlist.
	Much of the information this function supports is optional, however at
	the very least every track must have a name and artist.

	This function breaks the API standard by returning
	an ID if it exists and not an error.

	Used by the insert_track and update_track API calls.
	*/
	global $user_id;
	global $not_set;
	
	$playlist_id = mysql_escape_string($playlist_id);
	$id = mysql_escape_string($id);
	
	//Check track exists and get old values if it does.
	//Store important values.
	if(!eval(is_empty('$id')))
	{
		$sql = "SELECT * FROM `track` WHERE `id`='$id' AND `user_id`='$user_id'";
		$result = mysql_get($sql);
		if(eval(is_empty('$result')))
		{
			return api_failure(array("This does not exist."), $c);
		}
		$old_music_id = mysql_escape_string($result[0]["music_id"]);
		$artist_id = mysql_escape_string($result[0]["artist_id"]);
		$album_id = mysql_escape_string($result[0]["album_id"]);
		
		$sql = "SELECT * FROM `rating` WHERE `type`='music' AND `target_id`='$old_music_id' AND `user_id`='$user_id'";
		$result = mysql_get($sql);
		$rating_id = 0;
		if(!eval(is_empty('$result')))
		{
			$rating_id = $result[0]["id"];
			if(eval(is_empty('$rating_amount')))
			{
				$rating_amount = $result[0]["amount"];   
			}
		}
		else
		{
			if(eval(is_empty('$rating_amount')))
			{
				$rating_amount = "0";   
			}
		}
	}
	
	//Check playlist id.
	if(!eval(is_empty('$playlist_id')))
	{
		if(!is_numeric($playlist_id))
		{
			return api_failure(array("Invalid playlist id."), $c);
		}
		$sql = "SELECT `id` FROM `playlist` WHERE `id`='$playlist_id' AND `user_id`='$user_id'";
		$result = query($sql);
		if(mysql_num_rows($result) == 0)
		{
			return api_failure(array("Playlist does not exist."), $c);
		}
	}
	
	//Validate data.
	$valid_year = "1984";
	$valid_time = time();
	//$time_format = "^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$";
	if(!eval(is_empty('$number')))
	{
		if(is_numeric($number) == 0)
		{
			$number = 0;   
		}
	}
	if(!eval(is_empty('$service_provider')))
	{
		if(is_numeric($service_provider) == 0)
		{
			$service_provider = 1;
		}
	}
	if(!eval(is_empty('$artist_name')))
	{
		$artist_name = clean_data($artist_name);
		$artist_id = add_artist(mysql_escape_string($artist_name));
		$artist_id = $artist_id[0]["id"];
	}
	else
	{
		if(eval(is_empty('$artist_id')))
		{
			$artist_id = 1;   
		}
	}
	
	if(!eval(is_empty('$album_title')))
	{
		$album_title = clean_data($album_title);
		$album_id = add_album(mysql_escape_string($album_title), $artist_id);
		$album_id = $album_id[0]["id"];
	}
	else
	{
		if(eval(is_empty('$album_id')))
		{
			$album_id = 1;
		}
	}
	
	if(!eval(is_empty('$title')))
	{
		$title = clean_data($title);
		$music_id = add_music(mysql_escape_string($title), $artist_id, $album_id);
		$music_id = $music_id[0]["id"];
	}
	else
	{
		if(eval(is_empty('$music_id')))
		{
			$music_id = 1;
		}
	}

	if(!eval(is_empty('$genre')))
	{
		$genre = clean_data($genre);
	}
	if(!eval(is_empty('$year')))
	{
		if(is_numeric($year) == 0)
		{
			$year = "1984";
		}
	}
	if(!eval(is_empty('$rating_amount')))
	{
		if(is_numeric($rating_amount) == 0)
		{
			$rating_amount = "0";
		}
		else
		{
			if($rating_amount < 1 || $rating_amount > 100)
			{
				$rating_amount = "0";   
			}
		}
	}
	if(!eval(is_empty('$time_played')))
	{
		if(is_numeric($time_played) == 0)
		{
			$time_played = $valid_time;
		}
	}
	if(!eval(is_empty('$time_skipped')))
	{
		if(is_numeric($time_skipped == 0))
		{
			$time_skipped = $valid_time;
		}
	}
	if(!eval(is_empty('$time_added')))
	{
		if(is_numeric($time_added) == 0)
		{
			$time_added = $valid_time;
		}
	}
	if(!eval(is_empty('$play_count')))
	{
		if(!is_numeric($play_count))
		{
			$play_count = 0;
		}
	}
	if(!eval(is_empty('$skip_count')))
	{
		if(!is_numeric($skip_count))
		{
			$skip_count = 0;
		}
	}

	
	//Escape data.
	$title = mysql_escape_string($title);
	$genre = mysql_escape_string($genre);
	$year = mysql_escape_string($year);
	$time_play = mysql_escape_string($time_played);
	$time_added = mysql_escape_string($time_added);
	$play_count = mysql_escape_string($play_count);
	$skip_count = mysql_escape_string($skip_count);
	$time_skipped = mysql_escape_string($time_skipped);
	$service_resource = mysql_escape_string($service_resource);
	$artist_id = mysql_escape_string($artist_id);
	$album_id = mysql_escape_string($album_id);
	$music_id = mysql_escape_string($music_id);
	
	//Update track if it exists and belongs to the user.
	$table_name = "track";
	$fields = array(
	"title" => $title,
	"artist_id" => $artist_id,
	"genre" => $genre,
	"album_id" => $album_id,
	"year" => $year,
	"time_played" => $time_played,
	"time_added" => $time_added,
	"playlist_id" => $playlist_id,
	"play_count" => $play_count,
	"skip_count" => $skip_count,
	"time_skipped" => $time_skipped,
	"service_provider" => $service_provider,
	"service_resource" => $service_resource,
	"number" => $number,
	"user_id" => $user_id,
	"music_id" => $music_id);
	$fields = apply_qualifiers($fields);
	if(!eval(is_empty('$id')))
	{
		if(!eval(is_empty('$rating_amount')))
		{
			if($rating_amount)
			{
				//Delete rating associated with old music.
				if($rating_id)
				{
					delete_rating($rating_id);
				}
				
				//Use old music id if need be.
				if(eval(is_empty('$title')) && eval(is_empty('$artist_name')))
				{
					$music_id = $old_music_id; 
				}
				
				//Insert new rating.
				insert_update_rating("music", $music_id, $rating_amount);
			}
		}
		
		//Update track.
		$fields["id"] = $id;
		mysql_update_ab($table_name, $fields);
		
		return api_success("", $c);
	}
	else //Insert.
	{
		/*
		All of the fields bellow together can't exist
		as part of another row or we are inserting a duplicate regardless of differences between other data.
		*/
		$highlight_fields = array(
		"title" => $title,
		"artist_id" => $artist_id,
		"playlist_id" => $playlist_id,
		"user_id" => $user_id);
		$id = mysql_exist_ab($table_name, $highlight_fields);
		
		//It doesn't exist, insert it.
		if($id == "0")
		{
			$id = mysql_insert_ab($table_name, $fields);
			if(!eval(is_empty('$rating_amount')))
			{
				if($rating_amount)
				{
					insert_update_rating("music", $music_id, $rating_amount);
				}
			}
			return api_success(array("id" => "$id"), $c);
		}
		else
		{
			return api_success(array("id" => "$id", "warning" => "This already exists."), $c);
			//return api_failure(array("This already exists."), $c);
		}
	}
}

function upload_ipod_db($username, $c="")
{
	/*
	This function handles accepting an uploaded iPod database. It will create the required
	directory structure on the server so other scripts can read and process that user's database.

	Used by the upload_ipod_db API calls.
	*/
	global $user_id;
	global $config;

	//Check that we have a file
	if(!eval(is_empty('$_FILES["uploaded_file"]')))
	{
		$filename = basename($_FILES['uploaded_file']['name']);
		if($_FILES["uploaded_file"]["size"] < $config->anontune->ipod_db->max_size)
		{
			$ipod_db_path = "/home/anontune/ipod_db" . DIRECTORY_SEPARATOR . $username;
		
			//Check a database doesn't already exist.
			if(is_dir($ipod_db_path))
			{
				return api_failure(array("Database already exists."), $c);
			}
			
			//Create ipod directory structure.
			mkdir($ipod_db_path, 0777);
			chmod($ipod_db_path, 0777);
			$ipod_db_path .= DIRECTORY_SEPARATOR . "iPod_Control";
			mkdir($ipod_db_path, 0777);
			chmod($ipod_db_path, 0777);
			$ipod_db_path .= DIRECTORY_SEPARATOR . "iTunes";
			mkdir($ipod_db_path, 0777);
			chmod($ipod_db_path, 0777);
		
			$newname = $ipod_db_path . DIRECTORY_SEPARATOR . $filename;
			//Attempt to move the uploaded file to it's new place
			if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$newname))
			{
				return api_success(array("file" => "$filename"), $c);
			}
			else
			{
				//Error: A problem occurred during file upload!
				return api_failure(array("Could not move uploaded file from temp."), $c);
			}
		}
		else
		{
			//Size error.
			return api_failure(array("File is too large."), $c);
		}
	}
	else
	{
		//File not specified.
		return api_failure(array("Nothing to upload."), $c);
	}

}

//Make calls.
/*
This code handles making the API calls. It unpacks all of the information in an API request or
HTTP GET request and passes it on to the appropriate call. It will check the API call
is allowed to run and exists, handling any errors that occur along the way. Note: This
function doesn't actually run forever and none of the API calls are cached to prevent
browser issues.
*/
while(1) //Makes error handling easier.
{
	chk_glob("c", $_GET);
	if(chk_glob("username", $_GET, "", 0))
	{
		if($username != $not_set) //Set user_id.
		{
			$username = get_username(mysql_escape_string($username));
			$user_id = mysql_escape_string(get_user_id($username));
			if(eval(is_empty('$user_id')))
			{
				break;
			}
		}
	}

	switch($c)
	{
		//No authentication required.
		case "get_artist":
			if(chk_glob("id", $_GET))
			{
				$return_value = get_artist($id, $c);
			}
			break;
		case "get_album":
			if(chk_glob("id", $_GET))
			{
				$return_value = get_artist($id, $c);
			}
			break;
		case "get_playlists":
			if(chk_glob("username", $_GET))
			{
				$return_value = get_playlists($c);
			}
			break;
		case "get_playlist":
			$opt = "title,artist_name,genre,album_title,year,";
			$opt .= "time_played,time_added,play_count,skip_count,";
			$opt .= "time_skipped,service_provider,service_resource,number";
			$min = 0;
			chk_glob($opt, $_GET, $min);
			if(chk_glob("id", $_GET))
			{
				$return_value = get_playlist($title, $artist_name, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $service_provider, $service_resource, $number, $id, $c);
			}
			break;
		case "get_track":
			$opt = "title,artist_id,playlist_id,album_id,genre,year,";
			$opt .= "time_played,play_count,skip_count,time_skipped,";
			$opt .= "time_added,user_id,music_id,service_provider,service_resource,number";
			$min = 0;
			chk_glob($opt, $_GET, $min);
			if(chk_glob("id", $_GET))
			{
				$return_value = get_track($title, $artist_id, $playlist_id, $album_id, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $user_id, $music_id, $service_provider, $service_resource, $number, $id, $c);
			}
			break;
			
		//Authentication required.
		default:
			if(!chk_glob("auth_token", $_GET))
			{
				break;
			}
			//Check credentials.
			if(verify_token($auth_token) != 1)
			{
				break;
			}
			
			//Grab group.
			if(isset($_SESSION["auth_username"]))
			{
				$auth_username = $_SESSION["auth_username"];
			}
			$sql = "SELECT `group` FROM `user` WHERE `username`='$auth_username'";
			$result = mysql_get($sql);
			$group = $result[0]["group"];
			
			//User access level.
			if($group >= 1)
			{
				//Users can only do user API calls against users if it is them.
				if($username != $auth_username && $group == "1")
				{
					break;
				}
				switch($c)
				{
					case "upload_ipod_db":
						if(chk_glob("username", $_GET))
						{
							$return_value = upload_ipod_db($username, $c);
						}
						break;
					case "insert_rating":
						break;
						if(chk_glob("type,target_id,amount,username", $_GET))
						{
							$return_value = insert_update_rating($type, $target_id, $amount, "", $c);
						}
						break;
					case "update_rating":
						break;
						$min = 1;
						chk_glob("type,target_id,amount", $_GET, $min);
						if(chk_glob("id,username", $_GET))
						{
							$return_value = insert_update_rating($type, $target_id, $amount, $id, $c);
						}
						break;
					case "delete_rating":
						break;
						if(chk_glob("id,username", $_GET))
						{
							$return_value = delete_rating($id, $c);
						}
						break;
					case "insert_playlist":
						if(chk_glob("name,parent_id,cmd,username", $_GET))
						{
							$return_value = insert_update_playlist($name, $parent_id, $cmd, "", $c);
						}
						break;
					case "update_playlist":
						if(chk_glob("name,parent_id,cmd,id,username", $_GET))
						{
							$return_value = insert_update_playlist($name, $parent_id, $cmd, $id, $c);
						}
						break;
					case "delete_playlist":
						if(chk_glob("id,username", $_GET) && valid_ref())
						{
							$return_value = delete_playlist($id, $c);
						}
						break;
					case "insert_track":
						$opt = "album_title,genre,year,";
						$opt .= "time_played,play_count,skip_count,time_skipped,";
						$opt .= "time_added,rating_amount,service_provider,service_resource,number";
						$min = 0;
						$db_escape = 0;
						//THESE PARAMETERS ARE NOT DB ESCAPED.
						chk_glob($opt, $_GET, $min, $db_escape);
						if(chk_glob("title,artist_name,playlist_id,username", $_GET, "", $db_escape) && valid_ref())
						{
							$return_value = insert_update_track($title, $artist_name, $playlist_id, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $rating_amount, $service_provider, $service_resource, $number, "", $c);
						}
						break;
					case "update_track":
						$opt = "title,artist_name,album_title,genre,year,";
						$opt .= "playlist_id,";
						$opt .= "time_played,play_count,skip_count,time_skipped,";
						$opt .= "time_added,rating_amount,service_provider,service_resource,number";
						$min = 1;
						$db_escape = 0;
						//THESE PARAMETERS ARE NOT DB ESCAPED.
						if(chk_glob($opt, $_GET, $min, $db_escape))
						{
							if(chk_glob("id,username", $_GET, "", $db_escape))
							{
								$return_value = insert_update_track($title, $artist_name, $playlist_id, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $rating_amount, $service_provider, $service_resource, $number, $id, $c);
							}
						}
						break;
					case "delete_track":
						if(chk_glob("id,username", $_GET))
						{
							$return_value = delete_track($id, $c);
						}
						break;
				}
			}
			
			//Admin access level.
			if($group == 4)
			{
				switch($c)
				{
					case "add_artist":
						if(chk_glob("name", $_GET, "", 0))
						{
							$name = mysql_escape_string(clean_data($name));
							$return_value = add_artist($name, $c);
						}
						break;
					case "add_album":
						if(chk_glob("title,artist_id", $_GET, "", 0))
						{
							$title = mysql_escape_string(clean_data($title));
							$return_value = add_album($title, $artist_id, $c);
						}
						break;
					case "add_music":
						if(chk_glob("title,artist_id,album_id", $_GET, "", 0))
						{
							$title = mysql_escape_string(clean_data($title));
							$return_value = add_music($title, $artist_id, $album_id, $c);
						}
						break;
				}
			}

			//Append new auth_token to $return_value;
			$options = "";
			$options["ip_addr"] = "this";
			$options["expiry"] = 2 * 24 * 60 * 60; //2 days.
			$options["user_agent"] = "this";
			$options["uses"] = "1";
			$options["referer"] = "this";
			$options["auth_username"] = $_SESSION["auth_username"];
			$options["auth_password"] = $_SESSION["auth_password"];
			$auth_token = create_token($options);
			$return_value_len = strlen($return_value);
			$return_value = substr($return_value, 0, $return_value_len - 4) . ",\r\n\t\"auth_token\": \"" . $auth_token . "\"\r\n};";
	}
	break;
}

//Return API result.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
echo $return_value;

cleanup();
