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
		$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Missing captcha", "You did not enter a captcha. Please try again.");
		$sError .= $err->Render();
	}
	else
	{
		$resp = recaptcha_check_answer($recaptcha_privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if(!$resp->is_valid)
		{
			// invalid captcha
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Invalid captcha", "You did not enter the correct captcha. Please try again.");
			$sError .= $err->Render();
		}
		elseif(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['verify']))
		{
			// not all fields filled in
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Required fields missing", "One or more fields were not filled in. All fields are required in this form.");
			$sError .= $err->Render();
		}
		elseif(strlen($_POST['username']) > 30)
		{
			// username too long
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Username too long", "The username you entered is too long. It can not be longer than 30 characters.");
			$sError .= $err->Render();
		}
		elseif(strlen($_POST['password']) < 8)
		{
			// password too short
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Password too short", "Your password is too short. It must be at least 8 characters long.");
			$sError .= $err->Render();
		}
		elseif($_POST['password'] != $_POST['verify'])
		{
			// password doesn't match
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Passwords don't match", "The passwords you entered don't match. Please try again.");
			$sError .= $err->Render();
		}
		elseif(!(is_numeric($_POST['username'][0]) || ctype_alpha($_POST['username'][0])))
		{
			// username must begin with number or letter
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Invalid username", "Your username must begin with a letter or a number.");
			$sError .= $err->Render();
		}
		elseif(!preg_match('/[0-9a-zA-Z-_\[\]{}\\|`^]+$/', $_POST['username']))
		{
			// username contains invalid characters
			$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Invalid username", "The username you entered contains invalid characters. Please
			only use characters a-z, A-Z, 0-9, and the special characters -_[]{}\|`^");
			$sError .= $err->Render();
		}
		else
		{
			$sUsername = mysql_real_escape_string($_POST['username']);
			$result = mysql_query_cached("SELECT * FROM user WHERE `username` = '{$sUsername}'", 1);
			
			if($result)
			{
				// user already exists
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_ERROR, "Username already taken", "The username you entered is already taken, and is not
				available for registration. Please pick a different username.");
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
				
				$err = new CPHPErrorHandler(CPHP_ERRORHANDLER_TYPE_SUCCESS, "Successfully registered!", "Your account was successfully created, and you
				are now logged in.");
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
$sPageTitle = "Register";
?>
