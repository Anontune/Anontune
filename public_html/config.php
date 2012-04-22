<?php
$not_set = "v0id";
$xmlstr = @file_get_contents(dirname(__file__) . DIRECTORY_SEPARATOR . "../config.xml");
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
?>