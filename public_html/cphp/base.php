<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if(!isset($root_dir))
{
	$root_dir = "./";
}

require("{$root_dir}cphp/include.constants.php");

require("{$root_dir}cphp/config.php");

require("{$root_dir}cphp/include.dependencies.php");
require("{$root_dir}cphp/include.exceptions.php");
require("{$root_dir}cphp/include.datetime.php");
require("{$root_dir}cphp/include.misc.php");

require("{$root_dir}cphp/include.memcache.php");
require("{$root_dir}cphp/include.mysql.php");
require("{$root_dir}cphp/include.session.php");

require("{$root_dir}cphp/class.templater.php");
require("{$root_dir}cphp/class.localizer.php");

$locale = new Localizer();
$locale->Load($cphp_locale_name);

setlocale(LC_ALL, $locale->locale);

require("class.base.php");
require("class.databaserecord.php");

foreach($cphp_components as $component)
{
	require("components/component.{$component}.php");
}
?>
