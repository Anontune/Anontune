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
 *  (c) 2011 Anontune developers
 * 
 */

if($_ANONTUNE !== true) { die(); }

$display_form = true;
$sError = "";

$sFormUsername = "";

if(!empty($_POST['submit']))
{	
	$sFormUsername = htmlentities($_POST['username']);
	
	$sUsername = mysql_real_escape_string($_POST['username']);
	
	$result = mysql_query_cached("SELECT * FROM user WHERE `username` = '{$sUsername}'", 1);
	
	if(empty($_POST['username']) || empty($_POST['password']))
	{
		// not all fields filled in
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['login-error-missing-title'], $locale->strings['login-error-missing-message']);
		$sError .= $err->Render();
	}
	elseif(!$result)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['login-error-user-title'], $locale->strings['login-error-user-message']);
		$sError .= $err->Render();
	}
	else
	{
		$sUser = new User($result);
		
		if(!$sUser->VerifyPassword($_POST['password']))
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['login-error-password-title'], $locale->strings['login-error-password-message']);
			$sError .= $err->Render();
		}
		else
		{
			$_SESSION["group"] = $sUser->sGroupId;
			login($_POST['username'], $_POST['password']);
				
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['login-success-title'], $locale->strings['login-success-message']);
			$sError .= $err->Render();
			
			$display_form = false;
		}
	}
}

if($display_form === true)
{
	$template['page'] = new Templater();
	$template['page']->Load("page.login");
	$template['page']->Compile(array(
		'error'				=> $sError,
		'value-username'	=> $sFormUsername
	));
	$template['page']->Localize($locale->strings);
	$sPageContents .= $template['page']->Render();
}
else
{
	$sUsername = htmlspecialchars($_POST['username']);
	
	$template['page'] = new Templater();
	$template['page']->Load("page.login.success");
	$template['page']->Compile(array(
		'username'		=> $sUsername,
		'error'			=> $sError
	));
	$template['page']->Localize($locale->strings);
	$sPageContents .= $template['page']->Render();
}


$sSelectedLogin = "class=\"current\"";
$sPageTitle = $locale->strings['title-login'];
