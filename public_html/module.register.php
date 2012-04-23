<?php
if($_ANONTUNE !== true) { die(); }

$display_form = true;
$sError = "";

$sFormUsername = "";

if(!empty($_POST['submit']))
{	
	$sFormUsername = htmlentities($_POST['username']);
	
	if(empty($_POST["recaptcha_challenge_field"]) || empty($_POST["recaptcha_response_field"]))
	{
		// no captcha entered
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-nocaptcha-title'], $locale->strings['register-error-nocaptcha-message']);
		$sError .= $err->Render();
	}
	else
	{
		$resp = recaptcha_check_answer($recaptcha_privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if(!$resp->is_valid)
		{
			// invalid captcha
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-captcha-title'], $locale->strings['register-error-captcha-message']);
			$sError .= $err->Render();
		}
		elseif(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['verify']))
		{
			// not all fields filled in
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-missing-title'], $locale->strings['register-error-missing-message']);
			$sError .= $err->Render();
		}
		elseif(strlen($_POST['username']) > 30)
		{
			// username too long
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-toolong-title'], $locale->strings['register-error-toolong-message']);
			$sError .= $err->Render();
		}
		elseif(strlen($_POST['password']) < 8)
		{
			// password too short
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-tooshort-title'], $locale->strings['register-error-tooshort-message']);
			$sError .= $err->Render();
		}
		elseif($_POST['password'] != $_POST['verify'])
		{
			// password doesn't match
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-nomatch-title'], $locale->strings['register-error-nomatch-message']);
			$sError .= $err->Render();
		}
		elseif(!(is_numeric($_POST['username'][0]) || ctype_alpha($_POST['username'][0])))
		{
			// username must begin with number or letter
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-begin-title'], $locale->strings['register-error-begin-message']);
			$sError .= $err->Render();
		}
		elseif(!preg_match('/^[0-9a-zA-Z-_\[\]{}\\|`^]+$/', $_POST['username']))
		{
			// username contains invalid characters
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-username-title'], $locale->strings['register-error-username-message']);
			$sError .= $err->Render();
		}
		else
		{
			$sUsername = mysql_real_escape_string($_POST['username']);
			$result = mysql_query_cached("SELECT * FROM user WHERE `username` = '{$sUsername}'", 1);
			
			if($result)
			{
				// user already exists
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, $locale->strings['register-error-taken-title'], $locale->strings['register-error-taken-message']);
				$sError .= $err->Render();
			}
			else
			{
				// continue registering
				$sUser = new User(0);
				$sUser->uUsername = $_POST['username'];
				$sUser->uPassword = $_POST['password'];
				$sUser->uEmailAddress = "";
				$sUser->uAvatar = "";
				$sUser->uSignature = "";
				$sUser->uYoutubeVideo = "";
				$sUser->uGroupId = 1;
				$sUser->uAge = 1;
				$sUser->uCountryId = 1;
				$sUser->uTimesPlayed = 0;
				$sUser->uIsActivated = true;
				$sUser->GenerateHash();
				$sUser->InsertIntoDatabase();
				
				login($_POST['username'], $_POST['password']);
				
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, $locale->strings['register-success-title'], $locale->strings['register-success-message']);
				$sError .= $err->Render();
				
				$display_form = false;
			}
		}
	}
}

if($display_form === true)
{
	$template['page'] = new Templater();
	$template['page']->Load("page.register");
	$template['page']->Compile(array(
		'recaptcha'			=> recaptcha_get_html($recaptcha_publickey),
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
	$template['page']->Load("page.register.success");
	$template['page']->Compile(array(
		'username'		=> $sUsername,
		'error'			=> $sError
	));
	$template['page']->Localize($locale->strings);
	$sPageContents .= $template['page']->Render();
}


$sSelectedRegister = "class=\"current\"";
$sPageTitle = $locale->strings['title-register'];
?>
