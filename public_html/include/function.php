<?php
/*
 *  This file is part of Anontune.
 *
 *  Anontune is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published byu
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
 
/*
Provides generic functions that couldn't justify their own module. Much of
Anontune uses these functions, especially the API.
*/

require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "../global.php");

function valid_ref()
{
	/*
	Band aid for the CSRF API issue. It obviously isn't flawless. This function
	checks the refer is an Anontune domain returning true or false based on
	that. Referers can be spoofed, it's not flawless.
	*/

	global $root_url;
	$ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
	$ref = parse_url($ref);
	if($ref == FALSE) return 0;
	if(!isset($ref["host"])) return 0;
	$ref = $ref["host"];
	if(strpos($root_url, $ref) === false) return 0;
	return 1;
}

function is_empty($identifier)
{
	/*
	This is a custom implementation of PHP's empty function based on how a certain
	developer expected the function to behave.

	Back in the day, Anontune devs were experimenting
	with meta-programming. New tools and ideas were created allowing any
	programming language to be arbitrarily rewritten and extended. It worked by defining high-level
	abstractions or macros in an XML file which could then be used in a specific module once
	enabled. The macro was then resolved recursively so with our processor
	to the underlying code used in a particular module so you could, for example,
	do cool stuff like add multi-like Python-style strings to Javascript (in fact, we did.)

	Macros could be written in any programming language, and could themselves consist of other macros.
	This was possible because most programming languages are what's called "Turing-complete."
	Hypothetically speaking, any problem that can be solved in one Turing-complete programming
	language can be solved in another, therefore we were able to hack programming languages themselves
	in any way we liked due to the underlying flexibility of a Turing-complete language.

	However, this made everything messy and complicated. They added evals to the code. Debugging became
	intense and compiling the macros slowed down our editor; It also meant other developers would
	need to understand our macro system and use our editor . . . so we don't use them anymore
	but is_empty was a function that returns PHP code implementing a custom empty function. We
	had a macro that we could use: IS_EMPTY -- like it were a function, which would then
	be replaced with eval(is_empty(identifier)). (Required because of how PHP works.)

	Obviously, this was just a little too intense/experimental and we don't use macros
	anymore. But that's what this function did. Hope you enjoyed the hist0ry. Ph33r.
	*/
	global $not_set;
	$full_identifier = $identifier;
	$len = strlen($identifier);
	if($identifier[$len - 1] == "]")
	{
		for($i = $len - 1; $i != -1; $i--)
		{
			if($i - 1 != -1)
			{
				if($identifier[$i] == "[" && $identifier[$i - 1] != "]")
				{
					$identifier = substr($identifier, 0, $len - ($len - $i));
					/*
					Now identifier will just be the array name.
					So for example: $x->fsfdsdf->sdfsdf["sdfsdf"]->x["sdfsdf"]["sdfsdf"]
					should just be:
					$x->fsfdsdf->sdfsdf["sdfsdf"]->x
					where is represents an array.
					*/
					break;
				}
			}
		}
	}
	$code = "
	if(isset($identifier))
	{
		if(isset($full_identifier))
		{
			if(is_bool($full_identifier))
			{
				//Bool to string that's false
				//is usually set to an empty string
				//I prefer 0.
				if(!$full_identifier)
				{
					$full_identifier = '0';
				}
			}
		}
		if(is_array($identifier))
		{
			if(isset($full_identifier))
			{
				if(preg_match('/^\s*$/', (string) $full_identifier))
				{
					return 1;
				}
				if($full_identifier == '$not_set')
				{
					return 1;   
				}
				return 0;
			}
			return 1;
		}
		else
		{
			if('$identifier' != '$full_identifier')
			{
				return 1;
			}
			else
			{
				if(preg_match('/^\s*$/', (string) $identifier))
				{
					return 1;
				}
				if($identifier == '$not_set')
				{
					return 1;   
				}
				return 0;
			}
		}
	}
	return 1;";
	return $code;
}

function page_template($title, $body)
{
	/*
	1337 template function. Smarty ain't got nothing on this shit.
	*/

	$html = "
	<html>
	<head>
	<title>$title</title>
	</head>
	<body>
	$body
	</body>
	</html>
	";
	echo $html;
}

