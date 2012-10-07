/*
The Anontune player's code.

The purpose of this code is to load and manipulate playlists of tracks in
memory. It's organization looks like this:

at - General namespace. Functions not specifically related to the player are here.
at.api - Contains all stuff that interacts with the JSON API or api.php.
at.player - Contains all the player related functions.
at.player.add_track - Adds a track to a playlist.
at.player.del_track - Delete a track from a playlist.
at.player.edit_track - Edit track information for an existing track in a playlist.
at.player.play_track - Don't use this.
at.player.track_ended - Called when a track ends so at.player.next_track can be called.
at.player.open_pl - Updates path information. In the future playlists will be hierarchical.
at.player.add_pl - Add a new playlist.
at.player.del_pl - Delete an existing playlist.
at.player.edit_pl - Edit an existing playlist.
at.player.load_pls - Loads all the playlists for a user into memory.
at.player.load_pl - Loads all the track information for a playlist into memory.
at.player.next_track - Play the next track in a playlist relative to the currently active track.
at.player.prev_track - Play the previous track in a playlist relative to the currently active track.
at.player.shuffle - Shuffle all the tracks in the active playlist.
at.player.new_pl - Create a structure to hold a playlist.
at.player.new_track - Create a structure to hold a track.
at.skin - Skin namespace. All of the skin's code will be available through this namespace.

Those aren't all the functions in this module but the main ones.

Problems:
* Because the API isn't asynchronous when the API is used it locks up the menu. I propose we queue
all such API calls and have the interface update instantly regardless of what happens on the
server.
* Reorganize the code so it is clearer.
*/
json_string = null;
dmp = new diff_match_patch();
X2JS = new X2JS();
function htmlspecialchars_decode (string, quote_style) {
    // http://kevin.vanzonneveld.net
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Mateusz "loonquawl" Zalega
    // +      input by: ReverseSyntax
    // +      input by: Slawomir Kaniecki
    // +      input by: Scott Cariss
    // +      input by: Francois
    // +   bugfixed by: Onno Marsman
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // +      input by: Mailfaker (http://www.weedem.fr/)
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
    // *     returns 1: '<p>this -> &quot;</p>'
    // *     example 2: htmlspecialchars_decode("&amp;quot;");
    // *     returns 2: '&quot;'
    var optTemp = 0,
        i = 0,
        noquotes = false;
    if (typeof quote_style === 'undefined') {
        quote_style = 2;
    }
    string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE': 1,
        'ENT_HTML_QUOTE_DOUBLE': 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i = 0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
        // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
    }
    if (!noquotes) {
        string = string.replace(/&quot;/g, '"');
    }
    // Put this in last place to avoid escape being double-decoded
    string = string.replace(/&amp;/g, '&');

    return string;
}

ytplayer = null;
function onPlayerReady(event){
	at.player.hook_youtube();
}

function onPlayerStateChange(event){
    if(event.data == YT.PlayerState.ENDED){
		at.player.track_ended();
	}
	if(event.data == YT.PlayerState.PLAYING){
		if(at.me.peh != null){
			clearTimeout(at.me.peh);
			at.me.peh = null;
		}
		at.player.skin.enable_play();
	}
	if(event.data == YT.PlayerState.PAUSED){
		at.player.skin.disable_play();
	}
}

function onPlayerError(event){
	//Try next result.
	at.player.skin.auto_play(null);
}


