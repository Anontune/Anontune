<?php
$query = "-- phpMyAdmin SQL Dump
-- version 3.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 21, 2012 at 08:29 AM
-- Server version: 5.5.21
-- PHP Version: 5.3.10


--
-- Database: `anontune`
--

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE IF NOT EXISTS `cms_pages` (
  `Id` bigint(20) NOT NULL,
  `UrlName` varchar(30) NOT NULL,
  `Title` varchar(300) NOT NULL,
  `Contents` mediumtext NOT NULL,
  `Category` varchar(20) NOT NULL,
  `MenuTitle` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`Id`, `UrlName`, `Title`, `Contents`, `Category`, `MenuTitle`) VALUES
(0, 'privacy', 'Privacy Policy', 'Sorry, the Privacy Policy simply isn''t there yet. It will be added somewhere in the next few days. It will basically come down to \"we won''t give out your details\" anyway.', 'about', 'Privacy Policy'),
(0, 'papers', 'AnonTune Papers', '<p>To read more about the concept behind AnonTune and our ideas for the future, read the initial whitepaper.</p>\r\n<p><a class=\"button blue small pill\" href=\"/papers/Anontune_White_Paper.pdf\">Download (PDF)</a></p>', 'about', 'Papers'),
(0, 'ipod', 'Import your iPod music database', '<div class=\"col_7\">\r\n<p>\r\nThe software discussed on this page will allow you to \"upload\" your Apple iPod''s music to this website. The music itself will be played through third parties so none of your music is actually uploaded -- just information about your music. This means you won''t be breaking copyright laws.\r\n</p>\r\n<p>\r\n<strong>This will only work with Apple iPods and your iPod will need to be mounted as a disk drive. By default iPod Classic, iPod Nano, and iPod shuffle can be mounted as a disk drive but for the iPhone, iPod Touch, and iPad you will need to go to special means. Make sure the songs you want to add are in a playlist.</strong>\r\n</p>\r\n<p>\r\nDownload: \r\n<a class=\"button blue small pill\" href=\"http://www.anontune.com/import/ipod/windows_program.zip\">Windows</a>\r\n<a class=\"button blue small pill\" href=\"http://www.anontune.com/import/ipod/linux_program.zip\">Linux (and others)</a>\r\n</p>\r\n</div>\r\n<div class=\"col_5\">\r\n<iframe width=\"370\" height=\"248\" src=\"http://www.youtube.com/embed/sa-05F4X8nU\" frameborder=\"0\" allowfullscreen></iframe>\r\n</div>\r\n<div class=\"col_12\">\r\n<p>\r\nThe software is open source. All it does is upload your iPod''s music database to Anontune where it is processed. The Windows binary has been generated from the Python code. Linux users will have to use the Python code directly, no hand holding, as it goes.\r\n</p>\r\n<h3>Can''t find the database?</h3>\r\n<p>Your iPod needs to be mounted as a disk drive for the software to find the database. If it is then you will have to specify the path to the database manually in the software. When you open your iPod as a disk drive you should go find something resembling iPod_Control/iTunes/itunesDB (or itunesCDB.)</p>\r\n<p>Make sure you DON''T rename the database. The name is highly significant to this software because it indicates which type of database it is. Also, if you know where the file is you can use the upload form bellow to import it directly.</p>\r\n<p>\r\n<form enctype=\"multipart/form-data\" action=\"http://www.anontune.com/api.php?c=upload_ipod_db&username=&auth_username=&auth_password=\" method=\"POST\">\r\n<input name=\"uploaded_file\" type=\"file\" />\r\n<button class=\"small\" type=\"submit\">Upload!</button>\r\n</form>\r\n</p>\r\n<h3>iPod Touch / iPhone / iPad</h3>\r\n<p><strong>You will need to mount your iPod as a disk drive if it is not.</strong> There are programs out there that can do this (but I couldn''t find any, lololol). I found something which allowed me to read the device''s file system. For Windows I used <a href=\"http://www.macroplant.com/iexplorer/\">iExplorer</a> to extract the database to the Desktop and then specify it''s location manually in the software.</p>\r\n<p>\r\nThat''s basically it. You just need a way to extract the itunesDB or itunesCDB (use the one which is largest in size) to a place where the software can read it. Don''t expect any help with this, this is a prototype as it is. Things may be easier in the future but for now you need to be a power user to fix any problems.\r\n</p>\r\n<h3>Still doesn''t work?</h3>\r\n<p>Download \"<a href=\"http://www.anontune.com/import/ipod/msvcr71.dll\">MSVCR71.dll</a>\", \"<a href=\"http://www.anontune.com/import/ipod/w9xpopen.exe\">w9xpopen.exe</a>\", \"<a href=\"http://www.anontune.com/import/ipod/python25.dll\">python25.dll</a>\" to the same directory as the program. Also try installing <a href=\"http://www.microsoft.com/download/en/details.aspx?displaylang=en&id=3387\">Microsoft Visual C++ 2005 Redistributable Package (x86)</a>. Try again? If it didn''t work try using the Python code directly. Install <a href=\"http://www.python.org/getit/releases/2.5/\">Python 2.5</a> press [win] + r, type \"cmd\" without the quotes, cd to the directory with the <a href=\"http://www.anontune.com/import/ipod/linux_program.zip\">Python code/Linux version</a> and call it through Python.</p>\r\n<p>Oh yeah, If you specify the path manually make sure the database file is called either itunesDB or itunesCDB -- these names denote what type the database is! I can''t even open them without that (admittedly it''s trivial to add a code check for this but I''m lazy.)</p>\r\n<p><strong>Remember, this is a prototype. Report bugs to the person who sent you here.</strong></p>\r\n</div>\r\n<div class=\"clear\"></div>', 'tools', 'iPod importer');
";

mysql_connect("localhost", "anontune2", "8NRehW5t6pRwLxuV");
mysql_select_db("anontune2");
mysql_query($query);

var_dump(mysql_error());
?>
