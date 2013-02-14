<?php

/*
What is stopping client-side code from running and stealing the token?
VBScript, for example? Think of a security measure for this.

Todo: Session checking not verified working yet.
Todo: Unit tests.
Todo: Make standalone module.
*/

$_ANONTUNE = true;
//require_once("../global.php");
//require_once("mysql_con.php");
//require_once("function.php");
session_start(); 

//setcookie("abc", "hax", time()+3600);

//Cleanup old tokens.
delete_expired_tokens();
delete_used_tokens();

function unit_test()
{
	
}

function install()
{
	global $mysql_con;
	$sql = <<<EOD
	CREATE TABLE IF NOT EXISTS `auth_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(50) NOT NULL DEFAULT '0',
  `timestamp` bigint(8) NOT NULL DEFAULT '0',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `user_agent` varchar(8192) NOT NULL DEFAULT '0',
  `uses` int(11) NOT NULL DEFAULT '1',
  `token` varchar(40) NOT NULL DEFAULT '0',
  `referer` varchar(3000) NOT NULL DEFAULT '0',
  `auth_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `cookie_name` varchar(50) NOT NULL DEFAULT '0',
  `cookie_value` varchar(1024) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;
EOD;

	//Execute query.
	$result = mysql_query($sql, $mysql_con);
	if(!$result)
	{
		$error  = "Unable to install auth_token table.<br>";
		$error .= "Table might already exist.<br>";
		$error .= mysql_error();
		die($error);
	}
	else
	{
		$success = "Successfully installed auth_token table.";
		die($success);
	}
}

function create_token($options)
{
	global $mysql_con;
	
	//Check authentication.
	if(!check_credential($options["auth_username"], $options["auth_password"]))
	{
		//Login failed.
		return -1;
	}
	$auth_user_id = get_user_id(mysql_escape_string($options["auth_username"]));
	$options["auth_user_id"] = $auth_user_id;
	
	//Generate secure token.
	$charset  = "abcdefghijklmnopqrstuvwxyz";
	$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$charset .= "0123456789-._~";
	$charset_len = strlen($charset);
	$token = ""; 
	$token_len = 35;
	for($i = 0; $i < $token_len; $i++)
	{
		$token .= $charset[rand(0, $charset_len - 1)];
	}
	$options["token"] = $token;
	//Todo: There's a small chance the token won't be unique.
	//Loop until it's unique.	
		
	//Store current timestamp.
	$timestamp = time();
	$options["timestamp"] = $timestamp;
	
	//Parse expiry.
	if(!is_numeric($options["expiry"]))
	{
		//Malformed expiry.
		return -1;
	}
	//                                               Seconds in a year.
	if($options["expiry"] < 1 || $options["expiry"] > (365 * 24 * 60 * 60))
	{
		//Malformed expiry.
		return -1;
	}
	
	//Parse uses.
	if(!is_numeric($options["uses"]))
	{
		//Malformed uses.
		return -1;
	}
	
	//Parse IP address.
	if($options["ip_addr"] == "this")
	{
		if(isset($_SERVER['REMOTE_ADDR']))
		{
			if(empty($_SERVER['REMOTE_ADDR']))
			{
				//IP address empty.
				$options["ip_addr"] = "0";
				//return -1;
			}
			else
			{
				$options["ip_addr"] = $_SERVER['REMOTE_ADDR'];
			}
		}
		else
		{
			//return -1;
			$options["ip_addr"] = "0";
		}
	}
	
	//Parse session ID.
	$ses_id = session_id();
	//echo $ses_id . "<br>";
	if($options["session_id"] == "this")
	{
		if(empty($ses_id))
		{
			//No session found.
			$options["session_id"] = "0";
			//return -1;
		}
		else
		{
			//Session wasn't logged in.
			$options["session_id"] = "0";
			
			//Todo: add auth username to session
			if(isset($_SESSION["auth_username"]))
			{
				if($_SESSION["auth_username"] == $options["auth_username"])
				{
					$options["session_id"] = $ses_id;
				}
			}
		}
	}
	
	//Parse user-agent.
	if($options["user_agent"] == "this")
	{
		if(isset($_SERVER["HTTP_USER_AGENT"]))
		{
			if(empty($_SERVER["HTTP_USER_AGENT"]))
			{
				//User-agent was empty.
				$options["user_agent"] = "0";
				//return -1;
			}
			else
			{
				$options["user_agent"] = $_SERVER["HTTP_USER_AGENT"];
			}
		}
		else
		{
			//User-agent wasn't set.
			$options["user_agent"] = "0";
			//return -1;
		}
	}
	
	//Parse referer.
	if($options["referer"] == "this")
	{
		if(isset($_SERVER["HTTP_REFERER"]))
		{
			if(empty($_SERVER["HTTP_REFERER"]))
			{
				//HTTP referer was empty.
				$options["referer"] = "0";
			}
			else
			{
				$options["referer"] = $_SERVER["HTTP_REFERER"];
			}
		}
		else
		{
			//HTTP referer wasn't set.
			$options["referer"] = "0";
		}
	}
	
	//Parse cookie.
	if($options["cookie_name"] != "0")
	{
		if($options["cookie_value"] == "this")
		{
			if(isset($_COOKIE[$options["cookie_name"]]))
			{
				$options["cookie_value"] = $_COOKIE[$options["cookie_name"]];
			}
		}
	}
	
	//Make options safe.
	$options["ip_addr"] = mysql_real_escape_string($options["ip_addr"]);
	$options["timestamp"] = mysql_real_escape_string($options["timestamp"]);
	$options["expiry"] = mysql_real_escape_string($options["expiry"]);
	$options["session_id"] = mysql_real_escape_string($options["session_id"]);
	$options["user_agent"] = mysql_real_escape_string($options["user_agent"]);
	$options["uses"] = mysql_real_escape_string($options["uses"]);
	$options["token"] = mysql_real_escape_string($options["token"]);
	$options["referer"] = mysql_real_escape_string($options["referer"]);
	$options["auth_user_id"] = mysql_real_escape_string($options["auth_user_id"]);
	$options["cookie_name"] = mysql_real_escape_string($options["cookie_name"]);
	$options["cookie_value"] = mysql_real_escape_string($options["cookie_value"]);

	 //Create SQL.
	$sql  = "INSERT INTO `auth_token` (`ip_addr`, `timestamp`, `expiry`, `session_id`,";
	$sql .= "`user_agent`, `uses`, `token`, `referer`, `auth_user_id`, `cookie_name`,";
	$sql .= " `cookie_value`) VALUES ('%s', %d, %d, '%s', '%s', %d, '%s', '%s', %d, '%s', '%s')";
	$sql  = sprintf($sql, $options["ip_addr"], $options["timestamp"], $options["expiry"],
	$options["session_id"], $options["user_agent"], $options["uses"], $options["token"],
	$options["referer"], $options["auth_user_id"], $options["cookie_name"],
	$options["cookie_value"]);

	//Execute query.
	$result = mysql_query($sql, $mysql_con);
	if(!$result)
	{
		//Invalid SQL query.
		return -1;
		//die('Invalid query: ' . mysql_error());
	}
	
	//Return auth token!
	return $token;
}