/***
*    begin anontune
*    begin anontune
*/
at = new function(){

//Note: These vars are all public for now.
//Program state.
//Touch if you know what you're doing.
//Stores results from at.api functions.
this.json = null;
//Allow Anontune Java applets to run.
//Good place to disable it if you have security issues :).
this.enable_java = 1;
//OWner of current user page.
this.username = var_username;
//alert(this.username);
//Username we execute at.api calls under.
this.auth_username = var_auth_username;
//Password we execute at.api calls under. This allows making calls with more privileged accounts.
//todo: WARNING Insecure, use cookies.
this.auth_password = var_auth_password;
//Autoplay opened playlist?
this.autoplay = true;
//Will a track before the first or after last wrap around playback?
this.loop = false;
//Playlist. Used to index a playlist in playlists.
this.pl_i = null;
//Parent playlist index.
this.par_pl_i = null;
//Playlist type: "static" or "dynamic".
this.pl_type = "static";
//Playlist current working directory. For hiarchical playlists. A list of indexes.
this.pl_cwd = [];
//Active track. Used to index a track in a playlist.
this.track_i = null;
//Playlists structure. Used by most functions. Content can be dynamic, static -- I.E. fixed here -- or a mix of both.
this.pls = []
//For a sample pls structure see at.player.new_pl(param);
//Stop editing past this point.
//What player skin to use.
this.skin = "troll";
//Music engine namespace.
this.me = null;
//Search form, q input last value.
this.q = null;
//Toggle enable artist art.
this.enable_artist_art = 1;
//Toggle enable music engine results.
this.enable_me_results = 1;
//Enables or disables API so player can be used stand alone.
this.enable_api = 1;
//Specifies whether a resource exists and has had it's controls hooked.
this.res_hooked = false;
//Enables track end next.
this.enable_te_next = true;
//Track ended mutex.
this.track_ended_locked = false;

this.release_track_ended_lock = function(){
	at.track_ended_locked = false;
}

this.like = function(s1, s2){
/*
Returns whether or not s1 is like s2 (similar.) Function
is boolean.
*/
    loc = 1;
    //dmp.Match_Distance = parseFloat(s1.length > s2.length ? s1.length : s2.length);
    dmp.Match_Distance = 5;
    dmp.Match_Threshold = parseFloat(0.55);
    //alert(dmp.match_main(s1, s2, loc));
    //alert(dmp.match_main(s2, s1, loc));
    if(s1.length >= 32 || s2.length >= 32) return 0;
    return (dmp.match_main(s1, s2, loc) != -1 || dmp.match_main(s2, s1, loc) != -1) ? 1 : 0;
    //match = dmp.match_main(s1, s2, loc);
    
    
    
    //alert(s1.length);
    //alert(me.similar_text(s1, s2));
    //return (me.similar_text(s1, s2) / s1.length);
    //return match;
    //return me.similar_text(s1, s2) / s1.length >= 0.75;
    //return me.similar_text(s1, s2) >= 7 ? 1 : 0;
}

this.asyncRequest = function(url, callback)
{
	var req;
	if(window.XMLHttpRequest){ //Mozilla, safari, ...
		req = new XMLHttpRequest();
	}
	else if(window.ActiveXObject){ //IE 8 and older
		req = new ActiveXObject("Microsft.XMLHTTP");
	}
	
	req.onreadystatechange = function(event){
		if(req.readyState === 4){
			if(req.status === 200){
				callback(req);
			}
		}
	};
	req.open('GET', url, true);
	req.send(null);
}

this.urlencode = function(s){
    return encodeURIComponent(s);
}
this.urldecode = function(s){
    return decodeURIComponent(s);
}
this.http_get = function(url){
    var req = new XMLHttpRequest();
    req.open('GET', url, false);
    req.send(null);
    if(req.status == 200) {
        return req.responseText;
    }
    return false;   
}
this.strpos = function(haystack, needle, offset){
    // Finds position of first occurrence of a string within another  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/strpos
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Onno Marsman    
    // +   bugfixed by: Daniel Esteban
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: strpos('Kevin van Zonneveld', 'e', 5);
    // *     returns 1: 14
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}
this.clear_element = function(id){
    var Node1 = document.getElementById(id); 
    if(Node1 != null){
        var len = Node1.childNodes.length;
        for(var i = 0; i < len; i++)
        {           
            if(Node1.childNodes[i].id == "Child")
            {
              Node1.removeChild(Node1.childNodes[i]);
            }
        }
    }
}
this.htmlspecialchars_decode = function(str) {
    var div = document.createElement('div');
    div.innerHTML = str;
    return div.firstChild.data;
}
this.htmlspecialchars = function(str) {
    var div = document.createElement('div');
    var text = document.createTextNode(str);
    div.appendChild(text);
    return div.innerHTML;
}


this.zxcTO = "";

/***
*    begin anontune.api
*    begin anontune.api
*/
this.api = new function(){
//>Stop vid playing before api cal

this.queue = [];
this.call = function(params, c, min_group, requires_queue){
    /*
This function is shitty.
Although having one big function that handles every
API call means less code and one general interface,
it also becomes hard to maintain as more API calls
are added which required their own special cases
and processing. Perhaps split off these cases into
their own functions.
    */
    
    var at_json = null;
    var response = false;
    var url = null;
    var success = false;
    var no = 0;
    /*
Warning: Overwriting at.pl_i declaration.
    */
    var pl_i = null;
    var pl = null;
    var found = false;
    var pl_params = null; 
    var track = null;
    var loaded = false;
    var success = false;
    
    if(!at.enable_api) return false;
    
    //Clear old json var.
    at.json = null;
    //Main call.
    url = var_api_url + "?c=" + at.urlencode(c) + "&auth_username=" + at.urlencode(at.auth_username) + "&auth_password=" + at.urlencode(at.auth_password) + "&username=" + at.urlencode(at.username);
    //Append all the params.
    for(param in params){
        url = url + "&" + at.urlencode(param) + "=" + at.urlencode(params[param]);    
    }
    //alert(url);

    //Queue call if required.
    if(at.group < min_group && requires_queue){
        at.api.queue.push(c);   
    }
    //Return if we're not authorised to make call.
    if(at.group < min_group){
        return false;
    }
    
    //Specific logic for this call.
    if(c == "get_playlist"){
        no = at.pls.length;
        found = false;
        pl_params = null;
        
        //See if it needs loading.
        for(var i = 0; i < no; i++){
            if(at.pls[i] != null){
                if(typeof(at.pls[i]["id"]) != "undefined" && typeof(params["id"]) != "undefined"){
                    if(at.pls[i]["id"] == params["id"]){
                        found = true;
                        if(at.pls[i]["loaded"] == true){
                            loaded = true;
                        }
                        pl_i = i;
                        break;
                    }
                }
            }
        }
        
        //If it doesn't return.
        if(!found || loaded){
            return true;
        }
    }
    
    //Make call.
    response = at.http_get(url);
    //alert("rrr = " + response);
    
    //Evaluate response.
    if(response != false){
        if(response == ""){
            success = true;
        }
        else{
            eval(response);
            at.json = at_json;
            if(typeof(at.json["error"]) == "undefined"){
                success = true;
            }
            else
            {
                //if(typeof(at.json["error"]) != "undefined"){
                alert(at.json["error"]);
                //}
                return false;
            }
        }
    }

    //Do c specific processing.
    if(success){
        if(c == "get_playlists"){
            no = at.pls.length;
            found = false;
            for(var i in at.json){
                if(typeof(at.json[i]["name"]) !== "undefined"){
                    if(at.json[i]["cmd"] == "null" || at.json[i]["cmd"] == ""){
                        at.json[i]["cmd"] = null;
                    }
                    pl_params = at.json[i];
                    pl = at.player.new_pl(pl_params);
                    for(var j = 0; j < no; j++){
                        if(at.pls[j] != null){
                            if(at.pls[j]["id"] == at.json[i]["id"]){
                                found = true;
                                break;
                            }
                        }
                    }
                    if(!found){
                        at.pls.push(pl);
                    }
                }
            }
        }
        
        if(c == "get_playlist"){
            if(!loaded){
                for(var i in at.json){
                    if(typeof(at.json[i]["id"]) !== "undefined"){
                        track_params = at.json[i];
                        track = at.player.new_track(track_params);
                        at.pls[pl_i]["tracks"].push(track);
                    }
                }
                at.pls[pl_i]["loaded"] = true;
            }
        }
    }
    return true;
}

}
/*
*    end anontune.api
*    end anontune.api
***/

/***
*    begin anontune.player
*    begin anontune.player
*/
this.player = new function(){

this.add_track = function(params, pl_i){
    /*
Warning: pl_i overwrites at.pl_i.
    */
    var track;
    var track_params;
    
    if(pl_i < at.pls.length){ //No overflow.
        if(at.pls[pl_i] != null){ //If it exists.
            track_params = params;
            
            //API call.
            if(at.api.call(track_params, "insert_track", 1, 1)){
                track_params["id"] = at.json[0]["id"];
                if(typeof(at.json[0]["warning"]) != "undefined"){
                    alert(at.json[0]["warning"]);
                    return;
                }
            }
            else {
                return;
            }
            track = at.player.new_track(track_params);
            
            //Add track.
            at.pls[pl_i]["tracks"].push(track);
            
            //Change active track.
            at.track_i = at.pls[pl_i]["tracks"].length - 1;
        }
    }
};
this.del_track = function(track_i, pl_i){
    /*
Warning: pl_i overwrites at.pl_i.
Warning: track_i overwrites at.track_i.
    */
    if(pl_i < at.pls.length){ //No overflow.
        if(at.pls[pl_i] != null){ //If it exists.
            //API synch.
            if(!at.api.call({"id": at.pls[pl_i]["tracks"][track_i]["id"]}, "delete_track", 1, 1)){
                return;
            }
            
            //Delete track.
            at.pls[pl_i]["tracks"].splice(track_i, 1);
            
            //Clear active track.
            if(at.track_i == track_i && at.pl_i == pl_i){
                at.track_i = null;
            }
            if(at.me.track_i == track_i && at.me.pl_i == pl_i){
                at.me.track_i = null;
            }
        }
    }
};

this.edit_track = function(params, track_i, pl_i){
    /*
Warning: pl_i overwrites at.pl_i.
Warning: track_i overwrites at.track_i.
    */
    if(pl_i < at.pls.length){ //No overflow.
        if(at.pls[pl_i] != null){ //If it exists.
            //API synch.
            if(!at.api.call(params, "update_track", 1, 1)){
                return;
            }
            
            //Update.
            for(param in params){
                at.pls[pl_i]["tracks"][track_i][param] = params[param];
            }
        }
    }
};

this.play_track = null; //Hooked.
this.pause_track = null; //Hooked.
this.stop_track = null; //Hooked.

this.play_track = function(track_i, pl_i){ //YouTube player container ID.
    //Store active track.
    at.track_i = track_i;
    
    if(at.skin == "default"){
        //Highlight
        at.player.skin.highlight_main('atp-trackc' + track_i, 2);
        
        //Field vals
        at.player.skin.load_field_values();
    }
    

    var yt_api_call = null;
    var ytapicc = null; //YouTube API call container.
    var yt_script = null;
    var vid = null;
    var query = at.urlencode(at.pls[pl_i]["tracks"][track_i]["title"] + " - " + at.pls[pl_i]["tracks"][track_i]["artist_name"]);
    
    //Check VID isn't preset.
    if(vid == null){ //Not present, find one.
        //Search YouTube for track
        //&restriction="
        ip_addr = var_ip_address;
        
        yt_api_call = "http://gdata.youtube.com/feeds/api/videos?safeSearch=strict&max-results=1&v=2&alt=json-in-script&format=5&callback=atyt&q=" + query;
        if(ip_addr.match(/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/gi) != null){
            yt_api_call += "&restriction=" + at.urlencode(ip_addr);
        }
        ytapicc = document.getElementById("atp-ytapi");
        yt_script = document.createElement("script");
        yt_script.type = "text/javascript";
        yt_script.src = yt_api_call;
        ytapicc.appendChild(yt_script);
    }
    else
    { //Present, use current one.
        at.player.output_ytplayer(vid);
    }
}
this.track_ended = function(){
	if(at.loop){
		at.player.replay_track();
	}
	else {
		if(at.enable_te_next){
			at.player.next_track();
		}
	}
}
this.close_pl = function(){
    if(at.pl_cwd.length > 1){
        if(typeof(at.pls[pl_i]) != "undefined"){
            if(at.pls[pl_i] != null){
                at.pl_cwd.pop();
            }
        }
    }
}
this.open_pl = function(pl_i){
    if(typeof(at.pls[pl_i]) != "undefined"){
        if(at.pls[pl_i] != null){
            at.pl_cwd.push(pl_i);
        }
    }
}

this.add_pl = function(params){
    //API synch.
    if(at.api.call(params, "insert_playlist", 1, 1)){
        params["id"] = at.json[0]["id"];
        if(typeof(at.json[0]["warning"]) != "undefined"){
            alert(at.json[0]["warning"]);
            return;
        }
    }
    else {
        return;
    }
    
    //Add playlist.
    pl = at.player.new_pl(params);
    at.pls.push(pl);
    
    //Change active pl_i.
    at.pl_i = at.pls.length - 1;
    
    //Change active track.
    at.track_i = null;
};

this.del_pl = function(pl_i){
    /*
Warning: pl_i overwrites at.pl_i.
    */
    if(pl_i != null)
    {
        //API synch.
        if(!at.api.call({"id": at.pls[pl_i]["id"]}, "delete_playlist", 1, 1)){
            return;
        }
        
        //Delete playlist.
        at.pls.splice(pl_i, 1);
        
        //Clear active pl_i and track_i.
        if(at.pl_i == pl_i){
            at.pl_i = null;
            at.track_i = null;
        }
    }
};
this.edit_pl = function(params, pl_i){
    /*
Warning: pl_i overwrites at.pl_i.
    */             
    if(pl_i != null) //Otherwise what are we editing?
    {
        if(!at.api.call(params, "update_playlist", 1, 1)){
            return;
        }

        //Edit playlist.
        for(param in params){
            at.pls[pl_i][param] = params[param];
        }
    }
};
this.load_pl = function(params){
    at.api.call(params, "get_playlist", 0, 0);
};

this.load_pls = function(){
    at.api.call({}, "get_playlists", 0, 0);
}

this.hook_youtube = function(){
	at.player.replay_track = function(){
		if(ytplayer == null) return;
		ytplayer.seekTo(0, true);
		ytplayer.playVideo();
	}
	at.player.play_track = function(){
		if(ytplayer == null) return;
		ytplayer.playVideo();
	}
	at.player.pause_track = function(){
		if(ytplayer == null) return;
		ytplayer.pauseVideo();
	}
	at.player.stop_track = function(){
		if(ytplayer == null) return;
		ytplayer.stopVideo();
	}
	at.res_hook = true;
}

this.hook_jplayer = function(){
	//Controls.
	at.player.replay_track = function(){
		$("#jplayer").jPlayer("play");
	}
	at.player.play_track = function(){
		$("#jplayer").jPlayer("play");
	}
	at.player.pause_track = function(){
		$("#jplayer").jPlayer("pause");
	}
	at.player.stop_track = function(){
		$("#jplayer").jPlayer("stop");
	}
	
	//Events.
	$("#jplayer").bind($.jPlayer.event.ended, function(event){
		if(!at.track_ended_locked){
			at.track_ended_locked = true;
			at.player.track_ended();
			setTimeout("at.release_track_ended_lock();", 2000);
		}
		return false;
	});
	$("#jplayer").bind($.jPlayer.event.play, function(event){
		at.player.skin.enable_play();
	});
	$("#jplayer").bind($.jPlayer.event.pause, function(event){
		at.player.skin.disable_play();
	});
	$("#jplayer").bind($.jPlayer.event.timeupdate, function(event){
		//Fixes moved, and false 200 OK.
		if(event.jPlayer.status.currentTime){
			if(at.me.peh != null){
				clearTimeout(at.me.peh);
				at.me.peh = null;
			}
		}
	});
	//Fixes 404.
	$("#jplayer").bind($.jPlayer.event.error, function(event){
		at.player.skin.auto_play(null);
		return;
		if(event.jPlayer.error.type == $.jPlayer.error.URL || event.jPlayer.error.type == $.jPlayer.error.URL_NOT_SET){
			at.player.skin.auto_play(null);
		}
	});
	
	at.res_hook = true;
}

this.replay_track = null;

this.next_track = function(){

    //if(at.loop) return;
    if(at.pl_i != null && at.track_i != null){
        track_no = at.pls[at.pl_i]["tracks"].length;
        //alert(track_no);
        //alert(at.track_i);
        once = false;
        if(track_no){
            //alert("vvv");
            for(i = at.track_i; i < track_no; i++){

                //alert("here");
                if(i + 1 == track_no){ //It's the last track.
                    //Start at top.
                    i = -2;
                    if(once) break;
                    once = true;
                    continue;
                }
                else
                {
                    //Point to next track.
                    at.track_i = i + 1;
                }
                
                //Play track.
                //Todo: use new abstraction
                //alert(at.track_i);
                at.player.skin.auto_play({
					"title": at.pls[at.pl_i]["tracks"][at.track_i]["title"], 
					"artist": at.pls[at.pl_i]["tracks"][at.track_i]["artist_name"]
                });
                break;
            }
        }
    }
}
this.prev_track = function(){
    //if(at.loop) return;
    if(at.pl_i != null && at.track_i != null){
        track_no = at.pls[at.pl_i]["tracks"].length;
        once = false;
        if(track_no != 0){
			var i = at.track_i;
			if(!i){
				at.track_i = track_no - 1;
				at.player.skin.auto_play({
					"title": at.pls[at.pl_i]["tracks"][at.track_i]["title"], 
					"artist": at.pls[at.pl_i]["tracks"][at.track_i]["artist_name"]
                });
                return;
			}
            for(i = at.track_i; i < track_no; i--){
                if(i == 0){ //It's the last track.
                    //Start at bottom.
                    i = track_no - 2;
                    if(once) break;
                    once = true;
                    continue;
                }
                else
                {
                    //Point to next track.
                    at.track_i = i - 1;
                }
                
                //Play track.
                //Todo: create play track abstraction and use it here
                at.player.skin.auto_play({
					"title": at.pls[at.pl_i]["tracks"][at.track_i]["title"], 
					"artist": at.pls[at.pl_i]["tracks"][at.track_i]["artist_name"]
                });
                break;
            }
        }
    }
}


this.shuffle = function(pl_i){
    //Warning: pl_i over-writes at.pl_i.
    if(pl_i != null){
        if(pl_i > -1 && pl_i < at.pls.length){
               var n = at.pls[pl_i]["tracks"].length;
               var random = 0;
               var s = [];
               var cur_track = null;
               if(at.track_i != null && at.pl_i != null){
                   if(pl_i == at.pl_i){
                       cur_track = at.pls[pl_i]["tracks"][at.track_i];
                   }
               }
               while(n){
                   //Choose random track to become part of new set.
                   random = Math.floor(1 + (1 + n - 1) * Math.random()) - 1;
                   //Since we're moving tracks around at.track_i could now
                   //point to wrong track, correct it :).
                   if(cur_track != null){
                       if(at.pls[pl_i]["tracks"][random] == cur_track){
                           at.track_i = s.length;
                       }
                   }
                   //Push track to new set.
                   s.push(at.pls[pl_i]["tracks"][random]);
                   //Remove track from old set (so it can't be chosen again.)
                   at.pls[pl_i]["tracks"].splice(random, 1);
                   //Now that it's gone shrink known set size.
                   n--;
               }
               //Replace with new shuffled set.
               at.pls[pl_i]["tracks"] = s;
        }
    }
}
this.toggle_loop = function(){
    at.loop = at.loop ? false : true;
}

this.search_tracks = function(){
    
}
this.move_pl = function(){
    
}
this.move_track = function(){
    
}
this.status = function(){
    
}

this.swap_pl_by_filter = function(){
	if(at.pl_i == null) return;
	var new_pl = [];
	for(var i = 0; i < at.pls[at.pl_i]["tracks"].length; i++){
		if(at.pls[at.pl_i]["tracks"][i]["filter"] === false){
			new_pl.push(at.pls[at.pl_i]["tracks"][i]);
		}
	}
	for(var i = 0; i < at.pls[at.pl_i]["tracks"].length; i++){
		if(at.pls[at.pl_i]["tracks"][i]["filter"] === true){
			new_pl.push(at.pls[at.pl_i]["tracks"][i]);
		}
	}
	at.pls[at.pl_i]["tracks"] = new_pl; //Swapped.
}

this.swap_pls_by_filter = function(){
	var new_pls = [];
	for(var i = 0; i < at.pls.length; i++){
		if(at.pls[i]["filter"] === false){
			new_pls.push(at.pls[i]);
		}
	}
	for(var i = 0; i < at.pls.length; i++){
		if(at.pls[i]["filter"] === true){
			new_pls.push(at.pls[i]);
		}
	}
	at.pls = new_pls; //Swapped.
}

this.set_search = function(set, fields, query){
    /*
Example Usage: set_search('pls[0]["tracks"]', {"title", "artist"}, "eminem");
recode since one positive could be overwriten by a negative
    */
    query = query.toLowerCase();
    var l = eval(set + ".length;");
    var code = "";
    for(var i = 0; i < l; i++){
        //Reset.
        eval(set + '[i]["filter"] = true;');
        for(n in fields){
            code = "if(at.strpos(" + set + "[" + i + ']["' + fields[n] + '"].toLowerCase(), query, 0)';
            code += ' !== false || query == ""){ ' + set + "[" + i + ']["filter"] = false; }';
            //alert(code);
            eval(code);
        }
    }          
}

this.new_pl = function(params){
    var pl =
    {
        //Playlist ID in database.
        "id": null,
        //Has the playlist's tracks been loaded from the database?
        "loaded": false,
        //Command used to dynamically create a list of tracks.
        "cmd": "0",
        //Which playlist does this playlist reside in?
        "par_pl": null,
        "name": "New Playlist",
        "parent_id": "0",
        "tracks": [],
        "filter": false
    };
    
    //Overwrite with dynamic params.
    for(param in params){
        pl[param] = params[param];
    }
    return pl;
}

this.new_track = function(params){
    var track = 
    {
        //Track ID in database.
        "id": null,
        //For now this is any fixed YouTube VIDs.
        "service_resource": null,
        "title": "New Title",
        "artist_name": "New Artist",
        "filter": true
    };
    
    //Overwrite with dynamic params.
    for(param in params){
        track[param] = params[param];
    }
    return track;
}

//Skin namespace.
this.skin = null;

this.prepare = function(){
	at.username = htmlspecialchars_decode(at.username, 'ENT_COMPAT');
	at.auth_username = htmlspecialchars_decode(at.auth_username, 'ENT_COMPAT');
	at.auth_password = htmlspecialchars_decode(at.auth_password, 'ENT_COMPAT');
    
    /*
    var me_code_url = var_this_root_url + "/music_engine.js";
    var me_code = at.http_get(me_code_url);
    if(me_code == false){
        alert("Failed to load music engine.");
        return false;
    }
    eval(me_code);
    */
    
    at_player_ready();
}

}
/*
*    end anontune.player
*    end anontune.player
***/

};
/*
*    end anontune
*    end anontune
***/


