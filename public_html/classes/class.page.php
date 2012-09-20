<?php
class Page extends CPHPDatabaseRecordClass
{
	public $fill_query = "SELECT * FROM cms_pages WHERE `Id` = '%d'";
	public $verify_query = "SELECT * FROM cms_pages WHERE `Id` = '%d'";
	public $table_name = "cms_pages";

	public $prototype = array(
		'string' => array(
			'UrlName'		=> "UrlName",
			'Title'			=> "Title",
			'Category'		=> "Category",
			'MenuTitle'		=> "MenuTitle"
		),
		'none' => array(
			'Contents'		=> "Contents"
		)
	);
}
