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
artist:query
support google style queries
*/

?>


<!--<div class="overlay_view_watermark"><img src="/images/troll/nhk_cute.jpg"></div>-->


<div class="overlay_view">
</div>
<div class="overlay_view_container">
	<div class="overlay_view_close"><a href="#" onclick="at.player.skin.hide_overlay();"><img src="/images/troll/close.png"></a></div>
	<div class="overlay_view_title"></div>
	<div class="overlay"></div>
</div>

<div class="menu_list">
</div>

<div class="logo_bar">
	<div class="table_row_wrapper">
		<div class="search_bar">
			<div class="website_title">
				<a href="#">Anontune</a>
			</div>
			<form onsubmit="at.player.skin.search(); return false;">
			<input type="text" class="search_input text_input" spellcheck="false" autocomplete="off" placeholder="Search for music . . ." onkeypress="at.player.skin.try_schedule_menu_filter();"/>
			<button class="search_button add_button" onclick="at.player.skin.search();">Search</button>
			</form>
		</div>
		<div class="add_track">
			<form onsubmit="return false;">
			<input type="text" class="add_artist_input text_input" spellcheck="false" autocomplete="off" placeholder="Artist" id="add_artist_input" name="add_artist_input"/>
			<input type="text" class="add_track_input text_input" spellcheck="false" autocomplete="off" placeholder="Title" id="add_track_input" name="add_track_input"/>
			<button class="add_track_button add_button" onclick="at.player.skin.add_track();">Add</button>
			</form>
		</div>
	</div>
</div>

<div class="bottom_bar">
<div class="table_row_wrapper">
<div class="playlist_add">
<form onsubmit="return false;">
<input type="text" class="add_playlist_input text_input" placeholder="New Playlist" spellcheck="false" autocomplete="off" id="add_playlist_input" name="add_playlist_input"/>
<button class="add_playlist_button add_button" onclick="at.player.skin.add_playlist();">Add</button>
</form>
</div>
<div class="player_controls" id="player_controls">
<span class="loop_control"><a href="#" onclick="at.player.skin.enable_loop();" title="Loop"><img src="/images/troll/loop.png"></a></span>
<a href="#" onclick="at.player.skin.shuffle();" title="Shuffle"><img src="/images/troll/shuffle.png"></a>
<a href="#" onclick="at.player.skin.lazy_programmers();" title="Volume"><img src="/images/troll/volume.png"></a>
<a href="#" onclick="at.player.stop_track(); at.player.skin.disable_play();" title="Stop"><img src="/images/troll/stop.png"></a>
<a href="#" onclick="at.player.prev_track();" title="Previous Track"><img src="/images/troll/previous.png"></a>
<span class="play_control"><a href="#" onclick="at.player.skin.enable_play();"><img src="/images/troll/play.png"></a></span>
<a href="#" onclick="at.player.next_track();" title="Next Track"><img src="/images/troll/next.png"></a>
<span class="love_control"><a href="#" onclick="at.player.skin.lazy_programmers();" title="Love"><img src="/images/troll/love.png"></a></span>
<span class="like_control"><a href="#" onclick="at.player.skin.lazy_programmers();" title="Like"><img src="/images/troll/like.png"></a></span>
<span class="dislike_control"><a href="#" onclick="at.player.skin.lazy_programmers();" title="Dislike"><img src="/images/troll/dislike.png"></a></span>
<span class="money_control"><a href="#" onclick="at.player.skin.lazy_programmers();" title="Buy"><img src="/images/troll/money.png"></a></span>
</div>
</div>
</div>




<div class="search_view">
	<!--
	<div class="search_stats">
		Around x results found in y seconds.
	</div>
	<div class="search_stats_row">
		<div class="menu_list_action"><img src="/images/troll/arrow_right.png"></div>
	</div>
	-->
	<center>
	<div class="results_list">

	</div>
	</center>
	<!--<div class="play_view_back">(<a href="#" onclick="at.player.skin.show_main_view('.play_view')">Back</a>)</div>-->
</div>


<div class="lyrics_view">

<div class="indent">

<a href="#">Close lyrics</a>
<p>
<div class="lyrics">
</div>
<p>
<a href="#">Close lyrics</a>
</p>
</div>
</div>



<!--<div class="watermark"><img src="/images/troll/troll_face.png"></div>-->
<div class="watermark">
	<div class="watermark_top"></div>
	<img src="/images/troll/watermark.png">
	</div>
<!--<div class="left_watermark"><img src="/images/troll/left_watermark.png"></div>-->
<div class="play_view">


<div class="song_title_heading"></div>
<center>
<div class="play_main">
	<!--
	<div class="arrow_left">
		<a href="#"><img src="/images/troll/arrow_left.png"></a>
	</div>
	-->	
	<!--
	<div class="play_flag">
	<a href="#"><img src="/images/troll/flag.png"></a>
	</div>
	-->

	
	
	
<!-- 480 x 260 -->
<div class="play_video">
<div id="jplayer"></div>
<div id="ytplayer"></div>
</div>


<!--
<div class="play_msg">
<div class="play_msg_avatar">
	<img src="uploads/avatar/Mozart.jpg">
</div>
	
	
<div class="play_msg_author"><a href="#">Anonymous</a></div>
<div class="play_msg_tabno"></div>
<div class="play_msg_content">jjj
</div>
<!--<div class="play_msg_date">x</div>

</div>
-->




</div>
</center>


<div class="play_view_bottom_bar">
	<div class="search_result_no">(<a href="#" onclick="at.player.skin.show_search_view();">0 Results</a>)</div>
	<div class="play_view_nav_bottom">
	<!--<a href="#">Add</a>|--><a href="#" onclick="at.player.skin.auto_play(null);">Wrong song?</a><br>
	<!--
	<a href="#">Reply</a>
	<a href="#">Share</a>
	<a href="#">Lyrics</a>
	<a href="#">Lock</a>
	-->
	
	</div>
</div>


</div>
</div>


<!--<div class="right_art"><img src="/images/troll/test_art.jpg"></div>-->



<div class="result_bg">&nbsp;</div>
