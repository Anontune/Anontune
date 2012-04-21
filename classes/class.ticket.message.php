<?php
class TicketMessage extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM ticket_messages WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM ticket_messages WHERE `Id` = '%d'";
	public $table_name = "ticket_messages";
	
	public $prototype = array(
		'string' => array(
			'Message'		=> "Body"
		),
		'numeric' => array(
			'TicketId'		=> "TicketId",
			'UserId'		=> "UserId",
			'StateChange'	=> "StateChange"
		),
		'ticket' => array(
			'Ticket'		=> "TicketId"
		),
		'user' => array(
			'User'			=> "UserId"
		)
	);
}
?>
