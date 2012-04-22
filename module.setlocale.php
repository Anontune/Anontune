<?php
if($_ANONTUNE !== true) { die(); }

$render_template = false;

$new_locale = $router->uParameters[1];

switch($new_locale)
{
	case "dutch":
		$_SESSION['prefered_locale'] = "dutch";
		break;
	case "danish":
		$_SESSION['prefered_locale'] = "danish";
		break;
	default:
		$_SESSION['prefered_locale'] = "english";
		break;
}

header("Location: /");
die();
?>