function table_lookup($name)
{
	/*
	When inserting new entities always append to the end. Never
	change existing entities. Code refers to this for dynamic.
	*/

	static $table = array("mood",
	"user",
	"album",
	"tag",
	"music",
	"pod",
	"rating",
	"playlist",
	"message",
	"friend",
	"ip address history",
	"bad word",
	"banned");
	if(is_numeric($name))
	{
		if(isset($table[$name]))
		{
			return $table[$name];
		}
	}
	else
	{
		return array_search($name, $table);
	}
}

function group_lookup($name)
{
	/*
	When inserting new entities always append to the end. Never
	change existing entities. Code refers to this for dynamic.
	*/

	static $table = array("user", "moderator", "super moderator", "admin");
	if(is_numeric($name))
	{
		if(isset($table[$name]))
		{
			return $table[$name];
		}
	}
	else
	{
		return array_search($name, $table);
	}
}

function query($sql)
{
	/*
	Wrapper for mysql queries.
	*/
	global $mysql_con;

	$result = mysql_query($sql, $mysql_con);
	if(!$result)
	{
		die('Invalid query: ' . mysql_error());
	}
	return $result;
}


function cleanup()
{
	/*
	This function is called at the end of any PHP script that
	executes to close the MySQL connection (if open.)
	*/

	global $mysql_con;
	if($mysql_con != 0)
	{
		mysql_close($mysql_con);
		$mysql_con = 0;
	}
}

function hash_password($password)
{
	/*
	Take a password and hash in my way.
	*/
	global $salt;
	return hash("sha256", md5(str_rot13($password . $salt)));
}

function activation_code()
{
	/*
	Generate an random activation code.
	*/

	$len = 20;
	$code = "";
	for($i = 0; $i < $len; $i++)
	{
		$code .= chr(rand(97, 122));
	}
	return $code;
}

function check_credential($username, $password)
{
	/*
	Check whether a password is correct for a given username or
	check whether a username is correct for a given password.
	Will fail if the username doesn't exist.
	*/

	$username = mysql_escape_string($username);
	$hash = mysql_escape_string(hash_password($password));
	$sql = "SELECT `id` FROM `user` WHERE `username`='$username' AND `hash`='$hash'";
	$result = query($sql);
	while($row = mysql_fetch_row($result))
	{
		return 1;
	}
	return 0;
}


function apply_qualifiers($fields)
{
	/*
	The purpose of this function is to restrict the fields in a
	mysql result set by explicitly defining the fields to
	occur in the set. If ignore the whole set is used.
	*/
	
	global $not_set;
	
	//Count qualifiers (if any.)
	$qualifier_no = 0;
	$field_no = 0;
	foreach($fields as $value)
	{
		$field_no++;
		if($value != $not_set)
		{
			$qualifier_no++;
		}
	}

	//Apply qualifiers.
	if($qualifier_no != 0)
	{
		foreach($fields as $col => $value)
		{
			if($fields[$col] == $not_set)
			{
				unset($fields[$col]);
			}
		}
	}
	
	return $fields;
}

function chk_glob($identifiers, $super, $min_matches = "", $db_escape = 1)
{
	/*
		Using $_GET, $_POST, $_COOKIE, $_SERVER arrays are annoying
		because before you use them you have to check that the value
		exists. Then you have to escape it so there is a lot of code
		duplication and it's boring. There is also no succinct way of
		knowing whether the allocation was successful. This function
		solves that . . . and to stop duplication further it works on
		a list of idenitifers.
	*/
	global $not_set;
	
	$matches = 0;
	$identifiers = explode(",", $identifiers);
	$identifiers_no = count($identifiers);
	foreach($identifiers as $identifier)
	{
		global $$identifier;
		/*
		   Variable variables cannot be used with super globals hence
		   we copy it to a non-super global variable and free it.
		*/
		if(isset($super["$identifier"]))
		{
			/*
			Otherwise we will copy this into the global var and since
			NULL is used to signify the global var to be set wasn't
			set in the super global then we will get wrong results.
			*/
			if($super["$identifier"] == $not_set)
			{
				$super["$identifier"] = "";
			}
		}
		if($db_escape)
		{
			
			$$identifier = !isset($super["$identifier"]) ? $not_set : mysql_escape_string(urldecode($super["$identifier"]));
		}
		else
		{
			$$identifier = !isset($super["$identifier"]) ? $not_set : urldecode($super["$identifier"]);
		}
		
		if($$identifier != $not_set)
		{
			$matches++;
		}
	}
	if(eval(is_empty('$min_matches')))
	{
		if($matches != $identifiers_no)
		{
			return 0;
		}
		return $identifiers_no;
	}
	else
	{
		if($matches >= $min_matches)
		{
			return $matches;
		}
		return 0;
	}
}

