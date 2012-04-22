<?php
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
    font-size: 19px;
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
<script src="/netjs/netjs.js"></script>
<script src="/player/diff_match_patch.js"></script>
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
    setInterval(at.me.output_tiles, 2000);
    at.skin = "default";
    var skin_code_url = "<?php echo $this_root_url . '/player/skins/'; ?>" + at.skin + "/main.php";
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
    if(at.pls.length){
        at.player.skin.load_playlist(0);
        at.player.skin.load_playlists();    
        at.player.skin.highlight_main('atp-playlistc0', 1);
    }
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
    
    //Set height of container.
    var body = document.body,
    html = document.documentElement;
    var height = Math.max( body.scrollHeight, body.offsetHeight, 
    html.clientHeight, html.scrollHeight, html.offsetHeight );
    height = parseInt(height); //0.90 * height
    container = document.getElementById("container");
    container.style.height = height - (document.getElementById("nav").offsetHeight + 60);
    
    //Create player.
    <?php
        $s_src = "/player/player.php?username=" . urlencode($username);
        /*
        if($auth_username != "")
        {
            $s_src = $s_src . "&auth_username=" . urlencode($auth_username);
        }
        if($auth_password != "")
        {
            $s_src = $s_src . "&auth_password=" . urlencode($auth_password);
        }*/
    ?>
    var s_src = "<?php echo $s_src; ?>";
    var script = document.createElement("script");
    script.src = s_src;
    container.appendChild(script);
        
 
}
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30429650-1']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body onload="prepare();">
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
}
?>

<a href="#" class="nav-link" style="float: right; border-right: 0px;"><?php echo $username_s; ?></a>
<a href="#" onclick="at.search_filter();"><img src="/search.png" style="margin-right: 10px; padding-top: 5px; float: right;"></a>
<form name="search" style="float: right; margin: 0px; padding: 0px; " onsubmit="at.search_filter(); return false;"/>
<input type="text" name="q" id="q_field">
</form>
</div>
<center>
<div style="position: absolute; top: 100px; width: 95%;">
<center>
Loading . . . Accept all security warnings.<br>
Java NOT required. Firefox recommended.<br>
</center>
</div>
<div id="ads">
<!--
 Begin: adBrite, Generated: 2012-03-04 18:03:34  
<script type="text/javascript">
var AdBrite_Title_Color = '0000FF';
var AdBrite_Text_Color = '000000';
var AdBrite_Background_Color = 'FFFFFF';
var AdBrite_Border_Color = 'CCCCCC';
var AdBrite_URL_Color = '008000';
try{var AdBrite_Iframe=window.top!=window.self?2:1;var AdBrite_Referrer=document.referrer==''?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);}catch(e){var AdBrite_Iframe='';var AdBrite_Referrer='';}
</script>
<span><script type="text/javascript">document.write(String.fromCharCode(60,83,67,82,73,80,84));document.write(' src="http://ads.adbrite.com/mb/text_group.php?sid=2112221&zs=3732385f3930&ifr='+AdBrite_Iframe+'&ref='+AdBrite_Referrer+'" type="text/javascript">');document.write(String.fromCharCode(60,47,83,67,82,73,80,84,62));</script>
<a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=2112221&afsid=1"><img src="http://files.adbrite.com/mb/images/adbrite-your-ad-here-leaderboard.gif" style="background-color:#CCCCCC;border:none;padding:0;margin:0;" alt="Your Ad Here" width="14" height="90" border="0" /></a></span>

</div>-->
<div id="container"></div>
<!--<div id="footer">&copy; 2012 Anontune - All rights reserved</div>-->
</center>
</body>
</html>