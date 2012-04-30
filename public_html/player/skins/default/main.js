/***
*    begin skin
*    begin skin
*/
skin = new function(){
    
this.main_tab = 0;
this.music_tab = 0;
this.music_tab_index = 0;
this.ytplayer_width = 100;
this.ytplayer_height = 100;
//State information about old cells which were highlighted.
//{"id": x, "class": x};
this.highlight =
{
    "old_playlist_cell": null, "old_track_cell_a": null, "old_track_cell_b": null
};

this.load_field_values = function(){
    /*
    var pair = [
        {"form_id": "atp-main-left-title-form",
        "input_name": "container_name"},
        {"form_id": "atp-main-right-title-form",
"input_name": "track_title"},
        {"form_id": "atp-main-right-title-form",
"input_name": "track_artist"}
        ];
        */
        
   if(at.pl_i != null) { document.getElementById("atp-main-left-title-form")["container_name"].value = at.pls[at.pl_i]["name"];
   }
   else 
   {
document.getElementById("atp-main-left-title-form")["container_name"].value = "New Playlist";
   }
   
    if(at.pl_i != null && at.track_i != null) { document.getElementById("atp-main-right-title-form")["track_title"].value = at.pls[at.pl_i]["tracks"][at.track_i]["title"];
    }
    else {
document.getElementById("atp-main-right-title-form")["track_title"].value = "Song Title";
}
        if(at.pl_i != null && at.track_i != null) { document.getElementById("atp-main-right-title-form")["track_artist"].value = at.pls[at.pl_i]["tracks"][at.track_i]["artist_name"];
        }
        else {
    document.getElementById("atp-main-right-title-form")["track_artist"].value = "Song Artist";
    }
        /*
    for(var i = 0; i < pair.length; i++){
        el = document.getElementById(pair[i]["form_id"]);
        el[pair[i]["input_name"]].value = anontune.state["field_values"][pair[i]["input_name"]];
    }   
    */
};
    
this.output_ytplayer = function(vid){
    //Delete old song.
    at.me.reset_music_container();
    
    //Set id.
    var id = "atp-main-middle-music-content-yt";

    //Clear old HTML.
    //var container = document.getElementById(id);
    //at.clear_element(id);
    
    //Clear old API search call container.
    at.clear_element("atp-ytapi");
    
    //Create flash player object.
    var params = {allowScriptAccess: "always", allowFullScreen: "true"};
    var atts = {"id": id};
    var url = "http://www.youtube.com/e/" + encodeURIComponent(vid) + "?enablejsapi=1&fs=1&playerapiid=" + encodeURIComponent(id);
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
    
    //Optionally show tab0.
    /*
    if(id == "atp-main-middle-music-container-tab0"){
        anontune.player.show_tab("atp-main-middle-music-container-tab0", 0, 0);
    }
    */
    
    //Reset ytplayer_container.
    /*
    anontune.state["active"]["ytplayer_container"] = "atp-main-middle-music-container-tab0";
    */
    
}

this.load_playlist = function(index){ 
    var id = at.pls[index]["id"];
    //Todo: error checking.
    //alert(at.pls[index]["tracks"].length);
    at.player.load_pl({"id": id, "title": "1", "artist_name": "1"});
    //alert(at.pls[index]["tracks"].length);
    //alert(at.pls[);
    
    //at.search_filter();
    
    //alert(anontune.state["playlists"][3]["tracks"][0]);
    //alert(anontune.state["playlists"][3]["tracks"][0]["title"]);
    
    //clear old_playlist_cell
    //anontune.state["active"]["old_playlist_cell"] = null;
    //Clear old_track_cell_a]
    at.player.skin.highlight["old_track_cell_a"] = null;
    at.player.skin.highlight["old_track_cell_b"] = null;
    //anontune.state["active"]["old_track_cell_a"] = null;
    //Clear old_track_cell_b
    //anontune.state["active"]["old_track_cell_b"] = null;
    
    //anontune.state["field_values"]["container_name"] = anontune.state["playlists"][index]["name"];
    //anontune.player.load_field_values();
    
    //Change active parent playlist.
    //anontune.state["active"]["parent_playlist"] = index;
    
    //Clear active track.
    if(at.pl_i != index){
        at.track_i = null;
    }
    
    //Change active container.
    var container = at.pl_i = index;
    
    

    
    //Set youtube container id.
    at.player.skin.ytplayer_container = "atp-main-middle-music-container-tab0";
    
    //Todo: Load from API.
    var helper = function(){
        var buf = "";
        var cell_class = "atp-row-dark";
        var class_list = {"atp-row-dark": "atp-row-light", "atp-row-light": "atp-row-dark"};
        
        var left_cell_width = document.getElementById("atp-main-right-title-sample").offsetWidth;
        if(navigator.userAgent.indexOf("Firefox") != -1){
            left_cell_width = left_cell_width - 11;
        }
        if(navigator.userAgent.indexOf("Opera") != -1){
            left_cell_width = left_cell_width - 10;
        }
        var change_background;
        var row_id;
        var cell_id;
        //alert(at.pls[index]["tracks"].length);
        for(var i = 0; i < at.pls[index]["tracks"].length; i++)
        { 
            if(at.pls[index]["tracks"][i] != null)
            {
                if(at.pls[index]["tracks"][i]["filter"]) continue;
                row_id = "atp-trackr" + i;
                cell_id = "atp-trackc" + i;
                change_background = "at.player.skin.highlight_main('" + cell_id + "', 2);";
                title_safe = at.htmlspecialchars(at.pls[index]["tracks"][i]["title"]);
                artist_name_safe = at.htmlspecialchars(at.pls[index]["tracks"][i]["artist_name"]);
                var temp = "";
temp = temp + "\r\n" + buf;
temp = temp + "\r\n<tr id=\"" + row_id;
temp = temp + "\">\r\n    <td onmouseover=\"cell_bg = document.getElementById('" + cell_id + "a').style.backgroundColor; document.getElementById('" + cell_id + "a').style.backgroundColor='white'; document.getElementById('" + cell_id + "b').style.backgroundColor='white';\" onmouseout=\"document.getElementById('" + cell_id + "a').style.backgroundColor=cell_bg; document.getElementById('" + cell_id + "b').style.backgroundColor=cell_bg;\" id=\"" + cell_id;
temp = temp + "a\" class=\"atp-main-cell " + cell_class;
temp = temp + "\" width=\"" + left_cell_width;
temp = temp + "px\">\r\n        <span class=\"atp-main-list\">\r\n            <a style='margin-left: 1px;' href=\"#\" onclick=\"at.enable_me_results = 1; if(at.me.play_track(" + i;
temp = temp + ", " + container;
temp = temp + ")) " + change_background;
temp = temp + "\">" + title_safe;
temp = temp + "</a>\r\n        </span>\r\n    </td>\r\n    <td onmouseover=\"cell_bg = document.getElementById('" + cell_id + "a').style.backgroundColor; document.getElementById('" + cell_id + "a').style.backgroundColor='white'; document.getElementById('" + cell_id + "b').style.backgroundColor='white';\" onmouseout=\"document.getElementById('" + cell_id + "a').style.backgroundColor=cell_bg; document.getElementById('" + cell_id + "b').style.backgroundColor=cell_bg;\" id=\"" + cell_id;
temp = temp + "b\" class=\"atp-main-cell " + cell_class;
temp = temp + "\">\r\n        <span class=\"atp-main-list\">\r\n            <a href=\"#\" onclick=\"if(at.me.play_track(" + i;
temp = temp + ", " + container;
temp = temp + ")) " + change_background;
temp = temp + "\">" + artist_name_safe;
temp = temp + "</a>\r\n        </span>\r\n    </td>\r\n</tr>\r\n                " + "";
                buf = temp;
                cell_class = class_list[cell_class];
            }
        }
        return buf;
    };
    
    var buf = "";
buf = buf + "\r\n<tr>\r\n<td style=\"border-top: 1px solid black;\" colspan=\"2\"></td>\r\n</tr>\r\n    " + "";
    if(navigator.userAgent.indexOf("MSIE") != -1){
        buf = "";
    }
    var rows = helper();
    if(rows == ""){
        buf = "";
    }
    
    var html = "";
html = html + "\r\n<table>\r\n" + rows;
html = html + "\r\n" + buf;
html = html + "\r\n</table>\r\n<p>\r\n    " + "";
    document.getElementById("atp-main-right-content").innerHTML = html;
    //Why ? anontune.player.load_playlists();
};

this.highlight_main = function(id, type){
    var helper = function(name){
        un = at.player.skin.highlight[name];
        //alert(un);
        //alert(name);
        
        if(un != null){
            el = document.getElementById(un["id"]);
            
            if(navigator.userAgent.indexOf("MSIE") != -1){
                el.setAttribute("className", un["class"]);
            }
            else
            {
                el.setAttribute("class", un["class"]);
            }
        }
        
        //Store info.
        el = document.getElementById(id);
        classi = el.className;
        idi = el.id;
        at.player.skin.highlight[name] = {"id": idi, "class": classi};
        //alert(at.player.skin.highlight[name]);
        
        //Highlight row.
        if(navigator.userAgent.indexOf("MSIE") != -1){
            el.setAttribute("className", "atp-main-cell-highlight");
        }
        else{
            el.setAttribute("class", "atp-main-cell-highlight");
        }  
    }
    
    var un;
    var info;
    var classi;
    var idi;
    var el;
    if(type == 1){ //Unhighlight playlist row.
        helper("old_playlist_cell");
    }
    else{
        var temp = id;
        id = id + "a";
        helper("old_track_cell_a");
        id = temp + "b";
        helper("old_track_cell_b");
    }    
}

this.load_playlists = function(){
    at.player.load_pls();
    at.player.skin.load_field_values();

    //Todo: Load from API.
    var format = function(p){
        var buf = "";
        var cell_class = "";
        var class_list = {"atp-row-dark": "atp-row-light", "atp-row-light": "atp-row-dark"};
        var cell_id = "";
        var row_id = "";
        var change_background = "";
        for(var i = 0; i < p.length; i++)
        {
            row_id = "atp-playlistr" + i;
            cell_id = "atp-playlistc" + i;
            change_background = "at.player.skin.highlight_main('" + cell_id + "', 1);";
            var temp = "";
temp = temp + "\r\n" + buf;
temp = temp + "\r\n<tr id=\"" + row_id;
temp = temp + "\">\r\n    <td onmouseover=\"cell_bg = this.style.backgroundColor; this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor=cell_bg;\" class=\"atp-main-cell " + cell_class;
temp = temp + "\" id=\"" + cell_id;
temp = temp + "\">\r\n        <span class=\"atp-main-list\">\r\n            <img src=\"" + var_image_path + p[i]["type"];
temp = temp + ".png\" style=\"float: left; position: relative; top: 2px; margin-right: 2px;\"><a href=\"#\" onclick=\"document.search.q.value = ''; at.search_filter();" + p[i]["onclick"];
temp = temp + " " + change_background;
temp = temp + "\">&nbsp;" + p[i]["name"];
temp = temp + "</a>\r\n        </span>\r\n    </td>\r\n</tr>\r\n            " + "";

            buf = temp;
            //cell_class = class_list[cell_class];
        }
        return buf;
    };
    
    //Build playlists array.
    //{"name": "x", "type": "x", "onclick": "x"}
    var playlists = [];
    var playlist = {};
    var parent_playlist = at.par_pl_i;
    var name = "/";
    var type = "branch";
    var onclick = "";
    var path = at.pl_cwd;
    
    //Root branch. 
    /*
    for(var i = 0; i < path.length; i++){
        onclick = onclick + " at.player.close_playlist(); at.player.skin.load_playlists();";
    }
    playlist = {"name": name, "type": type, "onclick": onclick};
    playlists.push(playlist);
    //Child branches.
    for(var i = 0; i < path.length; i++){
        name = path[i];
        onclick = "";
        for(var j = i; j < path.length - 1; j++){
            onclick = onclick + " at.player.close_playlist(); at.player.skin.load_playlists();";
        }
        playlist = {"name": name, "type": type, "onclick": onclick};
        playlists.push(playlist);
    }*/
    //Other playlists.
    for(var i = 0; i < at.pls.length; i++){
       if(at.pls[i] != null)
        {
            if(at.pls[i]["par_pl"] == parent_playlist){
                //alert(anontune.state["playlists"][i]["cmd"]);
                if(at.pls[i]["filter"]) continue;
                name = at.htmlspecialchars(at.pls[i]["name"]);
                type = at.pls[i]["cmd"] == "0" ? "static" : "dynamic";
                onclick = "";
onclick = onclick + "at.player.skin.load_playlist(" + i;
onclick = onclick + "); at.player.skin.load_playlists();" + "";
                playlist = {"name": name, "type": type, "onclick": onclick};
                playlists.push(playlist);
            }
        }
    }
    var temp = "";
temp = temp + "\r\n<tr>\r\n<td id=\"atp-main-left-content-sample_row\">&nbsp;</td>\r\n</tr>\r\n    " + "";
    
    if(navigator.userAgent.indexOf("MSIE") != -1){
        temp = "";
    }

    var html = "";
html = html + "\r\n<table>\r\n" + format(playlists);
html = html + "\r\n" + temp;
html = html + "\r\n</table>\r\n<p>\r\n    " + "";
    document.getElementById("atp-main-left-content").innerHTML = html;
}
    
this.show_tab = function(id, is_main, no){
    var tab = document.getElementById(id);
    var zindex;
    var active_tab;
    if(is_main){
        zindex = 2;
        
        active_tab = at.player.skin.main_tab;
        
        //Set active tab.
        at.player.skin.main_tab = id;
    }
    else{
        zindex = 3;
        
        active_tab = at.player.skin.music_tab;
        
        //Set active tab.
        at.player.skin.music_tab = id;
    }
    
    if(active_tab != null){
        active_tab = document.getElementById(active_tab);
        if(active_tab != null){
            //Hide old tab.
            active_tab.style.zIndex = zindex - 1;
        }
    }
    
    //Set active music_tab_index.
    at.player.skin.music_tab_index = no - 1;
    
    //Show tab.
    if(tab != null){
        tab.style.zIndex = zindex + 1;
    }
    
    /*
    if(!is_main){
        //Reload navbar.
        anontune.player.load_music_tab_navbar();
    }
    */
    
}

this.main = function(){
    at.player.output_ytplayer = at.player.skin.output_ytplayer;
    var skin_root_url = var_this_root_url + "/skins/default/";
    
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
    var container_height = this_parent.offsetHeight - 30;
    var container_width = this_parent.offsetWidth;
    
    this_parent.innerHTML = html + this_parent.innerHTML;
    
    //Set height.
    //var cmd_height = document.getElementById("atp-cmd").offsetHeight;
    var top_nav_height = document.getElementById("atp-main-middle-title").offsetHeight; //33
    var bottom_nav_height = 0;//document.getElementById("atp-main-middle-resbar").offsetHeight; //36
    var footer_height = 0;
    var main_height = container_height - 5 - 35;//- (top_nav_height + bottom_nav_height + 5);
    document.getElementById("atp-main").style.height = main_height;
    
    var title_height = document.getElementById("atp-main-left-title").offsetHeight;
    var search_height = 0;//document.getElementById("atp-main-left-plsearch").offsetHeight;
    var imgdetail_height = parseInt(0.120 * container_width) - 2;
    var mudetail_height = parseInt(0.50 * imgdetail_height);
    document.getElementById("atp-main-left").style.height = 1;
    //document.getElementById("atp-main-left-plsearch").style.height = search_height;
    document.getElementById("atp-main-left-imgdetail").style.height = imgdetail_height;
    //document
    document.getElementById("atp-main-left-content").style.height = main_height - (title_height + search_height + imgdetail_height);
    document.getElementById("atp-main-right-content").style.height = main_height - (title_height + search_height + mudetail_height);
    document.getElementById("atp-main-right-mudetail").style.height = mudetail_height;
    
    
    //var music_nav1_height = document.getElementById("atp-main-middle-music-nav1").offsetHeight;
    //var music_nav2_height = document.getElementById("atp-main-middle-music-nav2").offsetHeight;
    var music_height = main_height - (top_nav_height + bottom_nav_height + 5);
    //alert(top_nav_height); //alert(document.getElementById("atp-main-middle").offsetHeght);
    var middle_height = document.getElementById("atp-main-middle").offsetHeight; //alert(middle_height);
    var tab_height = middle_height - top_nav_height;
    var music_container_height = 1;
    //document.getElementById("atp-main-middle-music-container").style.height = music_container_height;
    //document.getElementById("atp-main-middle-music-container-tab0").style.height = music_container_height;
    //document.getElementById("atp-main-middle-music-container-tab0-about").style.height = music_container_height;
    document.getElementById("atp-main-middle-music").style.height = tab_height;
    document.getElementById("atp-main-middle-information").style.height = tab_height;
    document.getElementById("atp-main-middle-edit").style.height = tab_height;
    document.getElementById("atp-main-middle-tabs").style.height = tab_height;
    document.getElementById("atp-main-middle-music-content").style.height = tab_height - 100;
    skin.ytplayer_height = tab_height - 100;
    //at.player.skin.ytplayer_width = 
    

    //Set width.
    var left_width = parseInt(0.154 * container_width);
    var right_width = parseInt(0.31 * container_width);
    if(left_width + right_width < container_width)
    {
        var middle_width = container_width - (left_width + right_width);
        //atp-cmd
        var atp_cmd_left_width = 240 > left_width ? 240 : left_width;
        var atp_cmd_right_width = 250 > right_width ? 250 : right_width;
        document.getElementById("atp-cmd-left").style.width = atp_cmd_left_width;
        document.getElementById("atp-cmd-right").style.width = atp_cmd_right_width;

        //atp-main
        document.getElementById("atp-main-left").style.width = left_width;
        document.getElementById("atp-main-right").style.width = right_width;
        
        //atp-footer
        /*
        document.getElementById("atp-footer-left").style.width = left_width;
        document.getElementById("atp-footer-middle").style.width = middle_width;
        document.getElementById("atp-footer-right").style.width = right_width;
        */
    }
    var music_container_width = document.getElementById("atp-main-middle-music").offsetWidth;
    at.player.skin.ytplayer_width = music_container_width;
    at.player.skin.ytplayer_height = music_container_height;

    //Load default field values.
    //anontune.player.load_field_values();
    
    //Load music navbar.
    //anontune.player.load_music_tab_navbar();
    
    //Show music tab.
    at.player.skin.show_tab('atp-main-middle-music', 1, 0);
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
