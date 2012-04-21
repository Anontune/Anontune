<?php
class Project extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM projects WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM projects WHERE `Id` = '%d'";
	public $table_name = "projects";
	
	public $render_template = "";
	
	public $prototype = array(
		'string' => array(
			'Title'				=> "Name",
			'Description'		=> "Description"
		),
		'numeric' => array(
			'OwnerId'			=> "OwnerId",
			'Status'			=> "Status"
		),
		'timestamp' => array(
			'CreationDate'		=> "CreationDate",
			'ModificationDate'	=> "ModificationDate"
		),
		'user' => array(
			'Owner'				=> "OwnerId"
		)
	);
	
	public $prototype_render = array(
		'id'				=> "Id",
		'name'				=> "Title",
		'description'		=> "Description",
		'creation-date'		=> "CreationDate",
		'modification-date'	=> "ModificationDate"
	);
}
?>
