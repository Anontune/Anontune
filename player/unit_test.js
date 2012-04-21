    var u = function unit_test(){
        at.username = "test";
        at.auth_username = "test";
        at.auth_password = "t3stp4ssw0rd";
        var random = Math.floor(Math.random() * 100000);
        
        //Insert playlist.
        //No, call it as if it were an API call, and provide params required.
        /*
        var name = "I am the new pl" + random;
        at.player.add_pl({"name": name, "parent_id": "23", "cmd": "h0h0"});
        if(typeof(at.pls[0]) == "undefined"){
            alert("Add pl failed.");
            return;
        }
        */
        //alert(at.pls[0]["name"]);
        
        //Edit playlist.
        /*
        name = "edited" + random;
        at.player.edit_pl({"name": name, "parent_id": "30", "cmd": "x", "id": at.pls[0]["id"]}, 0);
        if(at.pls[0]["name"] != name || typeof(at.pls[0]["id"]) == "undefined"){
            alert("Edit pl failed");
            return;
        }
        */
        //alert(at.pls[0]["name"]);
        
        //Delete playlist.
        /*
        at.player.del_pl(0);
        if(typeof(at.pls[0]) != "undefined"){
            alert("Del pl failed");
            return;
        }
        alert("pl 0 successfully deleted");
        */
        
        /*
        for(p in at.pls[0]){
            alert(p);
        }
        return;
        */
        
        //Add track to pl 0.
        /*
        alert("tracks = " + at.pls[0]["tracks"]);
        var title = "new sdfsd track " + random;
        var artist_name = "new artist " + random;
        alert("pl id = " + at.pls[0]["id"]);
        at.player.add_track({"title": title, "artist_name": artist_name, "playlist_id": at.pls[0]["id"]}, 0);
        if(typeof(at.pls[0]["tracks"][0]["title"]) == "undefined" || typeof(at.pls[0]["tracks"][0]["artist_name"]) == "undefined" || typeof(at.pls[0]["tracks"][0]["id"]) == "undefined")
        {
            alert("Add track failed.");   
            return;
        }
        alert(at.pls[0]["tracks"][0]["title"]);
        alert(at.pls[0]["tracks"][0]["artist_name"]);
        alert("track id =   " + at.pls[0]["tracks"][0]["id"]);
        */
        
        //Edit track.
        /*
        title = "edited xx " + random;
        artist_name = "edited xx " + random;
        at.player.edit_track({"id": at.pls[0]["tracks"][0]["id"], "title": title, "artist_name": artist_name}, 0, 0);
        if(at.pls[0]["tracks"][0]["title"] != title || at.pls[0]["tracks"][0]["artist_name"] != artist_name){
            alert("Edit track failed");
            return;
        }
        alert(at.pls[0]["tracks"][0]["title"]);
        alert(at.pls[0]["tracks"][0]["artist_name"]);
        alert("track id =   " + at.pls[0]["tracks"][0]["id"]);
        */
        
        //Delete track.
        /*
        at.player.del_track(0, 0);
        if(typeof(at.pls[0]["tracks"][0]) != "undefined"){
            alert("Del track failed.");
            return;
        }
        */
        
        /*
        //Load playlists
        at.player.load_pls();
        for(var i = 0; i < at.pls.length; i++){
            //alert(at.pls[i]["name"]);
            1;
        }
        
        //Load playlist.
        at.player.load_pl({"id": "1", "title": "1"});
        for(var i = 0; i < at.pls[0]["tracks"].length; i++){
            alert(at.pls[0]["tracks"][i]["title"]);
        }
        this.shuffle = function(pl_i){
        */
        
        //this.new_pl = function(params){
        /*
        var pl = at.player.new_pl({"name": "mai new pl", "name2": "2"});
        at.pls.push(pl);
        pl = at.player.new_pl({"name": "new 2", "name2": "x"});
        at.pls.push(pl);
        at.player.set_search("at.pls", ["name", "name2"], "new 2");
        for(var i = 0; i < at.pls.length; i++){
            if(at.pls[i]["filter"] == false){
                alert(at.pls[i]["name"]);
            }
        }
        */
        
        /*
        var pl = at.player.new_pl({"name": "mai new pl"});
        var track = at.player.new_track({"title": "a", "artist_name": "a"});
        var trackb = at.player.new_track({"title": "b", "artist_name": "b"});
        var trackc = at.player.new_track({"title": "c", "artist_name": "c"});
        pl["tracks"].push(track); pl["tracks"].push(trackb);
        pl["tracks"].push(trackc);
        at.pls.push(pl);
        at.player.shuffle(0);
        for(var i = 0; i < at.pls[0]["tracks"].length; i++){
            alert(at.pls[0]["tracks"][i]["title"]);
        }
        */
        
    }
    u();
    //alert(at.username);
    //alert(at.auth_username);
    //alert(at.auth_password);
