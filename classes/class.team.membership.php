<?php
class TeamMembership extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM team_memberships WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM team_memberships WHERE `Id` = '%d'";
	public $table_name = "team_memberships";
	
	public $prototype = array(
		'numeric' => array(
			'TeamId'	=> "TeamId",
			'UserId'	=> "UserId",
			'Level'		=> "Level"
		),
		'team' => array(
			'Team'		=> "TeamId"
		),
		'user' => array(
			'User'		=> "UserId"
		)
	);
}
?>
