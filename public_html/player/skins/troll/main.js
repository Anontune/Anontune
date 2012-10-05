/***
*    begin skin
*    begin skin
*/

/*
This module defines all the application logic for the skin interface.
*/

skin = new function(){
    
this.result_no = 0;
this.menu_filter_timeout = null;
this.pls_scroll_top = 0;

this.menu_filter = function(){
    filter = $(".search_input").val();
    
    //Show changes.
    if(at.pl_i != null){ //Open pl.
		//Apply to open pl.
		if(filter == ""){
			for(var i = 0; i < at.pls[at.pl_i]["tracks"].length; i++){
				at.pls[at.pl_i]["tracks"][i]["filter"] = true;
			}
			at.player.swap_pl_by_filter();
			at.player.skin.load_playlist(at.pl_i);
			$(".menu_list").scrollTop(0);
			return;
		}
		
		at.player.set_search('at.pls[' + at.pl_i + ']["tracks"]', ["title", "artist_name"], filter);
		at.player.swap_pl_by_filter();
		at.player.skin.load_playlist(at.pl_i);
	}
	else{ //List pl.
		//Apply to playlists.
		at.player.set_search('at.pls', ["name"], filter);
		at.player.swap_pls_by_filter();
		at.player.skin.load_playlists();
	}
    
    $(".menu_list").scrollTop(0);
    at.player.skin.menu_filter_timeout = null;
}

this.try_schedule_menu_filter = function(){
	if(at.player.skin.menu_filter_timeout == null){ //Schedule.
		at.player.skin.menu_filter_timeout = setTimeout("at.player.skin.menu_filter();", 1000);
	}
	else{ //Reschedule.
		clearTimeout(at.player.skin.menu_filter_timeout);
		at.player.skin.menu_filter_timeout = setTimeout("at.player.skin.menu_filter();", 1000);
	}
}

this.search = function(){
	var q = $(".search_input").val();
	q = q.split(/\s*-\s*/);
	if(q.length >= 2){
		//at.enable_te_next = false;
		at.player.skin.auto_play({"title": q[1], "artist": q[0]});
		$(".add_track_input").val(q[1]);
		$(".add_artist_input").val(q[0]);
	}
	if(q.length == 1){ //Artist discogrpahy.
		var url = "http://www.musicbrainz.org/ws/2/release-group/?query=release:" + at.urlencode(q[0]);
		var xml = at.http_get(url);
		var json_string = X2JS.xml_str2json(xml);
		//alert(json_string.metadata._created);
		//alert(json_string.metadata["release-group-list"]["release-group"][0].id);
		//alert(xml.metadata._created);
		//alert(json_string);
	}
}

this.shuffle = function(){
	if(at.pl_i == null) return;
	at.player.shuffle(at.pl_i);
	at.player.skin.load_playlist(at.pl_i);
}

this.enable_loop = function(){
	html = '<a href="#" onclick="at.player.skin.disable_loop();"><img src="/images/troll/loop_active.png"></a>';
	$(".loop_control").html(html);
	at.loop = 1;
}

this.disable_loop = function(){
	html = '<a href="#" onclick="at.player.skin.enable_loop();"><img src="/images/troll/loop.png"></a>';
	$(".loop_control").html(html);
	at.loop = 0;
}

this.enable_play = function(){
	//No resources hooked.
	if(!at.res_hook) return;
	at.player.play_track();
	html = '<a href="#" onclick="at.enable_te_next = true; at.player.skin.disable_play();"><img src="/images/troll/play_active.png"></a>';
	$(".play_control").html(html);
}

this.disable_play = function(){
	//No resources hooked.
	if(!at.res_hook) return;
	at.player.pause_track();
	html = '<a href="#" onclick="at.enable_te_next = true; at.player.skin.enable_play();"><img src="/images/troll/play.png"></a>';
	$(".play_control").html(html);
}

this.about = function(){
	at.player.skin.show_overlay("About", "Push music further.");
}

this.license = function(){
	agpl = at.htmlspecialchars(at.http_get("/LICENSE"));
	html = "<div style='margin-left: auto; margin-right: auto; width: 510px; text-align: left;'><pre>" + agpl + "</pre></div>";
	at.player.skin.show_overlay("License", html);
}

this.help = function(){
	at.player.skin.show_overlay("Help", "Check the contact link and someone may be available to answer your questions.");
}

this.date = function(){
	"Fri, 15 Jun 15:15";
	cur_date = new Date();
	f_date = cur_date.format("ddd, dd mmm HH:MM");
	date_html = "<a href='#'>" + f_date + "</a>";
	$(".date").html(date_html);
}


this.add_playlist = function(){
	if(at.auth_password == ""){
		alert("You need to be logged in to do this.");
		return;
	}
	name = $("#add_playlist_input").val();
	if(name == "") return;
	at.player.add_pl({"loaded": true,
	"name": name,
	"parent_id": "0",
	"cmd": "0"});
	at.player.skin.load_playlists();
	$('.menu_list').scrollTop($('.menu_list')[0].scrollHeight);
}

this.del_playlist = function(pl_i){
	if(at.auth_password == ""){
		alert("You need to be logged in to do this.");
		return;
	}
	at.player.del_pl(pl_i);
	at.player.skin.load_playlists();
}

this.add_track = function(){
	if(at.auth_password == ""){
		alert("You need to be logged in to do this.");
		return;
	}
	track = $("#add_track_input").val();
	artist = $("#add_artist_input").val();
	if(track == "" || artist == "") return;
	if(at.pl_i == null){
		alert("Select a playlist first.");
		return;
	}
	
	at.player.add_track({"title": track,
	"artist_name": artist,
	"playlist_id": at.pls[at.pl_i]["id"]}, at.pl_i);
	at.player.skin.load_playlist(at.pl_i);
	$('.menu_list').scrollTop($('.menu_list')[0].scrollHeight);
}

this.del_track = function(track_i, pl_i){
	if(at.auth_password == ""){
		alert("You need to be logged in to do this.");
		return;
	}
	at.player.del_track(track_i, pl_i);
	at.player.skin.load_playlist(pl_i);
}

this.hide_overlay = function(){
	$(".overlay_view").hide();
	$(".overlay_view_container").hide();
	$(".overlay_view_close").hide();
	$(".overlay_view_title").hide();
	$(".overlay").hide();
		
		/*
	overlay_view 1000
overlay_view_container 1001
overlay_view_close 1002
*/
}

this.show_overlay = function(title, content){
	$(".overlay_view_title").html(title);
	$(".overlay").html(content);
	$(".overlay_view").show();
	$(".overlay_view_container").show();
	$(".overlay_view_close").show();
	$(".overlay_view_title").show();
	$(".overlay").show();
}
    
this.load_playlist = function(index){ 
    var id = at.pls[index]["id"];
    //Todo: error checking.
    at.player.load_pl({"id": id, "title": "1", "artist_name": "1"});

    //Clear active track.
    if(at.pl_i != index){
        at.track_i = null;
    }
    
    //Change active container.
    var container = at.pl_i = index;
    
	menu_list = [];
	close_menu = {"menu_icon": "/images/troll/list_playlist.png",
	"title": "Back",
	"description": "Close " + at.pls[index]["name"],
	"main_onclick": "at.pl_i = null; at.player.skin.load_playlists(); $(\".menu_list\").scrollTop(at.player.skin.pls_scroll_top);",
	"action_icon": "/images/troll/action_delete.png",
	"action_onclick": "at.player.skin.load_playlists();"}
	menu_list.push(close_menu);
	
	for(i = 0; i < at.pls[index]["tracks"].length; i++){
		main_onclick = 'at.enable_te_next = true; at.track_i = ' + i + '; ';
		main_onclick += 'at.player.skin.auto_play({"title": ';
		main_onclick += 'at.pls[' + index + ']["tracks"][' + i + ']["title"]';
		main_onclick += ', "artist": at.pls[' + index + ']["tracks"][' + i + ']["artist_name"]';
		main_onclick += '});';
		menu = {
			"menu_icon": "/images/troll/list_unknown.png",
			"title": at.pls[index]["tracks"][i]["title"],
			"description": at.pls[index]["tracks"][i]["artist_name"],
			"main_onclick": main_onclick,
			"action_icon": "/images/troll/action_delete.png",
			"action_onclick": "at.player.skin.del_track(" + i + ", " + index + ");"
		};
		menu_list.push(menu);
	}
	menu_list.push(close_menu);
	menu_list_html = at.player.skin.menu_html(menu_list);
	$(".menu_list").html(menu_list_html);
};

this.menu_html = function(menu_list){
	/*
	 * menu = [
	 * "menu_icon": "",
	 * "title": "",
	 * "description": "",
	 * "main_onclick": "",
	 * "action_icon": "",
	 * "action_onclick": ""
	 * 
	 **/
	 menu_html = "";
	 for(i = 0; i < menu_list.length; i++){
		 menu_html += "<div class='menu_list_row'";
		 /*
		 if(typeof(menu_list[i]["background"]) != undefined){
			 menu_html += " style='background: " + menu_list[i]["background"] + "'";
		 }
		 */
		 menu_html += ">";
		 if(menu_list[i]["menu_icon"] != null){
			menu_html += "<div class='menu_list_icon'>";
			menu_html += "<a onclick='" + menu_list[i]["main_onclick"] + "'><img src='";
			menu_html += menu_list[i]["menu_icon"] + "'></a></div>";
		}
		 menu_html += "<div class='menu_list_text'>";
		 menu_html += "<a onclick='" + menu_list[i]["main_onclick"] + "'><div class='menu_list_title'>";
		 menu_html += at.htmlspecialchars(menu_list[i]["title"]) + "</div><div class='menu_list_description'>";
		 menu_html += at.htmlspecialchars(menu_list[i]["description"]) + "</div></a></div>";
		 if(menu_list[i]["action_icon"] != null){
			 menu_html += "<div class='menu_list_action'>";
			 menu_html += "<a onclick='" + menu_list[i]["action_onclick"] + "'><img src='";
			 menu_html += menu_list[i]["action_icon"] + "'></a></div>";
		}
		menu_html += "</div>";
	}
	
	return menu_html;
}

this.load_playlists = function(){
    at.player.load_pls();
	menu_list = [];
	for(i = 0; i < at.pls.length; i++){
		//"menu_icon": "/images/troll/list_playlist.png",
		menu = {
			"menu_icon": "/images/troll/list_playlist.png",
			"title": at.pls[i]["name"],
			"description": at.pls[i]["tracks"].length + " songs",
			"main_onclick": "at.player.skin.pls_scroll_top = $(\".menu_list\").scrollTop(); at.pl_i = " + i + "; at.player.skin.load_playlist(" + i + "); $(\".menu_list\").scrollTop(0);",
			"action_icon": "/images/troll/action_delete.png",
			"action_onclick": "at.player.skin.del_playlist(" + i + ");"
		};
		menu_list.push(menu);
	}
	menu_list_html = at.player.skin.menu_html(menu_list);
	$(".menu_list").html(menu_list_html);
}

this.show_main_view = function(name){
	var views = [".play_view", ".search_view"];
	
	//Hide everything.
	for(var i = 0; i < views.length; i++){
		$(views[i]).hide();
		$(views[i]).css("z-index", "5");
		$(views[i]).css("visibility", "hidden");
	}
	
	//Show name.
	$(name).show();
	$(name).css("z-index", "6");
	$(name).css("visibility", "visible");
}

this.show_search_view = function(){
	at.player.skin.show_main_view(".search_view");
}

this.show_play_view = function(){
	at.player.skin.show_main_view(".play_view");
}

this.result_change = function(){
	//Update visual result no.
	if(at.player.skin.result_no != at.me.result_no){
		$(".search_result_no").html('(<a href="#" onclick="at.player.skin.show_search_view();">' + at.me.result_no + ' Results</a>)');
		menu_list1 = [];
		menu_list2 = [];
		for(var i = 0; i < at.me.result_no; i++){
			//"menu_icon": "/images/troll/list_playlist.png",
			//if(at.me.results[i]["accuracy"] == 0) continue;
			menu = {
				"menu_icon": "/images/troll/list_" + at.me.results[i]["type"] + ".png",
				"title": at.me.results[i]["meta"]["title"],
				"description": at.me.results[i]["meta"]["artist"],
				"main_onclick": "at.me.sr != null ? clearTimeout(at.me.sr) : 0; at.me.sr = null; at.me.results[" + i + "][\"played\"] = true; at.player.skin.disable_play(); at.player.skin.play_resource(at.me.results[" + i + "]);",
				"action_icon": null,
				"action_onclick": null
			};
			if(at.me.results[i]["accuracy"] >= 1){
				menu_list1.push(menu);
			}
			else{
				menu_list2.push(menu);
			}
		}
		for(var i = 0; i < menu_list2.length; i++) menu_list1.push(menu_list2[i]);
		
		menu_list_html = at.player.skin.menu_html(menu_list1);
		play_view_back = '<a href="#" onclick="at.player.skin.show_play_view();">Back</a><p><p><p><p>';
		menu_list_html = play_view_back + menu_list_html + play_view_back;
		$(".results_list").html(menu_list_html);
		at.player.skin.result_no = at.me.result_no;
	}
}

this.auto_play = function(q){
	//Add button in searching thing to cancel.
	at.player.skin.result_no = 0;
	at.player.skin.reset_play_view();
	
	//Play results as they become available.
	at.me.sr = setTimeout("at.me.select_result(100);", 1000);
	
	//Start asynch processes that add results.
	if(q != null){
		at.me.find_results({query: at.me.new_query(q)});
	}
}

this.reset_play_view = function(){
	//This also means stopping everything currently playing.
	html = '<a href="#" onclick="at.enable_te_next = true; at.player.skin.enable_play();"><img src="/images/troll/play.png"></a>';
	$(".play_control").html(html);
	//at.player.skin.result_no = -1;
	//$(".search_result_no").html('(<a href="#" onclick="at.player.skin.show_search_view();">0</a>)');
	//$(".results_list").html('<a href="#" onclick="at.player.skin.show_play_view();">Back</a>');
	if(ytplayer != null){
		//ytplayer.stopVideo();
		ytplayer.destroy();
		ytplayer = null;
	}
	$("#jplayer").jPlayer("stop");
	$(".song_title_heading").html("");
	if(at.me.peh != null){
		clearTimeout(at.me.peh);
		at.me.peh = null;
	}
	if(at.me.sr != null){
		clearTimeout(at.me.sr);
		at.me.sr = null;
	}
	
	
}

this.play_resource = function(result){
	at.player.skin.reset_play_view();
	if(at.me.routes[at.me.get_route_index(result)].settings.timeout > 0){
		at.me.peh = setTimeout("at.player.skin.auto_play(null);", at.me.routes[at.me.get_route_index(result)].settings.timeout);
	}

	var title = "&#9834&nbsp;" + at.htmlspecialchars(result["meta"]["title"] + " - " + result["meta"]["artist"]) + "&#9835;";
	$(".song_title_heading").html(title);
	if(result["type"] == "youtube"){
		ytplayer = new YT.Player('ytplayer', {
			height: '390',
			width: '640',
			playerVars: { 'autoplay': 1},
			videoId: result["data"]["vid"],
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange,
				'onError': onPlayerError
			}
		});
	}
	if(result["type"] == "exfm"){
				if(at.me.peh != null){
			clearTimeout(at.me.peh);
			at.me.peh = null;
		}
		
		//Hook.
		at.player.hook_jplayer();
		//alert(result["data"]["url"]);
		
		//Play.
		$("#jplayer").jPlayer("setMedia", {
			mp3: result["data"]["url"]
		}).jPlayer("play");
	}
	at.player.skin.show_play_view();
}

