<?php
if($_ANONTUNE !== true) { die(); }

$sPageId = mysql_real_escape_string($router->uParameters[1]);

$result = mysql_query_cached("SELECT * FROM cms_pages WHERE `UrlName` = '{$sPageId}' AND `Category` = 'about'");

if($result)
{
	$sPage = new Page($result);
	
	$template['page'] = new Templater();
	$template['page']->Load("page.about.page");
	$template['page']->Localize($locale->strings);

	$template['page']->Compile(array(
		'title'		=> $sPage->sTitle,
		'contents'	=> $sPage->uContents  // We can do this here, because the database content is trusted.
	));

	$sPageContents = $template['page']->Render();

	$sSelectedAbout = "class=\"current\"";
	$sPageTitle = $sPage->sTitle;
}
else
{
	$sErrorCode = 404;
}
?>