function double_quote_escape($str)
{
	/*
	Security patch. This might stop them from potentially
	escaping the string limit and from there doing an XSS
	attack.
	*/
	$str = str_replace("\\", "", $str);
	$str = @ereg_replace("\"", "\\\"", $str);
	return $str;
}

function single_quote_escape($str)
{
	/*
	Security patch. This might stop them from potentially
	escaping the string limit and from there doing an XSS
	attack.
	*/
	$str = str_replace("\\", "", $str);
	$str = @ereg_replace('\'', '\\\'', $str);
	return $str;
}

function assoc_array_to_json($ar)
{
	/*
	Takes an array and outputs it as JSON.
	Array may contain other arrays. Have a play with it
	to see how it works. The JSON output is also pretty in that
	it is human readable. This function was coded because at the
	time PHP had no such function (or we didn't know about it) 
	and the API needed it.
	*/

	$json_name = double_quote_escape("at_json");
	$json = "var $json_name = {\r\n";
	$row_no = 0;
	$col_no = 0;
	$x = 0;
	$y = 0;
	foreach($ar as $value)
	{
		$row_no++;
	}
	foreach($ar as $row)
	{
		$json .= "\t\"" . double_quote_escape($y) . "\": {\r\n";
		
		$col_no = 0;
		$x = 0;
		foreach($row as $value)
		{
			$col_no++;
		}
		foreach($row as $col => $value)
		{
			$x++;
			$json .= "\t\t\"" . double_quote_escape($col) . "\": \"" . double_quote_escape($value);
			if($x == $col_no)
			{
				$json .= "\"\r\n";
			}
			else
			{
				$json .= "\",\r\n";
			}
		}
		if($y + 1 == $row_no)
		{
			$json .= "\t}\r\n";
		}
		else
		{
			$json .= "\t},\r\n";
		}
		$y++;
	}
	$json .= "};";
	return $json;
}

function mysql_result_to_assoc_array($result)
{
	/*
	Pulls a mysql row into an array.
	*/

	$ar = array("");
	$i = 0;
	while($row = mysql_fetch_assoc($result))
	{
		$ar[$i] = $row;
		$i++;
	}
	mysql_free_result($result);
	return $ar;
}

function get_user_id($username)
{
	/*
	Takes a username and return's it's ID in the username table.
	*/

	if(is_numeric($username))
	{
		return $username;
	}
	$sql = "SELECT `id` FROM `user` WHERE `username`='$username'";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		mysql_free_result($result);
		return $row["id"];
	}
	mysql_free_result($result);
	return "";
}

function get_username($user_id)
{
	/*
	Takes an ID for a user and returns their username from the user table.
	*/

	if(is_numeric($user_id) == 0)
	{
		return $user_id;
	}
	$sql = "SELECT `username` FROM `user` WHERE `id`='$user_id'";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		mysql_free_result($result);
		return $row["username"];
	}
	mysql_free_result($result);
	return "";
}

function rec_exist($table_name, $criteria)
{
	/*
	Returns 0 if $criteria is false for the table called $table_name.
	Otherwise returning the id for the item based on the criteria.
	*/

	$sql = "SELECT `id` FROM `$table_name` WHERE $criteria";
	$result = query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		mysql_free_result($result);
		return $row["id"];
	}
	mysql_free_result($result);
	return "0";
}

