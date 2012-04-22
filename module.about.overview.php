<?php
if($_ANONTUNE !== true) { die(); }

$sItems = "";

if($result = mysql_query_cached("SELECT * FROM cms_pages WHERE `Category` = 'about'"))
{
	foreach($result->data as $row)
	{
		$sPage = new Page($row);
		
		$template['page'] = new Templater();
		$template['page']->Load("page.about.overview.item");
		$template['page']->Localize($locale->strings);

		$template['page']->Compile(array(
			'title'		=> $sPage->sTitle,
			'urlname'	=> $sPage->sUrlName
		));

		$sItems .= $template['page']->Render();
	}
}

$template['page'] = new Templater();
$template['page']->Load("page.about.overview");
$template['page']->Localize($locale->strings);

$template['page']->Compile(array(
	'items'		=> $sItems
));

$sPageTitle = $sPageTitle = $locale->strings['title-about'];
$sPageContents = $template['page']->Render();
?>