this.lazy_programmers = function(){
	html = "It's not done yet ;__;";
	at.player.skin.show_overlay("Lazy Programmers", html);
}

this.main = function(){
	//menu = [{"menu_icon": "/images/troll/list_youtube.png", "title": "le title", "description": "le_description", "main_onclick": "alert(\"test1\");", "action_icon": "/images/troll/action_delete.png", "action_onclick": "alert(\"test2\");"}];
	//alert(at.player.skin.menu_html(menu));
	
    at.player.output_ytplayer = at.player.skin.output_ytplayer;
    var skin_root_url = var_this_root_url + "/skins/troll/";
    
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
    var this_parent = $('body')[0];

    
    this_parent.innerHTML = html + this_parent.innerHTML;
    
    //Set height.
    //var cmd_height = document.getElementById("atp-cmd").offsetHeight;

    

    

    

    //Load default field values.
    //anontune.player.load_field_values();
    
    //Load music navbar.
    //anontune.player.load_music_tab_navbar();
    
    //Show music tab.
    //at.player.skin.show_tab('atp-main-middle-music', 1, 0);
	
	at.player.skin.load_playlists();
	
	//Show date and keep it updated.
	at.player.skin.date();
	setInterval("at.player.skin.date()", 60000);
	setInterval("at.player.skin.result_change()", 2000);
	
	at.player.skin.hide_overlay();
	$(".menu_list").scrollTop(0);
}

};
/*
*    end skin
*    end skin
***/


//Load skin code into at.player.skin.namespace.
//Very important. This is evaled. Under at.player scope.
at.player.skin = skin;