function delete_expired_tokens()
{
	$sql = "DELETE FROM `auth_token` WHERE %s >= `timestamp` + `expiry`";
	$sql = sprintf($sql, mysql_real_escape_string(time()));
	$result = query($sql);
}

function delete_used_tokens()
{
	$sql = "DELETE FROM `auth_token` WHERE `uses` = 0";
	$result = query($sql);
}

function delete_token($token)
{
	global $mysql_con;
	
	//Sanity check.
	if(empty($token)) return;
	
	//Delete token.
	$sql = "DELETE FROM `auth_token` WHERE token='%s'";
	$sql = sprintf($sql, mysql_real_escape_string($token));
	$result = query($sql);
}

function verify_token($token)
{
	global $mysql_con;
	
	//Load token row.
	$sql = "SELECT * FROM `auth_token` WHERE token='%s'";
	$sql = sprintf($sql, mysql_real_escape_string($token));
	$result = query($sql);
	if(mysql_num_rows($result) == 0)
	{
		//Token doesn't exist.
		return -1;
	}
	$row = mysql_result_to_assoc_array($result);
	$row = $row[0];
	
	//Check uses.
	if($row["uses"] == 0)
	{
		//Token is used.
		delete_token($token);
		return -1;
	}
	
	//Check expiry.
	if(time() >= $row["timestamp"] + $row["expiry"])
	{
		//Token has expired.
		delete_token($token);
		return -1;
	}

	//Check IP address.
	if($row["ip_addr"] != "0" && !empty($row["ip_addr"]))
	{
		if(!isset($_SERVER["REMOTE_ADDR"])) return -1;

		//Invalid IP address.
		if($row["ip_addr"] != $_SERVER["REMOTE_ADDR"])
		{
			return -1;
		}
	}

	//Check session ID.
	if($row["session_id"] != "0" && !empty($row["session_id"]))
	{
		$ses_id = session_id();
		if(empty($ses_id)) return -1;

		//Invalid session ID.
		if($ses_id != $row["session_id"])
		{
			return -1;
		}
	}

	//Check user-agent.
	if($row["user_agent"] != "0" && !empty($row["user_agent"]))
	{
		if(!isset($_SERVER["HTTP_USER_AGENT"])) return -1;

		//Invalid user-agent.
		if($row["user_agent"] != $_SERVER["HTTP_USER_AGENT"])
		{
			return -1;
		}
	}

	//Check referer.
	if($row["referer"] != "0" && !empty($row["referer"]))
	{
		if(!isset($_SERVER["HTTP_REFERER"])) return -1;
		
		//Invalid referer.
		if($row["referer"] != $_SERVER["HTTP_REFERER"])
		{
			return -1;
		}	
	}
			
	//Check cookie.
	if($row["cookie_value"] != "0" && !empty($row["cookie_value"]))
	{
		if(!isset($_COOKIE[$row["cookie_name"]])) return -1;
		
		//Invalid cookie.
		if($_COOKIE[$row["cookie_name"]] != $row["cookie_value"])
		{
			return -1;
		}
	}
	
	//decrease uses if uses greater than 0.
	if($row["uses"] >= 1)
	{
		$sql = "UPDATE `auth_token` SET `uses`=`uses` - 1 WHERE `token`='%s'";
		$sql = sprintf($sql, mysql_real_escape_string($token));
		$result = mysql_query($sql, $mysql_con);
		if(!$result)
		{
			//Invalid SQL query.
			return -1;
		}
	}
	return 1;
}

function test()
{
	//install();
	delete_expired_tokens();
	
	return;
	$options = "";
	$options["ip_addr"] = "this";
	$options["expiry"] = "2";
	$options["session_id"] = "this";
	$options["user_agent"] = "this";
	$options["uses"] = "5";
	$options["referer"] = "this";
	$options["cookie_name"] = "abc";
	$options["cookie_value"] = "this";
	$options["auth_username"] = "sirboxan0n";
	$options["auth_password"] = "s3cur3!@#";
	//echo create_token($options);
	
	echo verify_token("ZIQBYwaeoZDHfp1z~6Hpp6QJ-SWmIYSutvV");
}

test();
//cleanup();

?>