function clean_data($s)
{
	/*
	HUGE function. Basically, this function is used to "clean" the song titles,
	artists, genres, album names, and stuff like that from user's iPods. Typically
	they will have a track called #1 EminEM00-~`-love_the_way_u_lie.mp3 on their
	iPods rather than just Love The Way You Lie - Eminem. Cleaning up the data
	helps ensure song titles are all in a common format thereby saving space
	in the database and allowing songs to more easily be search-able.
	*/

	if(preg_match("/^[\s]*$/", $s))
	{
		$s = "Unknown";
	}
	// And symbol to have exactly 1 space before and after it.
	$s = preg_replace("/&+/", "&", $s);
	$s = preg_replace("/\s*&\s*/", " & ", $s);
	// Replace underscores with spaces.
	$s = preg_replace("/_/", " ", $s);
	// Remove track no.
	$s = preg_replace("/([-~]\s*(track\s*)?[0-9]+\s*[-~])|(track[^a-zA-Z]*?\s*[0-9]+)|(\s+[0-9]+\s+)/i", "", $s);
	// Only 1 space between tokens.
	$s = preg_replace("/\s+/", " ", $s);
	// Replace -dfsdf- etc with (sdfsdf)
	$s = preg_replace("/[-~=]([^\s][\s\S]*?[^\s])[-~=]/", "[(]$1[)]", $s);
	// Replace weird brackets.
	$s = preg_replace("/[\[{<]/", "(", $s);
	$s = preg_replace("/[\]}>]/", ")", $s);
	$s = preg_replace("/[(][)]/", "", $s);
	// make sure there are no unmatched brackets.
	$new_s = "";
	$open_p = 0;
	$len = strlen($s);
	for($i = 0; $i < $len; $i++)
	{
		if($s[$i] == "(" and $open_p)
		{
			continue;
		}
		if($s[$i] == ")" and !$open_p)
		{
			continue;
		}
		if($s[$i] == ")" and $open_p)
		{
			$open_p = 0;
		}
		if($s[$i] == "(")
		{
			if(strpos($s, ")") !== false)
			{
				$open_p = 1;
			}
			else
			{
				continue;
			}
		}
		$new_s .= $s[$i];
	}
	$s = $new_s;
	// Once space before an after a brace.
	$s = preg_replace("/\s*[(]\s*/", " (", $s);
	$s = preg_replace("/\s*[)]\s*/", ") ", $s);
	// Only 1 space, no space before or after it.
	$s = preg_replace('/[\\\\\/]+/', '/', $s);
	$s = preg_replace("/\/+/", "/", $s);
	$s = preg_replace("/\\+/", "\\", $s);
	$s = preg_replace("/\s*\/\s*/", "/", $s);
	$s = preg_replace("/[\s]*\\\\[\s]*/", "\\", $s);
	$s = " " . $s . " ";
	$s = preg_replace("/([^a-zA-Z]+[\\\\\/]+)|([\\\\\/]+[^a-zA-Z]+)/", "", $s);
	// Fix erronious use of some symbols.
	$s = preg_replace("/,+/", ",", $s);
	$s = preg_replace("/\s*,\s*/", ", ", $s);
	$s = preg_replace("/!+/", "!", $s);
	$s = preg_replace("/\s*!\s*/", "! ", $s);
	$s = preg_replace("/#/", "", $s);
	$s = preg_replace("/\s+[\.]+[\s\S]*?[\s]/", "", $s);
	// Replace abbreviations.
	$s = " " . $s . " ";
	$s = preg_replace("/[\s]+ver[.']?[\s]+/i", " Version ", $s);
	$s = preg_replace("/[\s]+vol[.']?[\s]+/i", " Volume ", $s);
	$s = preg_replace("/[\s]+ft[.']?[\s]+/i", " Featuring ", $s);
	$s = preg_replace("/[\s]+and[\s]+/i", " & ", $s);
	$s = preg_replace("/-/", " ", $s);
	// Only capitalize the first letter of a word
	// and acryonyms
	$s = " " . $s . " ";
	$words = explode(" ", $s);
	$x = 0;
	$old_word = "";
	$new_word = "";
	foreach($words as $word)
	{
		$old_word = $word;
		$new_word = "";
		$x = 0;
		$len = strlen($word);
		$i = 0;
		for($i = 0; $i < $len; $i++)
		{
			if($word[$i] == "(")
			{
				$new_word .= $word[$i];
				continue;
			}
			if($x == 0)
			{
				$x = 1;
				if(ctype_alpha($word[$i]))
				{
					$new_word .= strtoupper($word[$i]);
				}
				else
				{
					$new_word .= $word[$i];
				}
			}
			else
			{
				if(ctype_alpha($word[$i]))
				{
					$new_word .= strtolower($word[$i]);
				}
				else
				{
					$new_word .= $word[$i];
				}
			}
		}
		if(preg_match('/^[\\\\\/(]?([a-zA-Z][.])+([a-zA-Z])[\\\\\/)]?$/', $new_word))
		{
			$new_word = strtoupper($new_word);
		}
		$new_word = " " . $new_word . " ";
		$old_word = " " . $old_word . " ";
		$s = str_replace($old_word, $new_word, $s);
		$len = strlen($s);
		$ss = "";
		$i = 0;
	}
	$len = strlen($s);
	for($i = 0; $i < $len; $i++)
	{
		if(($i + 1) < ($len - 1))
		{
			if(($s[$i] == "/" || $s[$i] == "\\") && ctype_alpha($s[$i + 1]))
			{
				$s[$i] = "/";
				$s[$i + 1] = strtoupper($s[$i + 1]);
			}
		}
	}
	// Remove all spaces.
	$s = preg_replace("/[.]/", "", $s);
	$s = preg_replace("/^\s*([\s\S]*?)\s*$/", "$1", $s);
	$s = preg_replace("/\s+/", " ", $s);
	$s = preg_replace("/[|]/", " ", $s);
	$s = preg_replace("/[\r\n]/", "", $s);
	if(preg_match("/^[\s]*$/", $s))
	{
		$s = "Unknown";
	}
	// Remove leading numbers.
	$s = preg_replace("/^[0-9]+[\s\S]*\s/", "", $s);
	return $s;
	/*
	echo clean("");
	echo clean("  dfsdf");
	echo clean("sdfsd && 7sxdfsdf &dfg");
	echo clean("  dfsdf__sdfsdf_");
	echo clean("~ track1");
	echo clean("track 9");
	echo clean("  dfsdf");
	echo clean("  dfsdf");
	echo clean("	~   track  23423   ~");
	echo clean("	~   23423   ~");
	echo clean("	track#$%#$   456456456");
	echo clean("	  3453454  ");
	echo clean("sdfsdf   sdfsdf  d");
	echo clean("-sdfsdfsdf-");
	echo clean("~sdfsdf~");
	echo clean("=s345345345=");
	echo clean("[sdfsdf]");
	echo clean("{sdfsdf}");
	echo clean("<sdfsfsdf>");
	echo clean("(sfsdfsd (sdfsdf) (sdfsdf (sdfsdf sdf)))sdf ()()sdf");
	echo clean("sdfsdf  ( sdfsdf )   sdfsdf");
	echo clean("sdfsdf // sdfsdf sdf\ sdfsdf \\//");
	echo clean("sdfsdf,sdfsdfsd  , sdfsdf");
	echo clean("sdfsdf,,,sdfsdf ,sdfsd ,sdf");
	echo clean("sdfsdf~~!!sdfsdf");
	echo clean("sdfsdf ! sdfsdf !dsfsdf!");
	echo clean("sdfsdf#dfgsdfs##sdfsdf");
	echo clean("asdfasdf sdfasdfsadf .sdfasdfasdfasdf");
	echo clean("	sdfsdf Ver");
	echo clean("   sdfsdf vEr.");
	echo clean("   sdfsdf veR'");
	echo clean("   asdas   VER dfsdf");
	echo clean("	   sdfsdf Vol");
	echo clean("   sdfsdf vOl.");
	echo clean("   sdfsdf voL'");
	echo clean("   asdas   VOL dfsdf");
	echo clean("   adasds Ft sfsdf");
	echo clean("   asdasd fT. asdasd");
	echo clean("   asdasd ft' asdasd");
	echo clean("   asdasd FT asdasd");
	echo clean("   eminem and asdasd");
	echo clean("	sdfsdf - sdfsdf");
	echo clean("   dfgdfg. dfgdfg. ");
	echo clean("sfsdf dsdfd.sdfsdf. sdf.sdf.sdf. asfc/bdfh jsdr\efw");
	echo clean("d.d.d sdfsdf f.f. .f.f. SDFSDF D.D");
	echo clean("   sdfsdfsdf   ");
	echo clean("  sdfsdf	sdfsdfsdf	dsfsdf");
	echo clean(" sdfsdf | sdfsfdf");	
	*/
}

