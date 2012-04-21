<?php
abstract class Attachment extends CPHPDatabaseRecordClass
{
	// Ensure to have a prototype entry in derived classes that indicates
	// what database entry this attachment belongs to.
	
	public $prototype = array(
		'string' => array(
			'Filename'		=> "Filename",
			'Description'	=> "Description",
			'LocalPath'		=> "LocalPath",
			'TahoeUri'		=> "TahoeUri",
			'TahoePath'		=> "TahoePath"
		),
		'numeric' => array(
			'Filesize'		=> "Filesize",
			'DownloadCount'	=> "DownloadCount"
		),
		'boolean' => array(
			'IsTahoeFile'	=> "IsTahoe"
		)
	);
}
?>
