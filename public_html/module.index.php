<?php
if($_ANONTUNE !== true) { die(); }

$template['page'] = new Templater();
$template['page']->Load("page.index");
$template['page']->Localize($locale->strings);
$sPageContents = $template['page']->Render();

$sSelectedHome = "class=\"current\"";
$sPageTitle = $locale->strings['title-home'];
?>