function add_tags()
{
	return 1;
}

function mysql_get($sql)
{
	/*
	Wrapper for select SQL. Reduces code duplication.
	*/

	$result = query($sql);
	if(mysql_num_rows($result) == 0)
	{
		return "";
	}
	return mysql_result_to_assoc_array($result);
}

function mysql_exist_ab($table_name, $fields)
{
	/*
	Wrapper for rec_exists. This function tasks an array of
	fields in the form field_name => field_value to check
	if it exists in a particular table. 
	E.g. select * from table where (foreach field . . . and)
	*/

	// Build criteria.
	$criteria = "";
	foreach($fields as $col => $value)
	{
		$criteria .= "`$col`='$value' AND ";
	}
	$criteria = substr($criteria, 0, strlen($criteria) - 5);
	
	// Check if it exists.
	$id = rec_exist($table_name, $criteria);
	return $id;
}

function mysql_insert_ab($table_name, $fields)
{
	/*
	Allows an array of field_name => value pairs to
	easily be inserted in a particular table.
	*/

	// Build insert statement.
	$sql = "INSERT INTO `$table_name` (";
	foreach($fields as $col => $value)
	{
		$sql .= "`$col`, ";
	}
	$sql = substr($sql, 0, strlen($sql) - 2);
	$sql .= ") VALUES (";
	foreach($fields as $value)
	{
		$sql .= "'$value', "; 
	}
	$sql = substr($sql, 0, strlen($sql) - 2);
	$sql .= ")";
	
	// Execute query.
	query($sql);
	
	// Store ID.
	$id = mysql_insert_id();
	return $id; 
}

