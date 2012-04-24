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
 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "../../../global.php");
$img_path = $this_root_url . "/../../images/";
/*
Optimize player loading time . . . it's probably the height adjustment code.
* Inconsistently named API calls, player functions skin functions
* Use of NULL
*/
?>

/***
*    begin skin
*    begin skin
*/
skin = new function(){
    

this.ytplayer_width = 298;
this.ytplayer_height = 150;



this.output_ytplayer = function(vid){
    //Delete old song.
    at.me.reset_music_container();
    
    //Set id.
    var id = "atp-main-middle-music-content-yt";

    //Clear old API search call container.
    at.clear_element("atp-ytapi");
    
    //Create flash player object.
    var params = {allowScriptAccess: "always"};
    var atts = {"id": id};
    var url = "http://www.youtube.com/e/" + encodeURIComponent(vid) + "?enablejsapi=1&playerapiid=" + encodeURIComponent(id);
    var width = at.player.skin.ytplayer_width;
    var height = document.getElementById("atp-main-middle-music-content").offsetHeight;
    //alert(height);
    swfobject.embedSWF(url, id, width, height, "8", null, null, params, atts);
    
    //Apply styles.
    if(navigator.userAgent.indexOf("MSIE") != -1){
        document.getElementById(id).setAttribute("className", "atp-music-tab");
    }
    else{
        document.getElementById(id).setAttribute("class", "atp-music-tab");
    }
    document.getElementById(id).style.height = height;    
}

this.load_playlist = function(index){ 
    var id = at.pls[index]["id"];

    at.player.load_pl({"id": id, "title": "1", "artist_name": "1"});
    
    //Clear active track.
    if(at.pl_i != index){
        at.track_i = null;
    }
    
    //Change active container.
    at.pl_i = index;

	html = "";
	html += '<div style="width: 100%; height: 20px; padding: 5px; overflow: hidden; border: 0px;"><a href="#atplayer" onclick="at.player.skin.load_playlists();">Back</a></div>';
	for(i = 0; i < at.pls[at.pl_i]["tracks"].length; i++){
		html += '<div style="width: 100%; height: 20px; padding: 5px; overflow: hidden; border: 0px;"><a href="#atplayer" onclick="at.me.play_track(' + i + ', ' + at.pl_i + ');">' + at.htmlspecialchars(at.pls[at.pl_i]["tracks"][i]["title"]) + ' - ' + at.htmlspecialchars(at.pls[at.pl_i]["tracks"][i]["artist_name"]) + '</a></div>';
	}
	document.getElementById("atp-main-middle-nav").innerHTML = html;
	return;
};



this.load_playlists = function(){
    at.player.load_pls();

	html = "";
	for(i = 0; i < at.pls.length; i++){
		html += '<div style="width: 100%; height: 20px; padding: 5px; overflow: hidden; border: 0px;"><a href="#atplayer" onclick="at.player.skin.load_playlist(' + i + ');">' + at.htmlspecialchars(at.pls[i]["name"]) + '</a></div>';
	}
	document.getElementById("atp-main-middle-nav").innerHTML = html;
}

this.main = function(){
    at.player.output_ytplayer = at.player.skin.output_ytplayer;
    var skin_root_url = "<?php echo $this_root_url . '/'; ?>";
    
    //Apply CSS
    var css = at.http_get(skin_root_url + "style.css");
    //alert(css);
    //alert(skin_root_url);
    if(css == false){
        alert("Unable to load skin CSS.");
        return;
    }
    var style = document.createElement("style");
    style.setAttribute("type", "text/css");
    if(style.styleSheet){// IE
        style.styleSheet.cssText = css;
    }
    else {// w3c
        var cssText = document.createTextNode(css);
        style.appendChild(cssText);
    }
    document.getElementsByTagName("head")[0].appendChild(style);

    //Apply HTML.
    var html = at.http_get(skin_root_url + "gui.php");
    if(html == false){
        alert("Unable to load interface HTML.");
        return;
    }
    var scripts = document.getElementsByTagName('script');
    var this_parent = scripts[scripts.length - 1].parentNode;
    var container_height = this_parent.offsetHeight;
    var container_width = this_parent.offsetWidth;
    //this_parent.innerHTML = html + this_parent.innerHTML;
	document.getElementById("at-container").innerHTML = html;
    
	at.player.skin.load_playlists();
}

};
/*
*    end skin
*    end skin
***/


//Load skin code into at.player.skin.namespace.
//Very important. This is evaled. Under at.player scope.
at.player.skin = skin;
