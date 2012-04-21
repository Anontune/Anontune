<?php
class TeamAssignment extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM team_assignments WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM team_assignments WHERE `Id` = '%d'";
	public $table_name = "team_assignments";
	
	public $prototype = array(
		'numeric' => array(
			'TeamId'	=> "TeamId",
			'ProjectId'	=> "ProjectId",
			'Level'		=> "Level"
		),
		'team' => array(
			'Team'		=> "TeamId"
		),
		'project' => array(
			'Project'	=> "ProjectId"
		)
	);
}
?>