function mysql_update_ab($table_name, $fields)
{
	/*
	Takes a list of field_name => value pairs and
	replaces them for a row in a table identified by
	the special field id => value.
	*/

	// Build SQL.
	$sql = "UPDATE `$table_name` SET ";
	foreach($fields as $col => $value)
	{
		if($col != "id")
		{
			$sql .= "`$col`='$value', ";
		}
	}
	$sql = substr($sql, 0, strlen($sql) - 2);
	$sql .= " WHERE `id`='" . $fields["id"] . "'";
	
	// Execute query.
	query($sql);
	// echo "<p>$sql<p>";
	return $fields["id"];
}

function mysql_get_ab($table_name, $fields, $criteria="")
{
	/*
	Mysql SELECT (for each fields) WHERE criteria = criteria
	Just an abstraction.
	*/

	// Build SQL.
	$sql = "SELECT ";
	foreach($fields as $col => $value)
	{
		$sql .= "`$col`,";
	}
	$sql = substr($sql, 0, strlen($sql) - 1);
	$sql .= " FROM `$table_name`";
	if(!eval(is_empty('$criteria')))
	{
		$sql .= " WHERE $criteria";   
	}

	// Execute and return result.
	$result = mysql_get($sql);
	return $result;
}


function update_music_totals($id, $amount, $op)
{
	/*
	Updates the music totals for a particular music record for the amount specified.
	Operation may be either addition or subtraction.
	*/
	if($amount < 1 or $amount > 100)
	{
		return "";
	}
	$sql = "UPDATE `music` SET `total_rating`=`total_rating` $op $amount, `total_rater`=`total_rater` $op 1 WHERE `id`='$id'";
	query($sql);
	if($amount <= 55)
	{
		$sql = "UPDATE `music` SET `total_dislike`=`total_dislike` $op 1 WHERE `id`='$id'";
		query($sql);
	}
	else
	{
		$sql = "UPDATE `music` SET `total_like`=`total_like` $op 1 WHERE `id`='$id'";
		query($sql);
	}
	return $id;
}
