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
The brain of Anontune.

This code is pig disgusting, stop copy and pasting code you lazy assholes.
*/

me = new function(){

//Used by asynch functions to store their return val.
this.ret = null;
//Real time buffer of search results.
this.results = [];
this.results_no = 0;

this.get_tiles_mutex = 1;
//Changed to 1 when get_tiles is done.
//Indicates the last time tiles have been output.
this.final_tiles = 0;

this.track_i = null;
this.pl_i = null;
this.q = null;

this.kill_netjs_http_open = function(){
/*
The purpose of this function is to try
and kill searching for alternative results
which occurs per song request if and only if
the user chooses another song.

But it's kind of like trying to stop a car once
it's going 200 mph -- it's gradual as you apply
the breaks. We have to do that here because
searching for multiple results at the same time
causes conflicts.

See, normally when you write code you do
so under the assumption that you actually
want to complete what ever task you coded
. . . but in this case the user potentially doesn't
want to.
*/
}

this.me_search = function(){
    //Cleanup.
    at.me.results = [];
    at.me.results_no = null;
    at.me.final_tiles = 0;
    at.me.track_i = null;
    at.me.pl_i = null;

    q = document.getElementById("me_search")["me_q"].value;
    at.me.q = q;
    at.me.search.youtube.p = {"start_index": 0, "q": q, "result_no": 5, "id": null, "callback_name": "at.me.search.soundcloud.main"};
    at.me.search.soundcloud.p = {"start_index": 0, "q": q, "result_no": 5, "id": null, "callback_name": "at.me.play_asynch_proc"};
    at.me.search.youtube.main();
    //at.me.search.soundcloud.main();
    return 1;
}

//Wrapper 
this.play_track = function(track_i, pl_i){
    //Are they trying to play the same song?
    /*if(at.me.track_i == track_i && at.me.pl_i == pl_i){
        alert("You've already selected this, dumbass.");
        return 0;
    }*/
    
    /*
    at.me.track_i = track_i;
    at.me.pl_i = pl_i;
    
    //Cleanup.
    at.me.results = [];
    at.me.results_no = null;
    at.me.final_tiles = 0;
    */
    
    //Delete old song.
    at.me.reset_music_container();
    
    //Auto play song.
    at.player.play_track(track_i, pl_i);
    
    /*
    
    //at.me.search.soundcloud.main();
    */


    return 1;
}

this.play_asynch_proc = function(){
    //Kill current HTTP requests.
    if(netjs.http.lock && netjs.http.lock == 0){
        //Fuck up netjs.js.
        //netjs.applet = null;
        //netjs_cb_recv_ret = null;
        //netjs.http.read_done = 1;
        //netjs.http.callback = null;
        clearInterval(netjs.http.worker_interval);
        
        //Fuck up the music engine.
        //at.me.ret = null;
        //at.me.results = null;
        
        //Really bad code: Wait for netjs.http.open
        //to die.
        //while(netjs.http.lock) var x = "";
        
        //Restore netjs.
        //netjs.applet = document.getElementById("netjs_applet");
        
        //Fuck up netjs.applet.
        netjs.applet.kill_netjs_http_open();
        
        //Free lock.
        netjs.http.lock = 0;
    }
    
    //Search 4shared.
    var q = null;
    if(at.me.pl_i != null && at.me.track_i != null){
        q = at.pls[at.me.pl_i]["tracks"][at.me.track_i]["title"];
            if(!at.pls[at.me.pl_i]["tracks"][at.me.track_i]["artist_name"].match(/^(\s*unknown\s*)|(\s+)$/i)){
            q += " - " + at.pls[at.me.pl_i]["tracks"][at.me.track_i]["artist_name"];
        }
    }
    else{
        q = at.me.q;
    }
    //Bug: Unknown problem here, where something goes wrong
    //and it doesn't release mutex.
    at.me.search.fshared.p = {"start_index": 0, "q": q, "result_no": 5, "id": null, "callback_name": "at.me.fina"};
    at.me.search.fshared.main();
    //at.me.search.youtube.main();
}

this.get_image = new function(){
    this.p = [];
    
    this.main = function(){
        if(!at.enable_java || !netjs_ready)
        {
            //alert("a");
            at.me.ret = 0;
            at.me.get_image.write_image(null);
            at.me.future_callback(at.me.get_image.p["callback_name"]);
            return;
        }
        
        var q = encodeURIComponent(at.me.get_image.p["q"] + " music");
        http://www.bing.com/images/search?q=sdfsdf&qs=n&form=QBIR&pq=sdfsdf&sc=0-0&sp=-1&sk=
        var url = "http://www.bing.com/images/search?q=" + q + "&qs=n&form=QBIR&sc=0-0&qft=+filterui:aspect-square&sp=-1&sk=&pq=" + q;
        
        //No POST data.
        var data = null;
        //No timeout.
        var timeout = null;
        //alert("f");
        //alert(url);
        at.me.ret = netjs.http.open(url, data, timeout, "at.me.get_image.proc_thumbs");
        //alert(at.me.ret);
        if(at.me.ret != 1){
            //alert("b");
            at.me.get_image.write_image(null);
            at.me.future_callback(at.me.get_image.p["callback_name"]);
        }
    };
    
    this.proc_thumbs = function(){
        pp = /<\s*span\s+class\s*=\s*"\s*sg_cv\s*"\s*>\s*<\s*a\s* href\s*=\s*"([^"]+)"\s*class\s*=\s*"\s*sg_tc\s*"\s*onmousedown\s*=\s*"[^"]+"\s*>\s*<\s*img\s*class\s*=\s*"sg_t"/gi
        at.me.ret = 0;
        //alert("c");
        if((r = pp.exec(netjs.http.recv_buffer))){
            //alert("found first thumb");
            r = "http://www.bing.com" + r[1];
            r = at.me.clean_html(r);
            //alert(r);
            at.me.ret = netjs.http.open(r, null, null, "at.me.get_image.strip_thumb");
        }
        if(at.me.ret != 1){
            at.me.get_image.write_image(null);
            at.me.future_callback(at.me.get_image.p["callback_name"]);
        }
    };
    
    this.strip_thumb = function(){
        pp = /href="([^"]+)"[^>]+>see full size/gi;
        at.me.ret = 0;
        url = null;
        if((r = pp.exec(netjs.http.recv_buffer))){
            url = r[1];
        }
        at.me.get_image.write_image(url);
        at.me.future_callback(at.me.get_image.p["callback_name"]);
    };
    
    this.write_image = function(url){
        html = '<img style="width: 100%; height: 100%;" src="' + (url == null ? "http://www.anontune.com/player/skins/default/../../images/photo.png" : at.htmlspecialchars(url)) + '">';
        document.getElementById("atp-main-left-imgdetail").innerHTML = html;     
    };
};

