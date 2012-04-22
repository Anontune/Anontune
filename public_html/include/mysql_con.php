<?php
#Connect to DB.
$mysql_con = mysql_connect($config->database->host, $config->database->username, $config->database->password);
if($mysql_con == FALSE)
{
    die("Failed to connect ot DB");
}

#Select database.
if(mysql_select_db($config->database->name, $mysql_con) == FALSE) die("unable to select DB");
?>