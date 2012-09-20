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
	<div class="menu_list_row">
		<div class="menu_list_icon"><a onclick="alert('test');"><img src="/images/troll/list_youtube.png"></a></div>
		<div class="menu_list_text">
		<a onclick="alert('test');">
			<div class="menu_list_title">Hire Me</div>
			<div class="menu_list_description">Dr Syntax</div>
		</a>
		</div>
		<div class="menu_list_action"><a onclick="alert('test2');"><img src="/images/troll/action_delete.png"></a></div>
	</div>
	<div class="menu_list_row">
		<div class="menu_list_icon"><img src="/images/troll/list_unknown.png"></div>
		<div class="menu_list_text">
			<div class="menu_list_title">Hire Me</div>
			<div class="menu_list_description">Dr Syntax</div>
		</div>
		<div class="menu_list_action"><img src="/images/troll/action_delete.png"></div>
	</div>
	<div class='menu_list_row'><div class='menu_list_icon'><a onclick='alert("test1");'><img src='/images/troll/list_youtube.png'></a></div><div class='menu_list_text'><a onclick='alert("test1");'><div class='menu_list_title'>le title</div><div class='menu_list_description'>le_description</div></a></div><div class='menu_list_action'><a onclick='alert("test2");'><img src='/images/troll/action_delete.png'></a></div></div>
</div>

<div class="logo_bar">
	<div class="table_row_wrapper">
		<div class="search_bar">
			<div class="website_title">
				<a href="#">Anontune</a>
			</div>
			<input type="text" class="search_input text_input" spellcheck="false" autocomplete="off" placeholder="Search for music as title - artist . . ."/>
			<button class="search_button add_button" onclick="at.player.skin.search();">Search</button>
		</div>
		<div class="add_track">
			<input type="text" class="add_track_input text_input" spellcheck="false" autocomplete="off" placeholder="Title" id="add_track_input" name="add_track_input"/><input type="text" class="add_artist_input text_input" spellcheck="false" autocomplete="off" placeholder="Artist" id="add_artist_input" name="add_artist_input"/>
			<button class="add_track_button add_button" onclick="at.player.skin.add_track();">Add</button>
		</div>
	</div>
</div>

<div class="bottom_bar">
<div class="table_row_wrapper">
<div class="playlist_add">
<input type="text" class="add_playlist_input text_input" placeholder="New Playlist" spellcheck="false" autocomplete="off" id="add_playlist_input" name="add_playlist_input"/>
<button class="add_playlist_button add_button" onclick="at.player.skin.add_playlist();">Add</button>
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
Drain the veins in my head<br>
Clean out the reds in my eyes to get by security lines<br>
Dear x-ray machine<br>
Pretend you don't know me so well<br>
I wont tell if you lied<br>
Cry, cause the droughts been brought up<br>
Drinkin' cause you're lookin so good in your starbucks cup<br>
I complain for the company that I keep<br>
The windows for sleeping rearrange<br>
And I'm nobody<br>
Well who's laughing now
<p>
I'm leaving your town again<br>
And I'm over the ground that you've been spinning<br>
And I'm up in the air said baby hell yeah<br>
Well honey I can see your house from here<br>
If the plane goes down, damn<br>
I'll remember where the love was found<br>
If the plane goes down, damn
<p>
Damn, I should be so lucky<br>
Even only 24 hours under your touch<br>
You know I need you so much<br>
I cannot wait to call you<br>
And tell you that I landed somewhere<br>
And hand you a square of the airport<br>
And walk you through the maze of the map<br>
That I'm gazing at<br>
Gracefully unnamed and feeling guilty for the luck<br>
And the look that you gave me<br>
You make me somebody<br>
Ain't nobody knows me<br>
Not even me can see it, yet I bet I'm
<p>
I'm leaving your town again love<br>
But I'm over the ground that you've been spinning<br>
And I'm up in the air, said baby hell yeah<br>
Oh honey I can see your house from here<br>
If the plane goes down, damn<br>
I'll remember where the love was found<br>
If the plane goes down, damn<br>
<p>
You keep me high minded<br>
You get me high<br>
<p>
Flax seeds, well they tear me open<br>
And supposedly you can crawl right through me<br>
Taste these teeth please<br>
And undress me from these sweaters better hurry<br>
Cause I'm keeping upward bound now<br>
Oh maybe I'll build my house on your cloud<br>
Here I'm tumbling for you<br>
Stumbling through the work that I have to do<br>
Don't mean to harm you
<p>
By leaving your town again love<br>
But I'm over the ground that you've been spinning<br>
But I'm up in the air, said baby hell yeah<br>
Oh honey I can see your house from here<br>
If the plane goes down, damn<br>
I'll remember where the love was found<br>
If the plane goes down, damn<br>
I'll remember where the love was found<br>
If the plane goes down, damn<br>
Well I'll remember where the love was found<br>
If the plane goes down, damn
<p>
Who do you<br>
Think you are, are, are, are<br>
To keep me so oh cold, cold<br>
You keep me high minded<br>
You keep me high minded
<p>
You get me high minded<br>
You get me high
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
	<!--<a href="#">Add</a>|--><a href="#" onclick="at.player.skin.auto_play(null);">Retry</a><br>
	<!--
	<a href="#">Reply</a>
	<a href="#">Share</a>
	<a href="#">Lyrics</a>
	<a href="#">Lock</a>
	-->
	
	</div>
</div>

<!-- jplayer stuff:
		<div id="jp_container_1" class="jp-video jp-video-360p">
			<div class="jp-type-single">
				<div id="jquery_jplayer_1" class="jp-jplayer"></div>
				<div class="jp-gui">
					<div class="jp-video-play">
						<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
					</div>
					<div class="jp-interface">
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>
						<div class="jp-controls-holder">
							<ul class="jp-controls">
								<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
								<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
								<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
							</ul>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
							<ul class="jp-toggles">
								<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
								<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
								<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
								<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
							</ul>
						</div>
						<div class="jp-title">
							<ul>
								<li>Big Buck Bunny Trailer</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
-->
</div>
</div>


<!--<div class="right_art"><img src="/images/troll/test_art.jpg"></div>-->



<div class="result_bg">&nbsp;</div>
