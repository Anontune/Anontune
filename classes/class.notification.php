<?php
abstract class Notification extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM notifications WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM notifications WHERE `Id` = '%d'";
	public $table_name = "notifications";
	
	public $prototype = array(
		'string' => array(
			'Description'	=> "Description"
		),
		'numeric' => array(
			'Type'			=> "Type",
			'ItemId'		=> "ItemId"
		)
	);
}
?>