me = new function(){
	this.results = [];
	this.result_no = 0;
	//{title: null, artist: null}, ...
	this.queries = [];
	this.routes = [];
	this.qid = -1;
	this.status = "ready";
	this.se = null; //Select result thread.
	this.peh = null; //Play error handler.
	
	this.get_route_index = function(result){
		var name = result.type;
		for(var i = 0; i < at.me.routes.length; i++){
			if(at.me.routes[i].settings.name == name) return i;
		}
		return -1;
	}
		
	this.new_result = function(p){
		//This form allows defaults.
		res = {
			"qid": null,
			"type": null,
			"data": null,
			"meta": {
				"title": null, //Required.
				"artist": null, //Required.
				"album": null
			},
			"accuracy": null,
			"played": false
		};
		for(property in p){
			res[property] = p[property];
		}
		return res;
	}
	
	this.find_results = function(p){
		at.me.result_no = 0;
		at.me.results = [];
		at.me.status = "searching";
		for(var i = 0; i < at.me.routes.length; i++){
			at.me.routes[i].route(p);
		}
		at.me.status = "scheduled";
	}
	
	this.rate_result = function(res){
		res.accuracy = 100;
		var result_list_filtered = [];

		//Split q into terms.
		if(at.pl_i == null || at.track_i == null) return res;
		
		var track = at.pls[at.pl_i]["tracks"][at.track_i];
		var q = track["title"] + " - " + track["artist_name"];
		var q_t = q.toLowerCase().split(/\s+/);
		
		//Skip blank results.
		for(var i = 0; i < q_t.length; i++){
			if(q_t[i].match(/^\s*$/) != null){
				q_t.splice(i, 1);
			}
		}

		//Split result into terms.
		var r_q = res["meta"]["title"] + " - " + res["meta"]["artist"];
		var r_t = r_q.toLowerCase().split(/\s+/);
		
		//Skip blank results.
		for(var k = 0; k < r_t.length; k++){
			if(r_t[k].match(/^\s*$/) != null){
				r_t.splice(k, 1);
			}
		}
		
		//Count redundancy of result q relative to q (rough.)
		var red_no = r_t.length; //Start at 100% redundancy.
		var match_no = 0; //Entropy starts at 0.
		var red_list = []; //Hold redundancy.
		var q_t_temp = q_t.slice(0);
		var r_t_temp = r_t.slice(0);
		for(var j = 0; j < r_t.length; j++){
			k = 0;
			for(; k < q_t_temp.length; k++){
				if(at.like(r_t[j], q_t_temp[k])){
					q_t_temp.splice(k, 1);
					r_t_temp[j] = null;
					match_no++;
					break;
				}
			}
		}
		red_list = q_t_temp.splice(0);
		for(var j = 0; j < r_t_temp.length; j++){
			if(r_t_temp[j] == null) continue;
			red_list.push(r_t_temp[j]);
		}
		red_no = red_list.length;
		
		//>40% redundancy = result is crap
		//alert(red_no);
		//alert(me.filter.globf.result_list[i]["q"]);
		//alert(red_no / ((q_t.length + r_t.length) - match_no));
		/*
		if(red_no / ((q_t.length + r_t.length) - match_no) > 0.4){
			continue;
		}
		*/
		
		//Specific redundancy disqualifies results.
		//E.g. 'remix'.
		red_dis_list = 
		[
			/remix/i, /cover/i, /instrumental/i, 
			/preview/i, /sample/i, /review/i,
			/orchestral/i, /mix/i, /mash[-]*up/i,
			/live/i, /concert/i, 
			/[ck]+ar[aoe]+[kc]+[ye]+/i, /parody/i,
			/lesson/i, /guide/i, /tutorial/i, /tv/i,
			/part/i, /ver([.]|sion)/i, /vocal/i,
			/mash/i, /demo/i, /acoustic/i, 
			/fandub/gi, /piano/i, /^at$/i, /^in$/i, /^the$/i,
			/spoof/i, /tribute/i, /guitar/i, /violin/i,
			/how[-]*to/i, /pt[.]/i, /promo/i, /trailer/i,
			/teaser/i
		];
		///piano/i
		/*
		 * how-to?
		Todo: Code a tool that applies the filter to all your music. Store whether it got through or it didn't. Then look for false positives and false negatives and correct filter.

		concat redundant terms by \s and then apply

		False positives:
		original mix
		part 1
		original version


		Problems: Above regexs could potentially be parts of common
		words that are found which would be a false positive but making
		it more accurate you lose flexibility.
		piano, guitar, violin . . .etc?
		demo?
		*/
		//Todo, catch artists with same song title
		
		//Apply dis-qualifiers.
		skip = 0;
		for(var j = 0; j < red_list.length; j++){
			for(var k = 0; k < red_dis_list.length; k++){
				if(red_dis_list[k].source.length >= 5){
					//Remove ^ and $ from start.
					if(r_q.toLowerCase().match(red_dis_list[k]) != null){
						skip = 1;
					}
				}
				else{
					if(red_list[j].toLowerCase().match(red_dis_list[k]) != null){
						skip = 1;
					}
				}
			}
		}
		/*
		for(var j = 0; j < red_list.length; j++){
			for(var k = 0; k < red_dis_list.length; k++){
				dTitle.toLowerCase().indexOf(title.toLowerCase()) !== -1
				if(
			}
		}
		*/
		if(skip){
			res.accuracy = 0;
		}
		
		res.accuracy = res.accuracy * (at.me.routes[at.me.get_route_index(res)].settings.weight / 100);
		return res;
	}
	
	this.add_results = function(results){
		for(var i = 0; i < results.length; i++){
			var result = at.me.rate_result(results[i]);
			at.me.results.push(result);
			if(result["qid"] == at.me.qid){
				at.me.result_no++
			}
		}
	}
	
	this.new_query = function(query){
		at.me.qid++;
		query.qid = at.me.qid;
		at.me.queries.push(query);
		return query;
	}
	
	this.select_result = function(floor){
		at.me.sr = null;
		var p = null;
		
		//Find highest accuracy result that satifies or exeeds floor.
		for(var i = 0; i < at.me.results.length; i++){
			//if(at.me.results[i]["accuracy"] == 0) continue;
			if(at.me.results[i]["qid"] == at.me.qid){
				if(!at.me.results[i]["played"]){
					if(at.me.results[i]["accuracy"] >= floor){
						if(p != null){
							if(at.me.results[i]["accuracy"] > at.me.results[p]["accuracy"]){
								p = i;
							}
						}
						else{
							p = i;
						}
					}
				}
			}
		}
		
		//Play result.
		if(p != null){
			at.me.results[p]["played"] = true;
			at.player.skin.play_resource(at.me.results[p]);
		}
		else{
			//Reschedual.
			if(floor - 20 < 0) floor = 0; //No more limits.
			else floor = floor - 20;
			at.me.sr = setTimeout("at.me.select_result(" + floor + ");", 1000);
		}
	}
	
};

