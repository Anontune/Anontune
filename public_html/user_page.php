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
 
/*
	INSECURE, USE HASHES
	USE API KEYS
	USE SESSIONS
	fix upcase uname glitch
*/
require_once("global.php");

$username = isset($_GET["username"]) ? urldecode($_GET["username"]) : "";
$username_s = htmlspecialchars($username);

$auth_username = isset($_COOKIE["auth_username"]) ? $_COOKIE["auth_username"] : "";
$auth_username_s = htmlspecialchars($auth_username);
$auth_password = isset($_COOKIE["auth_password"]) ? $_COOKIE["auth_password"] : "";
$auth_password_s = htmlspecialchars($auth_password);
if($username == $auth_username && $username != "" && $auth_username != "")
{
	$is_authed = 1;
}
else
{
	$is_authed = 0;
}
?>
<html>
<head>
<title>Anontune - <?php echo $username_s; ?></title>

<<<<<<< HEAD
<link href="favicon.ico" rel="SHORTCUT ICON">
<link href='http://fonts.googleapis.com/css?family=Paytone+One|Cantarell:400,700|PT+Sans+Caption:400,700' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="/js/prettify.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script type="text/javascript" src="/js/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="http://www.youtube.com/iframe_api"></script>


=======
<style>
html, body
{
	margin: 0px;
	padding: 0px;
	border: 0px;
	height: 100%;
}
body
{
	background: #F7F8F9;
}
#container
{
	width: 99%;
	position: relative;
	top: 5px;
	margin-left:auto;
	margin-right:auto;
}
#ads
{
	position: relative;
	top: 45px;
}
#nav
{
	position: absolute;
	top: 0px;
	padding: 0px;
	border: 0px;
	margin: 0px;
	width: 100%;
	height: 35px;
	background: black;
	text-shadow: 0 1px 0 #FFFFFF;
}
.nav-link, .nav-link a:link, .nav-link a:active, .nav-link a:hover, .nav-link a:visited
{
	border: 0px;
	color: white;
	line-height: 35px;
	dispay: bock;
	float: left;
	padding-left: 8px; padding-right: 8px;
	text-decoration: none;
	font-size: 14px;
	font-family: sans-serif;
}
#footer
{
	position: absolute;
	padding-top: 10px;
	padding-bottom: 10px;
	padding-left: 0px;
	padding-right: 0px;
	margin: 0px;
	bottom: 0px;
	font-size: 18;
	width: 100%;
	text-align: center;
}

#q_field
{
	background: #7d7e7d; /* Old browsers */
   
	
height: 20px; 
font-size: 12px;
margin-top: 8px;
margin-right: 5px;
width: 250px;
border: 0px;
color: white;
}

</style>
<script type="text/javascript" src="/player/swfobject.js"></script>
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
<script src="/netjs/netjs.js"></script>
<script src="/player/diff_match_patch.js"></script>
<script src="/player/xml2json.js"></script>
<script src="/js/date.format.js"></script>
<script>

<<<<<<< HEAD


=======
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
//alert(document.cookie);

function c_test(){
	alert("h0h0h0");
}
function get_netjs(){
	if(netjs_ready){
		netjs.applet = document.getElementById("netjs_applet");
		//Create thread to handle socket functions.
		//var tran_name = "netjs_http" + Math.random() + "\0";
		//netjs.tran_name = tran_name;
		//api_call = netjs.applet.get_api_call_obj();
		//alert(api_call);
		//api_call.cname = "create_transaction\0";
		//api_call.transaction_name = tran_name;
		//api_call.use_multicast = 600;
		//netjs.applet.proc_queue_add(api_call);
		//alert(name);
		//alert(netjs.applet.get_transaction_index_by_name(name));
		//alert(netjs.applet.get_transaction_index_by_name);
		//alert(netjs.applet.get_api_call_obj);	
		//tran = netjs.applet.get_transaction_index_by_name(tran_name);
		//alert(tran);
		return;
	}
	setTimeout(get_netjs, 1000);
}

function at_player_ready(){
<<<<<<< HEAD
	//setInterval(at.me.output_tiles, 2000);
	at.skin = "troll";
=======
	setInterval(at.me.output_tiles, 2000);
	at.skin = "default";
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
	var skin_code_url = "<?php echo $this_root_url . '/player/skins/'; ?>" + at.skin + "/main.js";
	//alert(skin_code_url);
	//alert(at.http_get);
	//return;
	var skin_code = at.http_get(skin_code_url);
	if(skin_code == false){
		alert("Failed to load skin code.");
		return false;
	}
	eval(skin_code);
	//alert(at.player.skin);
	//alert(at.player.skin);
	at.player.skin.main();
	//alert(skin_code);
	
	//Open first playlist.
<<<<<<< HEAD
	/*
=======
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
	if(at.pls.length){
		at.player.skin.load_playlist(0);
		at.player.skin.load_playlists();	
		at.player.skin.highlight_main('atp-playlistc0', 1);
	}
<<<<<<< HEAD
	*/
=======
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
}

