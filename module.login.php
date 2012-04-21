<?php
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
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Required fields missing", "One or more fields were not filled in. All fields are required in this form.");
		$sError .= $err->Render();
	}
	elseif(!$result)
	{
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "User does not exist", "Sorry, that username does not exist. If you want to register,
		then <a href=\"/register/\">click here</a>.");
		$sError .= $err->Render();
	}
	else
	{
		$sUser = new User($result);
		
		if(!$sUser->VerifyPassword($_POST['password']))
		{
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Password incorrect", "Sorry, that password was incorrect. Please try again.");
			$sError .= $err->Render();
		}
		else
		{
			login($_POST['username'], $_POST['password']);
				
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Successfully logged in!", "You have been successfully logged into your AnonTune account.");
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
$sPageTitle = "Log in";
?>
