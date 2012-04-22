<?php
if($_ANONTUNE !== true) { die(); }

$template['page'] = new Templater();
$template['page']->Load("page.contribute");
$template['page']->Localize($locale->strings);
$sPageContents = $template['page']->Render();

$sSelectedContribute = "class=\"current\"";
$sPageTitle = $locale->strings['title-contribute'];
?>
