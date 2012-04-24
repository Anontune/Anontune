<?php
class User extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM user WHERE `id` = '%d'";
	public $verify_query = "SELECT * FROM user WHERE `id` = '%d'";
	public $table_name = "user";
	
	public $uPassword = "";

	public $prototype = array(
		'string' => array(
			'Username'		=> "username",
			'EmailAddress'	=> "email",
			'Hash'			=> "hash",
			'Avatar'		=> "avatar",
			'Signature'		=> "signature",
			'YoutubeVideo'	=> "youtube_vid"
		),
		'numeric' => array(
			'GroupId'		=> "group",
			'Age'			=> "age",
			'CountryId'		=> "country",
			'TimesPlayed'	=> "time_played"
		),
		'boolean' => array(
			'IsActivated'	=> "is_activated"
		)
	);
	
	public function GenerateHash()
	{
		if(!empty($this->uPassword))
		{
			$this->uHash = $this->CreateHash($this->uPassword);
		}
		else
		{
			throw new MissingDataException("User object is missing a password.");
		}
	}
	
	public function CreateHash($input)
	{
		global $salt;
		return hash("sha256", md5(str_rot13($input . $salt)));
	}
	
	public function VerifyPassword($password)
	{
		if($this->CreateHash($password) == $this->sHash)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>

