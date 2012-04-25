<?php
/*
 *  This file is part of Anontune.
 *
 *  Anontune is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Anontune is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Anontune.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */
 
class User extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM user WHERE `id` = '%d'";
	public $verify_query = "SELECT * FROM user WHERE `id` = '%d'";
	public $table_name = "user";
	public $id_field = "id";
	
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
	
	public $prototype_export = array(
		'Username',
		'Avatar',
		'Age',
		'IsActivated'
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

