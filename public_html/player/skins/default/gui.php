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
 
require_once(dirname(__file__) . DIRECTORY_SEPARATOR . "../../../global.php");
//$img_path = $this_root_url . "/../../images/";
$img_path = "/player/images/";
//echo $this_root_url;
//echo "test";
/*
Those scpecial playlists should be virtual.
Their file structure will be implemented in the player
*/
?>
<div id="atp" style="margin-top: 35px;">
    <div id="atp-ytapi"></div>
    <table id="atp-cmd">
        <tr>
            <td id="atp-cmd-left">
            <center>
            <!--
 <a href="#" onclick="anontune.player.toggle_playlist_type();"><img src="<?php echo $img_path; ?>playlist_type.png"></a> 
<a href="#" onclick="anontune.player.add_playlist();" class="atp-main-nav"><img src="<?php echo $img_path; ?>add.png"></a>
<a href="#" onclick="anontune.player.delete_playlist();" class="atp-main-nav"><img src="<?php echo $img_path; ?>delete.png"></a>
<a href="#" onclick="anontune.player.edit_playlist();" class="atp-main-nav"><img src="<?php echo $img_path; ?>edit.png"></a>

<a href="#" onclick="anontune.player.open_playlist();" class="atp-main-nav"><img src="<?php echo $img_path; ?>open.png"></a>
<a href="#" onclick="anontune.player.close_playlist();" class="atp-main-nav"><img src="<?php echo $img_path; ?>close.png"></a> -->
            </center>
            </td>
            <td id="atp-cmd-middle">
                <center><!--
<a href="#" onclick="at.player.skin.show_tab('atp-main-middle-music', 1, 0);" class="atp-main-nav"><img src="<?php echo $img_path; ?>music.png"></a>

<a href="#" onclick="at.player.skin.show_tab('atp-main-middle-information', 1, 0);" class="atp-main-nav"><img src="<?php echo $img_path; ?>world.png"></a>
<a href="#" onclick="at.player.skin.show_tab('atp-main-middle-edit', 1, 0);" class="atp-main-nav"><img src="<?php echo $img_path; ?>edit.png"></a> 
<a href="#" onclick="anontune.player.previous_track();" class="atp-main-nav"><img src="<?php echo $img_path; ?>previous_track.png"></a>
<a href="#" onclick="anontune.player.next_track();" class="atp-main-nav"><img src="<?php echo $img_path; ?>next_track.png"></a>-->
                </center>
            </td>
            <td id="atp-cmd-right">
            <center><!--
<a href="#" onclick="anontune.player.add_track();" class="atp-main-nav"><img src="<?php echo $img_path; ?>add.png"></a>
<a href="#" onclick="anontune.player.delete_track();" class="atp-main-nav"><img src="<?php echo $img_path; ?>delete.png"></a>
<a href="#" onclick="anontune.player.edit_track();" class="atp-main-nav"><img src="<?php echo $img_path; ?>edit.png"></a>
<a href="#" onclick="anontune.player.new_music_tab();" class="atp-main-nav"><img src="<?php echo $img_path; ?>tab.png"></a>

<a href="#" onclick="anontune.player.shuffle();" class="atp-main-nav"><img src="<?php echo $img_path; ?>shuffle.png"></a>
<a href="#" onclick="at.player.toggle_loop();" class="atp-main-nav"><img src="<?php echo $img_path; ?>loop.png"></a> -->
            </center>
            </td>
        </tr>
    </table>
    <table id="atp-main">
        <tr>
            <td id="atp-main-left">
                <div id="atp-main-left-title">
                    <form id="atp-main-left-title-form" onsubmit="return false;">
                        <center><table border="0" width="99%">
                            <tr>
                                <td width="18">
                                    <center><a href="#" onclick='at.player.add_pl({"loaded": true, "name": document.getElementById("atp-main-left-title-form")["container_name"].value, "parent_id": "0", "cmd": "0"}); at.player.skin.load_playlists();' style="margin-top: 9px;" title="Add new playlist."><img src="<?php echo $img_path; ?>add.png"></a></center>
                                </td>
                                <td>
                                <center>
                                    <input type="text" name="container_name">
                                </center>
                                </td>
                                <td width="18">
                                    <center><a href="#" onclick='if(at.pl_i == null) { alert("Select a playlist first."); return; } at.player.del_pl(at.pl_i); at.player.skin.load_playlists();' style="margin-top: 9px;"><img src="<?php echo $img_path; ?>delete.png" title="Delete active playlist."></a></center>
                                </td>
                            </tr>
                        </table></center>
                    </form>
                </div>
                <div id="atp-main-left-content"></div>
                <div id="atp-main-left-imgdetail"><img src="<?php echo $img_path; ?>photo.png" style="width: 100%; height: 100%;">
                </div>
            </td>
            <td id="atp-main-middle">
                <div id="atp-main-middle-title">
                    <center style="padding-top: 3px;">
