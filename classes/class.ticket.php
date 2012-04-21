<?php
class Ticket extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM tickets WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM tickets WHERE `Id` = '%d'";
	public $table_name = "tickets";
	
	public $prototype = array(
		"string" => array(
			'Title'			=> "Name"
		),
		"numeric" => array(
			'CreatorId'		=> "CreatorId",
			'Status'		=> "Status"
			
		),
		"user" => array(
			'Creator'		=> "CreatorId"
		)
	);
}
?>