this.release_mutex = function(){
    at.me.get_tiles_mutex = 1;
    //netjs.http.lock = 0;
}

this.fina = function(){
    if(!at.me.results.length){
        document.getElementById("atp-main-middle-music-tiles").innerHTML = '<center><div class="tile_table" style="width: 100%;"><div class="tile_tr"><center><div class="tile_td"><div style="width: 1px;">&nbsp;</div></div><div class="tile_td"><div style="width: 300px;" class="tile_box"><div style="height: 50px; float: left;"> </div><span style="font-size: 22px; text-align: left; float: left; margin-left: 10px; margin-top: 8px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; width: 209px;"><form style="margin: 0px; white-space: nowrap; padding: 0px; display: block;" onsubmit="at.me.me_search(); return false;" id="me_search"><input style="width: 200px; float: left; margin-top: 13px; white-space: nowrap; padding-top: 0px; padding-bottom: 0px; padding-left: 5px; padding-right: 5px; text-align: center; display: block; border: 1px solid gray; -moz-border-radius: 15px; border-radius: 15px; background: none repeat scroll 0% 0% rgb(125, 126, 125); color: white;" name="me_q"/></form></span><a onclick="at.me.me_search();" href="#"><img style="float: left; margin-top: 22px;" src="search.png"/></a></div></div></center></div></div> </center>';
        document.getElementById("me_search")["me_q"].value = "Not found.";
    }   
    
}

this.future_callback = function(callback_name){
    if(callback_name == "symbolic") return;
    s = "setTimeout(function (){ eval(\"" + callback_name + "();\"); }, 50);";
    //alert(s);
    eval(s);
};

//The result type -- used by ~all functions in this
//bitching module.
this.new_result = function(p){
    res = {
    "q": null, //Required.
    "serv_res": null, //Required.
    "serv_prov": null, //Required.
    //Usually song_artist - song_title.
    "song_title": null, //Recommended.
    "song_artist": null, //Recommended.
    "desc": null //Optional.
    };
    for(property in p){
        res[property] = p[property];
    }
    return res;
}

this.output_tiles = function(){
    var l = at.me.results.length;
    var tid = "atp-main-middle-music-tiles";
    
    //Results have changed, output new tiles.
    //|| (netjs.http.lock && !at.me.final_tiles && l != 0)
    if(at.me.results_no != l || (!netjs.http.lock && !at.me.final_tiles && l != 0)){
        //Build HTML here.
        title_table_width = 0;
        for(var i = 0; i < l; i++){
            larger = at.me.results[i]["q"].length > at.me.results[i].serv_prov.length ? at.me.results[i]["q"].length : at.me.results[i].serv_prov.length;
            width = larger * 11;
            title_table_width += larger;
        }
        html = '<div class="tile_table"><div class="tile_tr">';
        if(netjs.http.lock){
            html += '<div class="tile_td"><div class="tile_box" style="width: 70px; "><img src="loading.gif"></div></div>';
        }
        
        
        for(var i = l - 1; i != -1; i--){
            larger = at.me.results[i]["q"].length > at.me.results[i].serv_prov.length ? at.me.results[i]["q"].length : at.me.results[i].serv_prov.length;
            width = larger * 11;
            html += '<div class="tile_td"><div class="tile_box" style="width: ' + (width + 80) + 'px;"><img src="/' + at.htmlspecialchars(at.me.results[i].serv_prov) + '.png" style="margin-top: 4px; float:left;"><span style="font-size: 22px; text-align: left; float: left;  margin-left: 10px; margin-top: 8px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width: ' + width + 'px;"><u><a href="#" style="color: white;" onclick=\'at.enable_me_results = 0; at.me.play.' + at.htmlspecialchars(at.me.results[i].serv_prov) + '.serv_res = at.me.results[' + i + ']["serv_res"]; at.me.play.' + at.htmlspecialchars(at.me.results[i].serv_prov) + '.main();\' title="' + at.htmlspecialchars(at.me.results[i]["q"]) + '">' + at.htmlspecialchars(at.me.results[i]["q"]) + '</a></u><br>-&nbsp;' + (at.me.results[i].serv_prov == "fshared" ? "4shared" : at.htmlspecialchars(at.me.results[i].serv_prov)) + '</span></div></div>';
        }
        html += '<div class="tile_td"><div class="tile_box" style="width: 300px;"><div style="height: 50px; float: left;">&nbsp;</div><span style="font-size: 22px; text-align: left; float: left;  margin-left: 10px; margin-top: 8px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width: 209px;"><form id="me_search" onsubmit="at.me.me_search(); return false;" style="margin: 0px; white-space: nowrap; padding: 0px; display: block;"><input name="me_q" style="width: 200px; float: left; margin: 0px; white-space: nowrap; padding-top: 0px; padding-bottom: 0px; padding-left: 5px; padding-right: 5px; text-align: center; display: block; background: #7D7E7D; color: white; border: 1px solid gray; -moz-border-radius: 15px; border-radius: 15px;"></form></span><a href="#" onclick="at.me.me_search();"><img src="/search.png" style="float:left; margin-top: 8px;"></a></div></div>';
        html += '</div></div>';
        document.getElementById(tid).innerHTML = html;
        if(!netjs.http.lock && !at.me.final_tiles){
            at.me.final_tiles = 1;
        }
    }
    
    //Update results no.
    at.me.results_no = at.me.results.length;
}

