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

$auth_username = isset($_SESSION["auth_username"]) ? $_SESSION["auth_username"] : "";
$auth_username_s = htmlspecialchars($auth_username);
$auth_password = isset($_SESSION["auth_password"]) ? $_SESSION["auth_password"] : "";
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

<link href="favicon.ico" rel="SHORTCUT ICON">
<link href='http://fonts.googleapis.com/css?family=Paytone+One|Cantarell:400,700|PT+Sans+Caption:400,700' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="/js/prettify.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script type="text/javascript" src="/js/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="http://www.youtube.com/iframe_api"></script>


<script src="/netjs/netjs.js"></script>
<script src="/player/diff_match_patch.js"></script>
<script src="/player/xml2json.js"></script>
<script src="/js/date.format.js"></script>
<script>



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
	//setInterval(at.me.output_tiles, 2000);
	at.skin = "troll";
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
	/*
	if(at.pls.length){
		at.player.skin.load_playlist(0);
		at.player.skin.load_playlists();	
		at.player.skin.highlight_main('atp-playlistc0', 1);
	}
	*/
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
}
</script>
<script type="text/javascript" src="/player/variables.php?username=<?php echo(urlencode($username)); ?>"></script>
<script type="text/javascript" src="/player/player.js"></script>
<script type="text/javascript">
$(function(){
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
	$(window).resize(function() {
	  if(ytplayer !== null){
			$("#ytplayer").height($(".play_view").height() - 90);
			$("#ytplayer").width($(".play_view").width() - 200);
		}
	});
});
</script>
</head>
<body>
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
</div>
</body>
</html>