var exfm_route = {
	settings: {
		name: "exfm",
		weight: 100,
		timeout: 5000
	},

	route: function(p){
		//Then build the search string.
		q = p.query.title + " - " + p.query.artist;
		
		//Build query URL.
		var url = "http://ex.fm/api/v3/song/search/";
		url += encodeURIComponent(q);
		url += "?start=0&results=20";
		
		//Send request and parse it into Javascript.
		var that = this;
		var xml_string = at.asyncRequest(url, function(xhr){
			//Parse JSON to me.result.
			var response = JSON.parse(xhr.responseText);
			var results = new Array();
			
			//Check the response.
			if(response.results > 0){
				var songs = response.songs;
				
				//Walk through the results and store it in "results".
				for(var i = 0; i < songs.length; i++){
					var song = songs[i];
					var result = new Object();
					if(song.url.indexOf("http://api.soundcloud") === 0){ //Unauthorised, use soundcloud resolver instead.
						continue;
					}
					
					if(song.artist !== null){
						if(song.title !== null){
							//Normalizes whitespace.
							var dTitle = "";
							if(song.title.indexOf("\n") !== -1){
								var stringArray = song.title.split("\n");
								var newTitle = "";
								for(var j = 0; j < stringArray.length; j++){
									newTitle += stringArray[j].trim() + " ";
								}
								dTitle = newTitle.trim();
							}
							else{
								dTitle = song.title;
							}
							
							//Removes garbage artist names from title.
							dTitle = dTitle.replace("\u2013","").replace("  ", " ").replace("\u201c","").replace("\u201d","");
							if(dTitle.toLowerCase().indexOf(song.artist.toLowerCase() + " -") === 0){
								dTitle = dTitle.slice(song.artist.length + 2).trim();
							}
							else if(dTitle.toLowerCase().indexOf(song.artist.toLowerCase() + "-") === 0){
								dTitle = dTitle.slice(song.artist.length + 1).trim();
							}
							else if(dTitle.toLowerCase() === song.artist.toLowerCase()){
								continue;
							}
							else if(dTitle.toLowerCase().indexOf(song.artist.toLowerCase()) === 0){
								dTitle = dTitle.slice(song.artist.length).trim();
							}
							var dArtist = song.artist;
						}
					}
					else{
						continue;
					}
					
					if(song.album != null){
						var dAlbum = song.album;
					}

					var meta = {
						"title": dTitle,
						"artist": dArtist
					};
					var data = {
						"url": song.url
					};
					var result = at.me.new_result({
						"qid": p.query.qid,
						"type": that.settings.name,
						"data": data,
						"meta": meta
					});
					results.push(result);
				}
			}
			at.me.add_results(results);
		});
	}
};

