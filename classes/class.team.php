<?php
class Team extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM teams WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM teams WHERE `Id` = '%d'";
	public $table_name = "teams";
	
	public $prototype = array(
		'string' => array(
			'Title'			=> "Name",
			'Description'	=> "Description"
		)
	);
}
?>