<a href="#" onclick="at.player.skin.show_tab('atp-main-middle-music', 1, 0);" title="Show now playing."><img src="<?php echo $img_path; ?>music.png"></a>
<!-- at.player.skin.show_tab('atp-main-middle-information', 1, 0); -->
<a href="#" onclick="at.player.shuffle(at.pl_i); at.player.skin.load_playlist(at.pl_i);"><img src="<?php echo $img_path; ?>shuffle.png" alt="shuffle" title="Shuffle active playlist."></a>
<a href="#" onclick="at.player.toggle_loop();"><img src="<?php echo $img_path; ?>loop.png" alt="loop" title="Loop active song."></a>
<!--<img src="<?php echo $img_path; ?>emasis.png">
<img src="<?php echo $img_path; ?>lyrics.png">
<img src="<?php echo $img_path; ?>fullscreen.png">
<img src="<?php echo $img_path; ?>shell.png" alt="shell">
<img src="<?php echo $img_path; ?>log.png" alt="log">
<img src="<?php echo $img_path; ?>search.png">
<img src="<?php echo $img_path; ?>feedback.png">
<img src="<?php echo $img_path; ?>help.png">-->
                    </center>
                </div>
                <div id="atp-main-middle-tabs" class="atp-main-tabc">
                    <div id="atp-main-middle-music" class="atp-main-tab atp-music-tab" style="color: #000000;"><center><div id="atp-main-middle-music-tiles">
                    <center>
                   <div class="tile_table" style="width: 100%;"><div class="tile_tr"><center><div class="tile_td"><div style="width: 1px;">&nbsp;</div></div><div class="tile_td"><div style="width: 300px;" class="tile_box"><div style="height: 50px; float: left;"> </div><span style="font-size: 22px; text-align: left; float: left; margin-left: 10px; margin-top: 8px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; width: 209px;"><form style="margin: 0px; white-space: nowrap; padding: 0px; display: block;" onsubmit="at.me.me_search(); return false;" id="me_search"><input style="width: 200px; float: left; margin-top: 13px; white-space: nowrap; padding-top: 0px; padding-bottom: 0px; padding-left: 5px; padding-right: 5px; text-align: center; display: block; border: 1px solid gray; -moz-border-radius: 15px; border-radius: 15px; background: none repeat scroll 0% 0% rgb(125, 126, 125); color: white;" name="me_q"/></form></span><a onclick="at.me.me_search();" href="#"><img style="float: left; margin-top: 22px;" src="/search.png"/></a></div></div></center></div></div> 
                    </center>
                    </div></center>
<div style="display: table; #position: relative; overflow: hidden;  color: white;" id="atp-main-middle-music-content">
<center><div id="atp-main-middle-music-content-yt"></div></center>
                    </div>
                    <div id="atp-main-middle-information" class="atp-main-tab">Information</div>
                    <div id="atp-main-middle-edit" class="atp-main-tab">Edit</div>
                </div>
            </td>
            <td id="atp-main-right">
                <div id="atp-main-right-title">
                    <form id="atp-main-right-title-form" onsubmit="return false;">
                        <table>
                            <tr>
                                <td id="atp-main-right-title-sample">
                                    <table border="0">
                                        <tr>
                                            <td width="18">
                                                <center><a href="#" onclick='if(at.pl_i == null) { alert("Select a playlist first."); return; } at.player.add_track({"title": document.getElementById("atp-main-right-title-form")["track_title"].value, "artist_name": document.getElementById("atp-main-right-title-form")["track_artist"].value, "playlist_id": at.pls[at.pl_i]["id"]}, at.pl_i);  at.player.skin.load_playlist(at.pl_i);' style="margin-top: 8px;"><img src="<?php echo $img_path; ?>add.png" title="Add new song to active playlist."></a></center>
                                            </td>
                                            <td style="border: 0px;">
                                                <input type="text" name="track_title">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table border="0">
                                        <tr>
                                            <td>
                                                <input type="text" name="track_artist" style="float: right;">
                                            </td>
                                            <td width="18" style="border: 0px;">
                                                <center><a href="#" onclick='if(at.pl_i == null) { alert("Select a playlist first."); return; } if(at.track_i == null) { alert("Select a track first."); return; } at.player.del_track(at.track_i, at.pl_i);  at.player.skin.load_playlist(at.pl_i);' style="margin-top: 8px;"><img src="<?php echo $img_path; ?>delete.png" title="Delete active song from active playlist."></a></center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div id="atp-main-right-content"></div>
                <div id="atp-main-right-mudetail"><center><b>He is speed and I am balance.</b></center></div>
            </td>
        </tr><!--
        <tr>
            <td id="atp-main-left-plsearch"><center>
            <input type="text" value="Search . . ." ></center>
            </td>
            <td id="atp-main-middle-resbar">dfgdfgfdgfdg</td>
            <td id="atp-main-right-musearch"><center>
            <input type="text" value="Search . . ." ></center></td>
            
        </tr>-->
    </table>
    <!--
    <table id="atp-footer">
        <tr>
            <td id="atp-footer-left"><input type="text" value="Search" id="atp-footer-left-input"></td>
            <td id="atp-footer-middle">Middle</td>
            <td id="atp-footer-right"><input type="text" value="Search" id="atp-footer-right-input"></td>
        </tr>
    </table>
    -->
</div>
