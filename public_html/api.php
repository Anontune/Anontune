<?php
/*
Version: 0.0.6 Alpha
Description: The Anontune API.

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

*/
set_time_limit(0);
require_once("global.php");
require_once("include/mysql_con.php");

//Hack
$_GET["username"] = isset($_COOKIE["username"]) ? $_COOKIE["username"] : $_GET["username"];
$_GET["auth_username"] = isset($_COOKIE["auth_username"]) ? $_COOKIE["auth_username"] : $_GET["auth_username"];
$_GET["auth_password"] = isset($_COOKIE["auth_password"]) ? $_COOKIE["auth_password"] : $_GET["auth_password"];

//Import functions that aren't API calls.
function api_success($value="", $c="")
{
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
function get_track($title, $artist_name, $playlist_id, $album_title, $genre, $year, $time_played, $play_count, $skip_count, $time_skipped, $time_added, $user_id, $music_id, $service_provider, $service_resource, $number, $id, $c="")
{
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
When a user rates a track their rating is against music not the track.
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
This function breaks the API standard by returning
an ID if it exists and not an error.
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
This function breaks the API standard by returning
an ID if it exists and not an error.
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
            if(!chk_glob("auth_username,auth_password", $_GET))
            {
                break;
            }
            //Check credentials.
            if(!check_credential($auth_username, $auth_password))
            {
                break;
            }
            
            //Grab group.
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
                        if(chk_glob("type,target_id,amount,username", $_GET))
                        {
                            $return_value = insert_update_rating($type, $target_id, $amount, "", $c);
                        }
                        break;
                    case "update_rating":
                        $min = 1;
                        chk_glob("type,target_id,amount", $_GET, $min);
                        if(chk_glob("id,username", $_GET))
                        {
                            $return_value = insert_update_rating($type, $target_id, $amount, $id, $c);
                        }
                        break;
                    case "delete_rating":
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
                        if(chk_glob("id,username", $_GET))
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
                        if(chk_glob("title,artist_name,playlist_id,username", $_GET, "", $db_escape))
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
    }
    break;
}

//Return API result.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
echo $return_value;

cleanup();
?>