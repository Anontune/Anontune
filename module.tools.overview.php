<?php
if($_ANONTUNE !== true) { die(); }

$sItems = "";

if($result = mysql_query_cached("SELECT * FROM cms_pages WHERE `Category` = 'tools'"))
{
	foreach($result->data as $row)
	{
		$sPage = new Page($row);
		
		$template['page'] = new Templater();
		$template['page']->Load("page.tools.overview.item");
		$template['page']->Localize($locale->strings);

		$template['page']->Compile(array(
			'title'		=> $sPage->sTitle,
			'urlname'	=> $sPage->sUrlName
		));

		$sItems .= $template['page']->Render();
	}
}

$template['page'] = new Templater();
$template['page']->Load("page.tools.overview");
$template['page']->Localize($locale->strings);

$template['page']->Compile(array(
	'items'		=> $sItems
));

$sPageContents = $template['page']->Render();
?>
