<?php
if($_ANONTUNE !== true) { die(); }

$template['page'] = new Templater();
$template['page']->Load("page.irc");
$template['page']->Localize($locale->strings);
$sPageContents = $template['page']->Render();

$sSelectedIrc = "class=\"current\"";
$sPageTitle = "IRC";
?>
