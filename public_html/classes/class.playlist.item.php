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
 */
 
class PlaylistItem extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM track WHERE `id` = '%d'";
	public $verify_query = "SELECT * FROM track WHERE `id` = '%d'";
	public $table_name = "track";
	public $id_field = "id";
	
	public $prototype = array(
		'string' => array(
			'Title'				=> "title",
			'Genre'				=> "genre",
			'ServiceResource'	=> "service_resource"
		),
		'numeric' => array(
			'ArtistId'			=> "artist_id",
			'AlbumId'			=> "album_id",
			'PlayCount'			=> "play_count",
			'SkipCount'			=> "skip_count",
			'TimesSkipped'		=> "time_skipped",
			'TimesPlayed'		=> "time_played",
			'TimesAdded'		=> "time_added",
			'Year'				=> "year",
			'PlaylistId'		=> "playlist_id",
			'TrackId'			=> "music_id",
			'ServiceId'			=> "service_id"
		),	
		'boolean' => array(	
			'IsValid'			=> "is_valid"
		),	
		'artist' => array(	
			'Artist'			=> "artist_id"
		),	
		'album' => array(	
			'Album'				=> "album_id"
		),	
		'playlist' => array(	
			'Playlist'			=> "playlist_id"
		),	
		'track' => array(	
			'Track'				=> "music_id"
		)
	);
	
	public $prototype_export = array(
		'Title',
		'Genre',
		'ServiceResource',
		'Album',
		'Artist',
		'Playlist',
		'Track',
		'PlayCount',
		'SkipCount',
		'TimesSkipped',
		'TimesPlayed',
		'TimesAdded',
		'Year',
		'ServiceId'
	);
}
?>