function prepare(){
	//Initialize netjs.
	//setTimeout(get_netjs, 1000);
	//netjs.applet = document.getElementById("netjs_applet");
	
	/*
This is just because all my regex often only works against
the HTML output dynamically generated for my development browser.
Don't remove it.
	*/
	//netjs.http.user_agent = "Mozilla/5.0 (X11; Linux x86_64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1";
	
	//Initialize diff engine.
	//dmp = new diff_match_patch();
<<<<<<< HEAD
}
</script>
=======
	
	//Set height of container.
	var body = document.body,
	html = document.documentElement;
	var height = Math.max( body.scrollHeight, body.offsetHeight, 
	html.clientHeight, html.scrollHeight, html.offsetHeight );
	height = parseInt(height); //0.90 * height
	container = document.getElementById("container");
	container.style.height = height - (document.getElementById("nav").offsetHeight + 60);
}
</script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
<script type="text/javascript" src="/player/variables.php?username=<?php echo(urlencode($username)); ?>"></script>
<script type="text/javascript" src="/player/player.js"></script>
<script type="text/javascript">
$(function(){
<<<<<<< HEAD
	//prepare();
	at.player.prepare();
	//$('#loading').hide();
});
</script>
<script>
$(document).ready(function(){
	//Init jplayer.
	$("#jplayer").jPlayer({solution: "html, flash", loop: false, swfPath: "/js"});
	//alert($("#jplayer").jPlayer());
	
	//$.jPlayer.event.ended
	//jPlayer.event.error
	/*
      $("#jplayer").jPlayer({
        ready: function() {
          $(this).jPlayer("setMedia", {
            mp3: "http://www.google.com/sdfsdf"
          }).jPlayer("play");
          var click = document.ontouchstart === undefined ? 'click' : 'touchstart';
          var kickoff = function () {
            $("#jplayer").jPlayer("play");
            document.documentElement.removeEventListener(click, kickoff, true);
          };
          document.documentElement.addEventListener(click, kickoff, true);
        },
        solution: "flash",
        loop: true,
        swfPath: "/js"
      });
	*/
	/*
	$("#jplayer").bind($.jPlayer.event.error, function(event) {
		alert(event.jPlayer.error.type);
	});
	*/
=======
	prepare();
	at.player.prepare();
	$('#loading').hide();
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
});
</script>
</head>
<body>
<<<<<<< HEAD
<div class="nav_top">
<div class="nav_top_wrapper">
<a href="/">Home</a>
<a href="#" onclick="at.player.skin.about();">About</a>
<a href="/forum/">Forum</a>
<?php
if($is_authed){
	echo "<a href='/login.php?action=logout'>Logout</a>";
}
else
{
	echo "<a href='/login/'>Login</a>";
=======
<div id="nav">
<a href="/" class="nav-link">Anontune</a>
<?php
if($is_authed)
{
	echo '<a href="/09/04/2012/import-ipod/" class="nav-link">Import</a>';
	echo '<a href="/login.php?action=logout" class="nav-link">Logout</a>';
}
else
{
	echo '<a href="/at-login-2/" class="nav-link">Login</a>';
	echo '<a href="/at-register/" class="nav-link">Register</a>';  
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
}
?>
<a href="/register/">Register</a>
<a href="/contribute/">Contribute</a>
<a href="/contact/">Contact</a>
<a href="/tools/">Tools</a>
<a href="#" onclick="at.player.skin.help();">Help</a>
<a href="#" onclick="at.player.skin.license();">License</a>
<?php
	echo "<a href='#'>" . $username_s . "</a>";
?>
<div class="date"></div>
</div>
<<<<<<< HEAD
</div>
=======
<center id="loading">
<div style="position: absolute; top: 100px; width: 95%;">
<center>
Loading . . . Accept all security warnings.<br>
Java NOT required. Firefox recommended.<br>
</center>
</div>
<div id="container"></div>
<!--<div id="footer">&copy; 2012 Anontune - All rights reserved</div>-->
</center>
>>>>>>> 043cf38bbe9f10a89b03465ee18f220ba08d039d
</body>
</html>
