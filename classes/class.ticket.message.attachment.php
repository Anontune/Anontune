<?php
class TicketMessageAttachment extends Attachment
{
	public $fill_query = "SELECT * FROM ticket_message_attachments WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM ticket_message_attachments WHERE `Id` = '%d'";
	public $table_name = "ticket_message_attachments";
	
	public function __construct($uDataSource, $uCommunityId = 0)
	{
		$prototype['numeric']['TicketMessageId'] = "TicketMessageId";
		$prototype['ticketmessage']['TicketMessage'] = "TicketMessageId";
		$this->ConstructDataset($uDataSource, $uCommunityId);
	}
}
?>
