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


//Wraper for YouTube API callback.
function atyt(data){
    at.player.process_ytapi(data);
}

//Get youtube player instance when possible.
function onYouTubePlayerReady(playerId) {
    var ytplayer = document.getElementById(playerId);
  
    //Play track.
    ytplayer.playVideo();
    
    //Add an event listener.
    ytplayer.addEventListener("onStateChange", "at.player.track_ended");
    at.ytplayer = ytplayer;
    
    if(at.enable_me_results){
        at.me.track_i = at.track_i;
        at.me.pl_i = at.pl_i;
        var track_i = at.track_i;
        var pl_i = at.pl_i;
        
        //Cleanup.
        at.me.results = [];
        at.me.results_no = null;
        at.me.final_tiles = 0;
        
        q = at.pls[pl_i]["tracks"][track_i]["title"];
        if(!at.pls[pl_i]["tracks"][track_i]["artist_name"].match(/^(\s*unknown\s*)|(\s+)$/i)){
        q += " - " + at.pls[pl_i]["tracks"][track_i]["artist_name"];
    }
    at.me.search.youtube.p = {"start_index": 0, "q": q, "result_no": 5, "id": null, "callback_name": "at.me.search.soundcloud.main"};
    at.me.search.soundcloud.p = {"start_index": 0, "q": q, "result_no": 5, "id": null, "callback_name": "symbolic"};
    at.me.search.youtube.main();
    }
    
    //Get image.
    if(at.enable_artist_art){
        at.me.get_image.p["q"] = at.pls[at.pl_i]["tracks"][at.track_i]["artist_name"];
        at.me.get_image.p["callback_name"] = "at.me.play_asynch_proc";
        if(netjs.http.lock){
            at.me.get_image.write_image(null);
            //at.me.play_asynch_proc();
        }
        else{
            at.me.get_image.main();
            //at.me.play_asynch_proc();
        }
    }
    

    
    //todo: change video quality to 240p
    //Change quality to lowest for buffer-less playback.
    //ytplayer.setPlaybackQuality("small");
    //Forget about this, YouTube's autodetect is excellent.
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
//YouTube player element. E.G. ytplayer.stopVideo();.
this.ytplayer = null;
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
this.skin = "default";
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

this.check_search_filter = function(){
    filter = document.search.q.value;
    if(filter != at.q){
        at.search_filter();
    }
    at.q = filter;
}

this.search_filter = function(){
    filter = document.search.q.value;
    
    //Apply to playlists.
    at.player.set_search('at.pls', ["name"], filter);
    
    //Apply to active playlist.
    if(at.pl_i != null){
        at.player.set_search('at.pls[' + at.pl_i + ']["tracks"]', ["title", "artist_name"], filter);
        
        //Show changes.
        at.player.skin.load_playlist(at.pl_i);
    }
    
    //Show changes.
    at.player.skin.load_playlists();
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
        
        yt_api_call = "http://gdata.youtube.com/feeds/api/videos?paid-content=false&safeSearch=strict&max-results=1&v=2&alt=json-in-script&format=5&callback=atyt&q=" + query;
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
this.track_ended = function(event){
    if(event == 0){
        if(at.loop){
            at.ytplayer.seekTo(0, true);
            //at.player.play_track(at.track_i, at.pl_i);
        }
        else {
            at.player.next_track();
        }
    }
}
this.process_ytapi = function(data){
    /*
Called indirectly by the YouTube API. It's purpose
is to parse the search results and write the YouTube
player code to play the first result.
    */
    var feed = null;
    var entries = null;
    var entry = null;
    var title = null;
    var p = null;
    var vid = null;
    if(data != null){ //Use vid from search result.
        feed = data.feed;
        entries = feed.entry || [];
        for(var i = 0; i < entries.length; i++){
            entry = entries[i];
            title = entry.title.$t;
            data = entries[i].media$group.media$content[0].url;
            break;
        }
        p = /^.*?((youtu.be\/)|(v\/)|(e\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*?/
        vid = p.exec(data);
        if(vid == null){
            document.getElementById("atp-main-middle-music-content").innerHTML = "<p><center>Auto-play failed. Try the alternative results.</center>";
            return;
        }
        if(typeof(vid[7]) == "undefined"){
            vid = null;
        }
        else{
            vid = vid[7];
        }
        
        at.player.output_ytplayer(vid);
    }
}
this.output_ytplayer = function(vid){
    //Hooked by skin.
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
                //alert("ccc");
                if(i + 1 != track_no){
                    if(at.pls[at.pl_i]["tracks"][i + 1]["filter"])
                    {
                        //alert("yes");
                        continue;
                    }
                }
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
                at.me.play_track(at.track_i, at.pl_i);
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
            for(i = at.track_i; i < track_no; i--){
                if(i != 0){
                    if(at.pls[at.pl_i]["tracks"][i - 1]["filter"]) continue;
                }
                
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
                at.me.play_track(at.track_i, at.pl_i);
                break;
            }
        }
    }
}

this.lock_yt_vid = function(){
    /*
    var input_val = document.getElementById("atp-main-middle-music-nav1-form-input").value;
    var p = /^.*?((youtu.be\/)|(v\/)|(e\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*?/
    var vid = p.exec(input_val)[7];
    var container = anontune.state["active"]["container"];
    var track = anontune.state["active"]["track"];
    if(container != null && track != null){
        if(anontune.state["use_api"]){
            var track_id = anontune.state["playlists"][container]["tracks"][track]["id"];
            if(!anontune.api.update_vid(track_id, vid)){
                return;
            }
        }
        anontune.state["playlists"][container]["tracks"][track]["service_resource"] = vid;
    }
    */
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
        "filter": false
    };
    
    //Overwrite with dynamic params.
    for(param in params){
        track[param] = params[param];
    }
    return track;
}

this.show = function(){
    /*
    //>looks like some code was cut off here, check it was only function declaration
    this.prepare()


    
    //Apply CSS.
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
    var scripts = document.getElementsByTagName('script');
    var this_parent = scripts[scripts.length - 1].parentNode;
    var container_height = this_parent.offsetHeight;
    var container_width = this_parent.offsetWidth;
    this_parent.innerHTML = html + this_parent.innerHTML;
    
    //Load playlists.
    anontune.player.load_playlists();


    //>Create ytapi element container for script,.
    */
    
    

}

//Skin namespace.
this.skin = null;

this.prepare = function(){

at.username = htmlspecialchars_decode(at.username, 'ENT_COMPAT');
at.auth_username = htmlspecialchars_decode(at.auth_username, 'ENT_COMPAT');
at.auth_password = htmlspecialchars_decode(at.auth_password, 'ENT_COMPAT');
    
    var me_code_url = var_this_root_url + "/music_engine.js";
    var me_code = at.http_get(me_code_url);
    if(me_code == false){
        alert("Failed to load music engine.");
        return false;
    }
    eval(me_code);
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
//alert(at.http_get);
//alert(at.skin);
//Show player.

//at.player.show();


//anontune.api.something();
