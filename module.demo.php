<?php
if($_ANONTUNE !== true) { die(); }

$template['page'] = new Templater();
$template['page']->Load("page.demo");
$template['page']->Localize($locale->strings);
$sPageContents = $template['page']->Render();

$sSelectedDemo = "class=\"current\"";
$sPageTitle = "Demo";
?>