this.clean_html = function(s){
    //decodeURIComponent(html_entity_decode(result[1].replace(/\s+/gi, " ")));
    
    //Remove tags from text and convert symbols.
    html_tag = /<[^<>]+>/gi
    s = s.replace(html_tag, "");
    //Fix HTML entities.
    s = html_entity_decode(s);
    //Normalize spaces.
    s = s.replace(/\s+/gi, " ");
    
    return s;
}

this.like = function(s1, s2){
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

//Should this be OO or just namespace based?
//namespace for now, investigate OO later
/*
All of the functions can be hooked and overwritten.
Allows for crazy dynamic code. Note: Pointers are
awesome.

This allows for easy testing of alternative algorithms
for auto-play and filter. The aim is also for a fault
tolerant system of routers which are user-contributed.

*/


this.serv_lookup = {"1": "youtube", "youtube": "1"};
  





this.reset_music_container = function(){
/*
The functions in the ME change the music
container a lot and some of these changes
may destroy things for future functions
so this restores the state. Call it before
outputting a new player.
*/
    at.clear_element("atp-main-middle-music-content-yt");
    html = '<center><div id="atp-main-middle-music-content-yt"></div></center>';
    document.getElementById("atp-main-middle-music-content").innerHTML = html;
    return 1;
};

this.play = new function(){
    //this.serv_prov = null;
    //this.serv_res = null;
    //this.tile_list = null;
    
    //Restore middle music content.
    //at.me.reset_music_container();
    
    this.fshared = new function(){
        this.track_i = null;
        this.pl_i = null;
        this.callback_name = null;
        this.serv_res = null;
        this.tile_list = null;
        this.main = function(){
            //Output HTML.
            if(at.me.play.fshared.serv_res != null){
                html = '<center><p><embed src="' + at.htmlspecialchars(at.me.play.fshared.serv_res) + '" width="420" height="250" allowfullscreen="true" allowscriptaccess="always"  flashvars="autostart=true"></embed><p>If this stops working sign into your account at www.4shared.com. -- Registration is quick and free if you don\'t have an account. Do it, trust me it\'s worth it.';
                document.getElementById("atp-main-middle-music-content").innerHTML = html;
            }
/*
Use global filter if specific one is undefined
otherwise use them both. Global can always be
turned off if undesirable.
*//* Ignore all this crap for now.
            var filter = null;
            if(typeof(at.me.filter.fshared) == "undefined"){
                filter = function(){ at.me.filter.globf.main(); };
            }
            else{
                filter = function(){ at.me.filter.globf.main(); at.me.filter.fshared.main(); };
            }

The purpose is to find + play a resource
so if it already exists we can just play
without searching for it.

            if(at.me.play.fshared.serv_res != null){
                //Play.
                //Todo: Add to tile.
                at.me.play.fshared.asynch_callback();
            }
            else{
                //Get results.
                if(at.me.play.fshared.tile_list == null){
                    at.search.fshared.track_i = at.me.play.fshared.track_i;
                    at.search.fshared.pl_i = at.me.play.fshared.pl_;
                    at.search.fshared.start_index = 0;
                    at.search.fshared.max_results = 10;
                    at.search.fshared.callback_name = "at.me.play.youtube.asynch_callback";
                    
                }
                
                //Filter results.
                at.me.play.fshared.tile_list = filter(at.me.play.fshared.tile_list);
                
                //play f_r
            }
            //error check
            //check that filter hasnt eliminated all results
            return 0;
            */
        };
        
        this.asynch_callback = function(){
            return 0;
        };
        
    };
    
    this.soundcloud = new function(){
        this.track_i = null;
        this.pl_i = null;
        this.callback_name = null;
        this.serv_res = null;
        this.tile_list = null;
        this.main = function(){
            //Output HTML.
            if(at.me.play.soundcloud.serv_res != null){
                html = '<p><iframe width="100%" height="166" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F' + at.htmlspecialchars(at.me.play.soundcloud.serv_res) + '&show_artwork=true&auto_play=true&buying=flase&liking=false&show_comments=false&show_user=false&sharing=false&show_playcount=false"></iframe>';
                document.getElementById("atp-main-middle-music-content").innerHTML = html;
            }
        };
    };
    
    this.youtube = new function(){
        this.track_i = null;
        this.pl_i = null;
        this.callback_name = null;
        this.serv_res = null;
        this.tile_list = null;
        this.main = function(){
            //Output HTML.
            if(at.me.play.youtube.serv_res != null){
                at.player.skin.output_ytplayer(at.me.play.youtube.serv_res);
            }
        };
    };
    
    /*
    this.auto = new function(){
        this.track_i = null;
        this.pl_i = null;
        this.serv_prov = null;
        this.serv_res = null;
        this.tile_lit = null;
        this.callback_name = null;
        this.main = function(){
            //For ea ser provider . . . call play.serv_pro(. . .);
            //Search for 10 results per provider
            if(serv_prov == null && serv_res == null && tile_list != null){
                //filter tile list with glob
                //play the first one with a valid res and serv provider
                //by recursion ofc
            }
            return 0;
        };
        this.asynch_callback = function(){
            return 0;
        };
    };
    */
};


this.filter = new function(){
    
    this.globf = new function(){
        //Params.
        this.enabled = 1;
        this.result_list = null;
        this.callback_name = null;
        this.q = null;
        
        
        this.main = function(){
            result_list_filtered = [];
            
            if(!at.me.filter.globf.enabled) return 0;
            
            //Split q into terms.
            q_t = at.me.filter.globf.q.toLowerCase().split(/\s+/);
            
            //Skip blank results.
            for(var i = 0; i < q_t.length; i++){
                if(q_t[i].match(/^\s*$/) != null){
                    q_t.splice(i, 1);
                }
            }
            
            //Filter results
            for(var i = 0; i < at.me.filter.globf.result_list.length; i++){
                //Split result into terms.
                r_t = at.me.filter.globf.result_list[i]["q"].toLowerCase().split(/\s+/);
                
                //Skip blank results.
                for(var k = 0; k < r_t.length; k++){
                    if(r_t[k].match(/^\s*$/) != null){
                        r_t.splice(k, 1);
                    }
                }
/*
I know what you're thinking but this approach just seems stupid.
The relevance of search results returned by YouTube, Google,
Bing, etc, are not to be discarded based on their divergence from
the stated query. This is because they are returned based on
several metrics which would be thrown out if we go with this
approach. I'm talking about behavioral approaches but let us
test this theory. The qualifiers seem fine and go well with
this filter.

No prove me wrong faggot, im a genius
*/
                
                //Count redundancy relative to q (rough.)
                red_no = r_t.length; //Start at 100% redundancy.
                match_no = 0; //Entropy starts at 0.
                red_list = []; //Hold redundancy.
                q_t_temp = q_t.slice(0);
                r_t_temp = r_t.slice(0);
                for(var j = 0; j < r_t.length; j++){
                    k = 0;
                    for(; k < q_t_temp.length; k++){
                        if(at.me.like(r_t[j], q_t_temp[k])){
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
                if(red_no / ((q_t.length + r_t.length) - match_no) > 0.4){
                    continue;
                }
                
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
                    /fandub/gi, /piano/i
                ];
                ///piano/i
/*
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
                        if(red_list[j].match(red_dis_list[k]) != null){
                            skip = 1;
                        }
                    }
                }
                if(skip) continue;
                
                //This result passed, append to filtered results.
        result_list_filtered.push(at.me.filter.globf.result_list[i]);
            }
            
            at.me.ret = result_list_filtered;
        }
        
        this.asynch_callback = function(){
            //Place holder for any asynch code.
            return 0;
        };
        
        this.on = function() { at.me.filter.globf.enabled = 1; };
        this.off = function() { at.me.filter.globf.enabled = 0; };
    };
    

    
    /*
    this.new_filter = function(){
        var filter = new function(){
            this.do = null;
            this.enabled = 1;
            this.on = function() { enabled = 1; };
            this.off = function() { enabled = 0; };
        };
        return filter;
    };
    */
    
};

this.meta_search = new function(){
/*
Todo: . . . In order to avoid warnings from the big
search engines for automated queries we will instead use
multiple search engines and switch between them. That way
we aren't hitting one hard and there will be less probability
of getting banned. Google offers 100 free API calls -- switch
between this and normal Google search too -- switch up the
queries.

For now, just use bing. Haven't been able to reproduce the
Bing captcha. Make sure to use regex on the captcha page
for the image we found.

I heard that Bing and Yahoo use the same results
but would getting banned on one get you banned on the other?
Suppose Yahoo could be a contender for the meta-search engine
too.

But for now, avoid Google and use Bing. The meta-search engine
is a future prospect. Bing is actually good enough for our
purposes. Hopefully we don't get banned because then the
alternative is 4shared search, which fucking sucks.

Oh yeah -- solving the Google captcha needs cookies enabled
so http.open will need a cookie engine. A captcha seems to
give us a ~5 minute window for more searches and ~55 queries.
These limits fluctuate wildly indicating we have no 
fucking clue how the algorithm really works. lol true
*/
};

this.search = new function(){
    this.bing = new function(){
/*
Notes: This function doesn't return real time
results. It doesn't need to. It also doesn't set
song_title, and song_artist in it's results but it
does set desc.
*/
        //Todo: Upgrade this so the max results specifiers and
        //offset code work. Also, use the glob filter on results.
        //Params section.
        this.p = null;
        //Might be some kind of ID.
        this.form = null;
        
        this.main = function(){
            //Fail if Java is disabled or not ready.
            if(!at.enable_java || !netjs_ready)
            {
                at.me.ret = 0;
                        at.me.future_callback(at.me.search.bing.p["callback_name"]);
                return;
            }
            
            //10 results per page.
            var page_res_no = 10;
            var q = encodeURIComponent(at.me.search.bing.p["q"]);
            http://www.bing.com/search?q=sdfsdfsdfsdf&go=&qs=ns&form=QBRE&filt=all
            var url = "http://www.bing.com/search?q=" + q + "&go=&qs=ns&form=QBRE&filt=all&first=" + (((at.me.search.bing.p["start_index"] - 1) * page_res_no) + 1);
            if(at.me.search.bing.form != null){
                url += "&form=" + encodeURIComponent(at.me.search.bing.form);
            }

            //No POST data.
            var data = null;
            //No timeout.
            var timeout = null;
            at.me.ret = netjs.http.open(url, data, timeout, "at.me.search.bing.asynch_callback");
            if(at.me.ret != 1){
                at.me.future_callback(at.me.search.bing.p["callback_name"]);
                return;
            }
        };
        
        this.asynch_callback = function(){
            reply = netjs.http.recv_buffer;
            //alert(reply);
            //document.myform.outputform.value = reply;
            //Strip search form's important info:
            /*
            gform = /input\s*name\s*=\s*"form"\s*type\s*=\s*"hidden"\s*value\s*=\s*"([^"]+?)"/gi.exec(reply);
            if(gform != null){
                if(typeof(gform[1]) != "undefined"){
                    me.search.bing.form = gform[1];
                }
            }
            */
            le_ret = [];
            result = null;
            
            //result_pattern = /sa_wr.*?a\s*href\s*=\s*"([^"]+?)"[^>]+>(.+?)<.a>.*?<p>(.*?)<.p>/gi
            result_pattern = /<\s*h3\s*>\s*<\s*a\s*href\s*=\s*"([^"]+?)"[^>]+>([\s\S]*?)<.a><.h3>[\s\S]*?<p>([\s\S]*?)<[\/\\]p>/gi
            while((result = result_pattern.exec(reply))){
                //Remove tags from text and convert symbols.
                result[1] = at.me.clean_html(result[1]);
                result[2] = at.me.clean_html(result[2]);
                result[3] = at.me.clean_html(result[3]);
                res = at.me.new_result({"serv_res": result[1], "serv_prov": "bing", "q": result[2], "desc": result[3]});
                le_ret.push(res);
            }
            //Fuck the police.
            if(le_ret == []){
                at.me.ret = 0;
            }
            else{
                at.me.ret = le_ret;
            }
            at.me.future_callback(at.me.search.bing.p["callback_name"]);
            return;
        };
    };
    
    //Be nice Google. Don't be evil.
    //I'll seriously buy an API key when I can afford to.
    this.google_hack = new function(){
/*
Works -- but a few things need to be changed, didn't
restore it after development but easily fixed. Leave it
for now.

Notes: This function doesn't return real time
results. It doesn't need to. It also doesn't set
song_title, and song_artist in it's results but it
does set desc.

Strip out the Google captcha and find a way to silve it.
*/
        //Todo: Upgrade this so the max results specifiers and
        //offset code work. Also, use the glob filter on results.
        //Params section.
        this.p = null;
        
        //Search box hidden vars.
        this.gbv = null;
        this.sei = null;
        //this.q = null;
        //this.start_index = null;
        //this
        //q, start_index, max_results, id, callback_name
        
        this.main = function(){
            //Fail if Java is disabled or not ready.
            if(!at.enable_java || !netjs_ready)
            {
                at.me.ret = 0;
                at.me.future_callback(at.me.search.google_hack.p["callback_name"]);
                return;
            }
            
            //10 results per page.
            var page_res_no = 10;
            var q = encodeURIComponent(at.me.search.google_hack.p["q"]);
            var url = "http://www.google.com/search?q=" + q + "&btnG=Search&start=" + (at.me.search.google_hack.p["start_index"] * page_res_no);
            if(at.me.search.google_hack.gbv != null){
                url += "&gbv=" + encodeURIComponent(at.me.search.google_hack.gbv);
            }
            if(at.me.search.google_hack.sei != null){
                url += "&sei=" + encodeURIComponent(at.me.search.google_hack.sei);
            }
            //No POST data.
            var data = null;
            //No timeout.
            var timeout = null;
            at.me.ret = netjs.http.open(url, data, timeout, "at.me.search.google_hack.asynch_callback");
            if(at.me.ret != 1){
                at.me.future_callback(at.me.search.google_hack.p["callback_name"]);
                return;
            }
        };
        
        this.asynch_callback = function(){
            alert("cb");
            //Parse results.
            //Todo: Execute Javascript to better resemble web-browser.
            //Be careful here, crazy XSS potential from Google.
            //Don't want to join their Botnet now. Constant vigilance.
            reply = netjs.http.recv_buffer;
            alert(reply);
            return;
            //document.myform.outputform.value = reply;
            //alert(reply);
            redirect = /Please click <\s*a\s*href\s*=\s*["]([^"]+)["]/gi.exec(reply);
            data = null; var timeout = null;
            if(redirect != null){
                //alert("redirect");
                redirect = redirect[1];
                //alert(redirect);
                at.me.ret = netjs.http.open(redirect, null, null, "at.me.search.google_hack.asynch_callback");
                if(at.me.ret != 1){
                    at.me.future_callback(at.me.search.google_hack.p["callback_name"]);
                    return;
                }
            }
            else{
                //Strip search form's important info:
                gform = /hidden["]\s*name\s*=\s*["]gbv["]\s*value\s*=\s*["]([0-9]+)["][\s\S]*name\s*=\s*["]sei["]\s*value\s*=\s*["]([^"]+)["]/gi.exec(reply);
                if(gform != null){
                    if(typeof(gform[1]) != "undefined"){
                        at.me.search.google_hack.gbv = gform[1];
                    }
                    if(typeof(gform[2]) != "undefined"){
                        at.me.search.google_hack.sei = gform[2];
                    }
                }
                le_ret = [];
                result = null;
                result_pattern = /<\s*li\s*class\s*=\s*["]g["]\s*>\s*<\s*h3\s*class\s*=\s*["]r["]\s*>\s*<\s*a href\s*=\s*["]([^"]+)["]\s*>([\s\S]*?)<.a>[\s\S]*?=\s*["]s["]\s*>([\s\S]*?)<\s*div\s*>[\s\S]*?<\/li>/gi
                while(result = result_pattern.exec(reply)){
                    alert("yes");
                    //Todo: remove tags.
                    result_url = /\/url[?]q\s*=\s*([^&]+)&/gi
                    //Patch URL;
                    result[1] = result_url.exec(result[1]);
                    if(!result[1]) continue;
                    result[1] = result[1][1];
                    result[1] = decodeURIComponent(at.me.clean_html(result[1]));
                    
                    //Remove tags from text and convert symbols.
                    result[2] = at.me.clean_html(result[2]);
                    result[3] = at.me.clean_html(result[3]);
                    alert(result[1]);
                    alert(result[2]);
                    alert(result[3]);
                    
                    le_ret.push(at.me.new_result({"serv_res": result[1], "serv_prov": "google_hack", "q": result[2], "desc": result[3]}));
                }
                //Fuck the police.
                if(le_ret == []){
                    at.me.ret = 0;
                }
                else{
                    at.me.ret = le_ret;
                }
                //me.future_callback(me.search.google_hack.p["callback_name"]);
            
                var output = '';
                for(i = 0; i < at.me.ret.length; i++){
                    for (property in at.me.ret[i]) {
                      output += property + ': ' + at.me.ret[i][property]+'; ';
                    }
                }
                //alert(output);
                return;
                //alert(me.ret);
            }
            //document.write(results);
        };
    };
    
    //Coup de grace, faggots.
    this.fshared = new function(){
/*
Limitations
-------------
Traffic: >900MB / 24h = 300 songs / 24h (@3mb / song)
Login: Required
*/
        //Todo: upgrade this to return multiple results
        //currently it's not done because it takes too long
        //and we don't want to keep Bob waiting for his illegal
        //free music.
        //Params section.
        this.p = null;
        
        this.found = 0;
        this.i = 0;
        this.bing_results = null;
        this.download_page = null;
        
        this.main = function(){
            //Cleanup.
            at.me.search.fshared.bing_results = null;
            at.me.search.fshared.found = 0;
            at.me.search.fshared.i = 0;
            at.me.search.fshared.download_page = null;
            
            if(!at.enable_java || !netjs_ready)
            {
                at.me.ret = 0;
                at.me.future_callback(at.me.search.fshared.p["callback_name"]);
                return;
            }
    
            //4shared search sucks, we start by using Google's.
            //Hello I'm Johnny, I hack stuff.
            //query = "site:4shared.com inurl:/mp3/ -folder -\"no longer available\"" + me.search.fshared.p["q"];
            //me.search.google_hack.p = {"start_index": 0, "q": query, "max_results": 10, "id": null, "callback_name": "me.search.fshared.asynch_callback"};
            //me.search.google_hack.main();
            query = "site:www.4shared.com mp3 " + at.me.search.fshared.p["q"];
            at.me.search.bing.p = {"start_index": 1, "q": query, "result_no": at.me.search.fshared.p["result_no"], "id": null, "callback_name": "at.me.search.fshared.asynch_callback"};
            at.me.search.bing.main();
        };
        this.asynch_callback = function(){
            //callback = function(){
/*
This function is contrived. Fuck I hate asynch callbacks.
Someone needs to fix this shit. Fix Stratified Javascript.
*/
            //Set bing results.
            if(at.me.search.fshared.bing_results == null){
                if(typeof(at.me.ret[0]) != "object"){
                    at.me.ret = 0;
                    at.me.future_callback(at.me.search.fshared.p["callback_name"]);
                    return;
                }
                
                //Strip crap from 4shared title.
                fshared_bing_filter = /[.]mp3[\s\S]*/i
                for(var j = 0; j < at.me.ret.length; j++){
                    if(typeof(at.me.ret[j]) != "undefined"){
                        if(typeof(at.me.ret[j]["q"]) != "undefined"){
                            //alert(me.ret[j]["q"]);
                            at.me.ret[j]["q"] = at.me.ret[j]["q"].replace(fshared_bing_filter, "");
                            /*
                            q_f = fshared_bing_filter.exec(me.ret[j]["q"]);
                            if(!q_f){
                                me.ret[j]["q"] = me.search.fshared.p["q"];
                            }
                            else{
                                me.ret[j]["q"] = q_f[1];
                            }
                            */
                            //alert(me.ret[j]["q"]);
                        }
                    }
                }
                /*for(var k = 0; k < me.ret.length; k++){
                    alert(me.ret[k]["q"]);
                }*/
                /*
Todo: Strip qualifiers fails if they appear in the title
at the end of it and the title has been truncated (such as what)
occurs in search engines. Also use the filter on the title
on the download page. Bing, not in title function?

Todo: Implement Javascript clean and apply to result.

Don't get the title from the <h1> get it from the title
on the download page
why the fuck is that not found error occurring? -- mutex I think
why is that first result being chopped off? -- invalid regex
fandub add to

705 -- original mix false positive
                */
                
                //Apply global filter.
                //alert(me.search.fshared.p["q"]);
 
                //alert("start filter");
                
                at.me.filter.globf.q = at.me.search.fshared.p["q"];
                at.me.filter.globf.result_list = at.me.ret;
                at.me.filter.globf.main();
                
                /*for(var k = 0; k < me.ret.length; k++){
                    alert(me.ret[k]["q"]);
                }*/

                //Store filtered results.
                at.me.search.fshared.bing_results = at.me.ret;
                at.me.ret = 1;
                netjs.http.recv_buffer = null;
            }
            
            //Validity check.
            if(at.me.ret != 1){
                at.me.ret = 0;
                at.me.future_callback(at.me.search.fshared.p["callback_name"]);
                return;
            }
            
            //Set download page.
            if(netjs.http.recv_buffer != null){
                at.me.search.fshared.download_page = netjs.http.recv_buffer;
            }
            else{
                netjs.http.recv_buffer = null;
            }
            //alert("dl page" + me.search.fshared.download_page);
            
            //alert("herere1");
            //Just encase it doesn't find any.
            at.me.ret = 0;
            for(; at.me.search.fshared.i < at.me.search.fshared.bing_results.length && at.me.search.fshared.found != at.me.search.fshared.p["result_no"]; at.me.search.fshared.i++){
                //Get the download page.
                if(at.me.search.fshared.download_page == null){
                    //alert("getting dl page");
                    valid_resource = /(\/mp3\/)|(\/audio\/)/gi
                    at.me.search.fshared.bing_results[at.me.search.fshared.i]["serv_res"] = at.me.search.fshared.bing_results[at.me.search.fshared.i]["serv_res"].replace(/\/get\//gi, "\/mp3\/");               
                   //alert(me.search.fshared.bing_results[me.search.fshared.i]["serv_res"]);
                    if(!valid_resource.exec(at.me.search.fshared.bing_results[at.me.search.fshared.i]["serv_res"])){
                        //alert("skipping");
                        continue;
                    }
                    at.me.ret = netjs.http.open(at.me.search.fshared.bing_results[at.me.search.fshared.i]["serv_res"], null, null, "at.me.search.fshared.asynch_callback");
                    return;
                }            
                else{ //Strip file resource.
                    reply = netjs.http.recv_buffer;
                    netjs.http.recv_buffer = null;
                    //alert(reply);
                    //document.myform.outputform.value = reply;
                    //Todo: possible problem with the regex matching all characters
                    //between "" or '' -- It doesn't take into account escaping.
                    //Ignore for now.
                    serv_res = /"&lt;embed src=&quot;([\s\S]*?)&quot;/gi
                    serv_res = serv_res.exec(reply);
                    q = /<\s*title\s*>([^<>]+?)<\/title\s*>/gi
                    q = q.exec(reply);
                    desc = /<\s*meta\s*name\s*=\s*"description"\s*content\s*=\s*"([\s\S]*?)"\s*>/gi
                    desc = desc.exec(reply);
    
                    if(!serv_res || !q || !desc){
                        at.me.search.fshared.download_page = null;
                        continue;
                    }
                    serv_res = at.me.clean_html(serv_res[1]);
                    q = at.me.clean_html(q[1]);
                    q = q.replace(/[.]mp3[\s\S]*/i, "");
                    //alert(q); //alert(me.search.fshared.bing_results[me.search.fshared.i]["serv_res"]);
                    desc = at.me.clean_html(desc[1]);
                    
                    //Apply global filter to result.
                    at.me.filter.globf.q = at.me.search.fshared.p["q"];
                    at.me.filter.globf.result_list = [at.me.new_result({"q": q})];
                    at.me.filter.globf.main();
                    //alert(me.ret.length);
                    //alert(me.ret[0]["q"]);
                    if(!at.me.ret.length) continue;
                    
                    //Add to real time results.
                    at.me.results.push(at.me.new_result({"q": q, "serv_res": serv_res, "serv_prov": "fshared", "desc": desc}));
    
                    //document.write(mp3);
                    //alert(mp3);
                    //me.ret = mp3;
                    at.me.search.fshared.found += 1;
                    at.me.search.fshared.download_page = null;
                }
            }
            
            //Return all results.
            if(at.me.search.fshared.found){
                at.me.ret = at.me.results;
            }
            
            //Final callback.
            at.me.future_callback(at.me.search.fshared.p["callback_name"]);
        };
    };
    
    this.soundcloud = new function(){
    /*
    Limitations
    -------------
    API call restrictions
    */
        //Params section.
        this.p = [];

    	this.limit = 10;
		this.client_id = "5571395eef6b7144681c9d76d3bead7d";

        this.main = function(){
            /*
            if(netjs.http.lock){
                 at.me.ret = 0;
            at.me.future_callback(at.me.search.soundcloud.p["callback_name"]);
                return;
            }
            netjs.http.lock = 1;
            */

            query = at.me.search.soundcloud.p["q"];
			url = "http://api.soundcloud.com/tracks.json?client_id=" + encodeURIComponent(at.me.search.soundcloud.client_id) + "&q=" + encodeURIComponent(query) + "&filter=streamable&limit=" + at.me.search.soundcloud.limit + "&callback=at.me.search.soundcloud.proc_results";
            
			if(window.XMLHttpRequest){ // Mozilla, Safari, ...
                httpRequest = new XMLHttpRequest();
            } else if (window.ActiveXObject){ // IE
                  try {
                    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                  } 
                  catch (e) {
                    try {
                      httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } 
                    catch (e) {}
                  }
            }
            
            if (!httpRequest) {
                at.me.ret = 0;
                            at.me.future_callback(at.me.search.soundcloud.p["callback_name"]);
                return;
            }
            httpRequest.onreadystatechange = at.me.search.soundcloud.check_results;
            httpRequest.open('GET', url);
            httpRequest.send();
        };
        
        this.check_results = function(){
            if (httpRequest.readyState === 4) {
                if (httpRequest.status === 200) {
                    eval(httpRequest.responseText);
                }
                else{
                    at.me.search.soundcloud.proc_results(null);
                }
            }
        };
        
        this.proc_results = function(r){
            if(r == null){
                at.me.ret = 0;
            at.me.future_callback(at.me.search.soundcloud.p["callback_name"]);
                return;
            }
            
            var results = [];
            for(i = 0; i < r.length; i++){
                //Add to real time results.
                res = at.me.new_result({"q": r[i]["title"], "serv_res": r[i]["id"], "serv_prov": "soundcloud"});
                at.me.results.push(res);
                
                //Add to all results.
                results.push(res);
            }
            
            //Return all results.
            if(results != []){
                at.me.ret = results;
            }
            
            //Final callback.
            at.me.future_callback(at.me.search.soundcloud.p["callback_name"]);
        };
    };
    
    this.youtube = new function(){
    /*
    Limitations
    -------------
    API call restrictions
    */
        //Params section.
        this.p = [];
    
        this.main = function(){
            query = at.urlencode(at.me.search.youtube.p["q"]);
            ip_addr = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
            url = "http://gdata.youtube.com/feeds/api/videos?paid-content=false&safeSearch=strict&max-results=10&v=2&alt=json-in-script&format=5&callback=at.me.search.youtube.proc_results&q=" + query;
        
            if(ip_addr.match(/[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+/gi) != null){
                url += "&restriction=" + at.urlencode(ip_addr);
            }
            //alert(url);
            //return;
            
    		if(window.XMLHttpRequest){ // Mozilla, Safari, ...
                httpRequest = new XMLHttpRequest();
            } else if (window.ActiveXObject){ // IE
                  try {
                    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                  } 
                  catch (e) {
                    try {
                      httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } 
                    catch (e) {}
                  }
            }
            
            if (!httpRequest) {
                at.me.ret = 0;
                            at.me.future_callback(at.me.search.youtube.p["callback_name"]);
                return;
            }
            httpRequest.onreadystatechange = at.me.search.youtube.check_results;
            httpRequest.open('GET', url);
            httpRequest.send();
        };
        
        this.check_results = function(){
            if (httpRequest.readyState === 4) {
                if (httpRequest.status === 200) {
                    eval(httpRequest.responseText);
                }
                else{
                    at.me.search.youtube.proc_results(null);
                }
            }
        };
        
        this.proc_results = function(data){
            if(data == null){
                at.me.ret = 0;
            at.me.future_callback(at.me.search.youtube.p["callback_name"]);
                return;
            }
            
            var results = [];
            
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
            var url = null;
            if(data != null){ //Use vid from search result.
                feed = data.feed;
                entries = feed.entry || [];
                for(var i = 0; i < entries.length; i++){
                    entry = entries[i];
                    title = entry.title.$t;
                    url = entries[i].media$group.media$content[0].url;
                    p = /^.*?((youtu.be\/)|(v\/)|(e\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*?/
                    vid = p.exec(url);
                    if(typeof(vid[7]) != "undefined"){
                        vid = vid[7];
                        //Add to real time results.
                        res = at.me.new_result({"q": title, "serv_res": vid, "serv_prov": "youtube"});
                        at.me.results.push(res);
                        
                        //Add to all results.
                        results.push(res);
                    }
                }
            }
            
            //Return all results.
            if(results != []){
                at.me.ret = results;
            }
            
            //Final callback.
            at.me.future_callback(at.me.search.youtube.p["callback_name"]);
        };
    };
    
    this.template = new function(){
        //Params section.
        this.p = null;
        
        this.main = function(){
            if(!at.enable_java || !netjs_ready)
            {
                at.me.ret = 0;
                at.me.future_callback(at.me.search.template.p["callback_name"]);
                return;
            }
        
        };
        
        this.asynch_callback = function(){
            at.me.future_callback(me.search.template.p["callback_name"]);
        };
    };    
};

this.download = new function(){
/*
This is a place holder.
Expect us.

Idea guys: If we upgrade netjs to support the server functions we can create a virtual web server that runs on the user's localhost. Then using the client functions we can patch into the Gnutella network and other networks. To serve the content we just get the user to talk to their own web server. All the content is held in memory. Javascript Limewire, anyone? h0hh0h0

k.. that is fucking genius. Added to list

iknowright. ph33r

Note:
Use asynch callback structure for all
routes even if they don't need them.
We need a consistent calling pattern.
*/
    /*
    this.youtube = new function(){
        this.tile_list = null;
        this.callback_name = null;
        this.main = function(){
            return 0;
        };
        
        this.asynch_callback = function(){
            return 0;
        }
        
    };
    The last step of the chain will call the callback.
    Er, we need a status thing. . .
    */
};

};

//Load music engine code into at.me namespace.
//Very important. This is evaled. To load code into ns.
at.me = me;