var youtube_route = {
	settings: {
		name: "youtube",
		weight: 95,
		timeout: 15000
	},
	route: function(p){
		//Then build the search string.
		q = p.query.title + " - " + p.query.artist;
		
		//Build query URL.
		var_ip_address = "78.46.172.17"; //Todo: Comment out in real app.
		url =  "http://gdata.youtube.com/feeds/api/videos?safeSearch="
		url += "strict&max-results=10&v=2&alt=json&format=5"
		url += "&q=" + encodeURIComponent(q); //&paid-content=true
		//Note: Fields param can be used to filter out non-used data.
		//paid-content could be used to remove Vevo results
		//license could be used to remove infringing files
		if(var_ip_address.match(/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/gi) != null){
			url += "&restriction=" + at.urlencode(var_ip_address);
		}
		
		//Send request and parse it into Javascript.
		var that = this;
		var xml_string = at.asyncRequest(url, function(xhr){
			//Parse JSON to me.result.
			var response = JSON.parse(xhr.responseText);
			var results = new Array();
			var data = response;
			
			//Check the response.
			var feed = null;
			var entries = null;
			var entry = null;
			var title = null;
			var vidp = null;
			var vid = null;
			var skip = false;
			if(data != null){
				feed = data.feed;
				entries = feed.entry || [];
				for(var i = 0; i < entries.length; i++){
					skip = false;
					entry = entries[i];
					title = entry.title.$t;

					//Check accessControls.
					for(var j = 0; j < entry.yt$accessControl; j++){
						if(entry.yt$accessControl[j]["action"] === "embed"){
							//Can it be embedded?
							if(entry.yt$accessControl[j]["permission"] === "denied"){
								skip = true;
								break;
							}
						}
						//Can it be played on mobile?
						if(entry.yt$accessControl[j]["action"] === "syndicate"){
							if(entry.yt$accessControl[j]["permission"] === "denied"){
								skip = true;
								break;
							}
						}
					}
					
					//Check geo restrictions.
					//Todo: check country.
					if(typeof(entry.app$control) != "undefined"){
						if(entry.app$control.yt$state.name === "restricted"){
							skip = true;
						}
					}
					
					//Check URL.
					vidp = /^.*?((youtu.be\/)|(v\/)|(e\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*?/
					vid = vidp.exec(entry.media$group.media$content[0].url);
					if(vid == null){
						skip = true;
					}
					if(typeof(vid[7]) == "undefined"){
						skip = true;
					}
					else{
						vid = vid[7];
					}
					
					if(skip) continue;
					
					//We made it, but note: Videos can still be blocked after this point.
					//Todo: Remove artist from title. Port clean() to js.
					var meta = {
						"title": entry.title.$t,
						"artist": p.query.artist //Use uploader?
					};
					var data = {
						"vid": vid
					};
					var result = at.me.new_result({
						"qid": p.query.qid,
						"type": that.settings.name,
						"data": data,
						"meta": meta
					});
					results.push(result);
				}
				at.me.add_results(results);
			}
		});
	}
};

//Install routes.
at.me = me;
at.me.routes.push(exfm_route);
at.me.routes.push(youtube_route);
