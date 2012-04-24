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
 
class Playlist extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM playlist WHERE `id` = '%d'";
	public $verify_query = "SELECT * FROM playlist WHERE `id` = '%d'";
	public $table_name = "playlist";
	public $id_field = "id";
	
	public $prototype = array(
		'string' => array(
			'Name'			=> "name"
		),
		'numeric' => array(
			'UserId'		=> "user_id",
			'ParentId'		=> "parent_id"
		),
		'user' => array(
			'User'			=> "user_id"
		),
		'playlist' => array(
			'Parent'		=> "parent_id"
		)
	);
}
?>
