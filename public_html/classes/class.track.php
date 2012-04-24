<?php
class Track extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM music WHERE `id` = '%d'";
	public $verify_query = "SELECT * FROM music WHERE `id` = '%d'";
	public $table_name = "music";
	
	public $prototype = array(
		'string' => array(
			'Title'			=> "title"
		),
		'numeric' => array(
			'ArtistId'		=> "artist_id",
			'AlbumId'		=> "album_id",
			'TotalRating'	=> "total_rating",
			'TotalRaters'	=> "total_rater",
			'TotalLike'		=> "total_like",
			'TotalDislike'	=> "total_dislike",
			'PlayCount'		=> "play_count",
			'SkipCount'		=> "skip_count"
		),
		'boolean' => array(
			'IsValid'		=> "is_valid"
		),
		'artist' => array(
			'Artist'		=> "artist_id"
		),
		'album' => array(
			'Album'			=> "album_id"
		),
	);
}
?>
