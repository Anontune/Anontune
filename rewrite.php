<?php
$_ANONTUNE = true;
require("includes/base.php");

$sPageTitle = "";
$sPageContents = "";
$sErrorCode = 0;

$sSelectedHome = "";
$sSelectedDemo = "";
$sSelectedLogin = "";
$sSelectedRegister = "";
$sSelectedAbout = "";
$sSelectedTools = "";
$sSelectedContribute = "";
$sSelectedIrc = "";

$render_template = true;

if(empty($force_index))
{
	$router = new CPHPRouter();

	$router->routes = array(
		0 => array(
			'^/$'									=> "module.index.php",
			'^/demo/?$'								=> "module.demo.php",
			'^/login/?$'							=> "module.login.php",
			'^/logout/?$'							=> "module.logout.php",
			'^/register/?$'							=> "module.register.php",
			'^/about/?$'							=> "module.about.overview.php",
			'^/about/([^/]+)/?$'					=> "module.about.page.php",
			'^/tools/?$'							=> "module.tools.overview.php",
			'^/tools/([^/]+)/?$'					=> "module.tools.page.php",
			'^/user/([^/]+)/?$'						=> "wrapper.player.php",
			'^/contribute/?$'						=> "module.contribute.php",
			'^/irc/?$'								=> "module.irc.php",
			'^/(at-login-2)/?$'						=> "module.legacy.php",
			'^/(at-register)/?$'					=> "module.legacy.php",
			'^/(login_register\.php)$'				=> "module.legacy.php",
			'^/login\.php\?action=(logout)$'		=> "module.legacy.php",
			'^/09/04/2012/(import-ipod)/?$'			=> "module.legacy.php",
			'^/user\.php\?username=(.+)$'			=> "module.notfound.php"
		),
		1 => array(
			'^/(.+)$'								=> "module.notfound.php"
		)
	);

	$router->RouteRequest();
}
else
{
	include("module.index.php");
}

if($render_template === true)
{
	if($sErrorCode == 404)
	{
		// 404 error
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		
		$template['404'] = new Templater();
		$template['404']->Load("page.404");
		$template['404']->Localize($locale->strings);

		$sPageContents = $template['404']->Render();
		$sPageTitle = "404";
	}
	
	$sMenuAbout = "";
	$sMenuTools = "";
	
	if($result = mysql_query_cached("SELECT * FROM cms_pages WHERE `Category` = 'about'"))
	{
		foreach($result->data as $row)
		{
			$sPage = new Page($row);
			$sMenuAbout .= "<li><a href=\"/about/{$sPage->sUrlName}/\">{$sPage->sMenuTitle}</a></li>";
		}
	}
	
	if($result = mysql_query_cached("SELECT * FROM cms_pages WHERE `Category` = 'tools'"))
	{
		foreach($result->data as $row)
		{
			$sPage = new Page($row);
			$sMenuTools .= "<li><a href=\"/tools/{$sPage->sUrlName}/\">{$sPage->sMenuTitle}</a></li>";
		}
	}
	
	$template['main'] = new Templater();
	$template['main']->Load("page.main");
	$template['main']->Localize($locale->strings);
	$template['main']->Compile(array(
		'title'				=> $sPageTitle,
		'contents'			=> $sPageContents,
		'set-home'			=> $sSelectedHome,
		'set-demo'			=> $sSelectedDemo,
		'set-login'			=> $sSelectedLogin,
		'set-register'		=> $sSelectedRegister,
		'set-about'			=> $sSelectedAbout,
		'set-tools'			=> $sSelectedTools,
		'set-contribute'	=> $sSelectedContribute,
		'set-irc'			=> $sSelectedIrc,
		'menu-about'		=> $sMenuAbout,
		'menu-tools'		=> $sMenuTools
	));

	$template['main']->Output();
}
else
{
	// directly output whatever is in the buffer
	echo($sPageContents);
}


?>

