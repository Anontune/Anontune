-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 21, 2013 at 11:06 AM
-- Server version: 5.1.66
-- PHP Version: 5.3.6-13ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `anontune4`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation`
--

CREATE TABLE IF NOT EXISTS `activation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `artist_id` bigint(20) unsigned DEFAULT '1',
  `title` varchar(70) DEFAULT 'Unknown',
  `is_valid` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11696 ;

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE IF NOT EXISTS `artist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT 'Unknown',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10983 ;

-- --------------------------------------------------------

--
-- Table structure for table `auth_token`
--

CREATE TABLE IF NOT EXISTS `auth_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(50) NOT NULL DEFAULT '0',
  `timestamp` bigint(8) NOT NULL DEFAULT '0',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `user_agent` varchar(8192) NOT NULL DEFAULT '0',
  `uses` int(11) NOT NULL DEFAULT '1',
  `token` varchar(40) NOT NULL DEFAULT '0',
  `referer` varchar(3000) NOT NULL DEFAULT '0',
  `auth_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `cookie_name` varchar(50) NOT NULL DEFAULT '0',
  `cookie_value` varchar(1024) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

--
-- Table structure for table `bad_word`
--

CREATE TABLE IF NOT EXISTS `bad_word` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `word` longtext,
  `replacement` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE IF NOT EXISTS `banned` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(70) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(0, 'privacy', 'Privacy Policy', 'Sorry, the Privacy Policy simply isn''t there yet. It will be added somewhere in the next few days. It will basically come down to "we won''t give out your details" anyway.', 'about', 'Privacy Policy'),
(0, 'papers', 'AnonTune Papers', '<p>To read more about the concept behind AnonTune and our ideas for the future, read the initial whitepaper.</p>\r\n<p><a class="button blue small pill" href="/papers/Anontune_White_Paper.pdf">Download (PDF)</a></p>', 'about', 'Papers'),
(0, 'ipod', 'Import your iPod music database', '<div class="col_7">\n<p>\nThe software discussed on this page will allow you to "upload" your Apple iPod''s music to this website. The music itself will be played through third parties so none of your music is actually uploaded -- just information about your music. This means you won''t be breaking copyright laws.\n</p>\n<p>\n<strong>This will only work with Apple iPods and your iPod will need to be mounted as a disk drive. By default iPod Classic, iPod Nano, and iPod shuffle can be mounted as a disk drive but for the iPhone, iPod Touch, and iPad you will need to go to special means. Make sure the songs you want to add are in a playlist.</strong>\n</p>\n<p>\nDownload: \n<a class="button blue small pill" href="http://www.anontune.com/import/ipod/windows_program.zip">Windows</a>\n<a class="button blue small pill" href="http://www.anontune.com/import/ipod/linux_program.zip">Linux (and others)</a>\n</p>\n</div>\n<div class="col_5">\n<iframe width="370" height="248" src="http://www.youtube.com/embed/sa-05F4X8nU" frameborder="0" allowfullscreen></iframe>\n</div>\n<div class="col_12">\n<p>\nThe software is open source. All it does is upload your iPod''s music database to Anontune where it is processed. The Windows binary has been generated from the Python code. Linux users will have to use the Python code directly, no hand holding, as it goes.\n</p>\n<h3>Can''t find the database?</h3>\n<p>Your iPod needs to be mounted as a disk drive for the software to find the database. If it is then you will have to specify the path to the database manually in the software. When you open your iPod as a disk drive you should go find something resembling iPod_Control/iTunes/itunesDB (or itunesCDB.)</p>\n<p>Make sure you DON''T rename the database. The name is highly significant to this software because it indicates which type of database it is. Also, if you know where the file is you can use the upload form bellow to import it directly.</p>\n<p>\n<form enctype="multipart/form-data" action="http://www.anontune.com/api.php?c=upload_ipod_db&username=&auth_username=&auth_password=" method="POST">\n<input name="uploaded_file" type="file" />\n<button class="small" type="submit">Upload!</button>\n</form>\n</p>\n<h3>iPod Touch / iPhone / iPad</h3>\n<p><strong>You will need to mount your iPod as a disk drive if it is not.</strong> There are programs out there that can do this (but I couldn''t find any, lololol). I found something which allowed me to read the device''s file system. For Windows I used <a href="http://www.macroplant.com/iexplorer/">iExplorer</a> to extract the database to the Desktop and then specify it''s location manually in the software.</p>\n<p>\nThat''s basically it. You just need a way to extract the itunesDB or itunesCDB (use the one which is largest in size) to a place where the software can read it. Don''t expect any help with this, this is a prototype as it is. Things may be easier in the future but for now you need to be a power user to fix any problems.\n</p>\n<h3>Still doesn''t work?</h3>\n<p>Download "<a href="http://www.anontune.com/import/ipod/msvcr71.dll">MSVCR71.dll</a>", "<a href="http://www.anontune.com/import/ipod/w9xpopen.exe">w9xpopen.exe</a>", "<a href="http://www.anontune.com/import/ipod/python25.dll">python25.dll</a>" to the same directory as the program. Also try installing <a href="http://www.microsoft.com/download/en/details.aspx?displaylang=en&id=3387">Microsoft Visual C++ 2005 Redistributable Package (x86)</a>. Try again? If it didn''t work try using the Python code directly. Install <a href="http://www.python.org/getit/releases/2.5/">Python 2.5</a> press [win] + r, type "cmd" without the quotes, cd to the directory with the <a href="http://www.anontune.com/import/ipod/linux_program.zip">Python code/Linux version</a> and call it through Python.</p>\n<p>Oh yeah, If you specify the path manually make sure the database file is called either itunesDB or itunesCDB -- these names denote what type the database is! I can''t even open them without that (admittedly it''s trivial to add a code check for this but I''m lazy.)</p>\n<p><strong>Remember, this is a prototype. Report bugs to the person who sent you here.</strong></p>\n</div>\n<div class="clear"></div>', 'tools', 'iPod importer'),
(0, 'criticism', 'Our response to (media) criticism', '<p>\r\nWe have recently issued a response to criticism from both media and users. This response addresses questions like "Is the Java applet safe?", "What''s so special about AnonTune?", and the financial aspect of AnonTune.\r\n</p>\r\n<p>\r\n<a class="button blue small pill" href="http://pastebin.com/fuM5TYDr">Read our response on Pastebin</a>\r\n</p>', 'about', 'Our response to (media) criticism');

-- --------------------------------------------------------

--
-- Table structure for table `email_list`
--

CREATE TABLE IF NOT EXISTS `email_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=126 ;

-- --------------------------------------------------------

--
-- Table structure for table `friend`
--

CREATE TABLE IF NOT EXISTS `friend` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `friend_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_address_history`
--

CREATE TABLE IF NOT EXISTS `ip_address_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `message` text,
  `type` varchar(70) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `is_sticky` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `music`
--

CREATE TABLE IF NOT EXISTS `music` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(70) DEFAULT 'Unknown',
  `artist_id` bigint(20) unsigned DEFAULT '1',
  `album_id` bigint(20) unsigned DEFAULT '1',
  `total_rating` bigint(20) unsigned DEFAULT '0',
  `total_rater` bigint(20) unsigned DEFAULT '0',
  `total_like` bigint(20) unsigned DEFAULT '0',
  `total_dislike` bigint(20) unsigned DEFAULT '0',
  `play_count` bigint(20) unsigned DEFAULT '0',
  `skip_count` bigint(20) unsigned DEFAULT '0',
  `is_valid` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50287 ;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `cmd` varchar(200) DEFAULT '0',
  `parent_id` bigint(20) unsigned DEFAULT '0',
  `name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10442 ;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(70) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `amount` int(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2958 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) DEFAULT NULL,
  `type` varchar(70) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE IF NOT EXISTS `track` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(70) DEFAULT 'Unknown',
  `artist_id` bigint(20) unsigned DEFAULT '1',
  `genre` varchar(50) DEFAULT 'Unknown',
  `album_id` bigint(20) unsigned DEFAULT '1',
  `year` char(4) DEFAULT '1984',
  `time_played` int(10) unsigned DEFAULT '0',
  `time_added` int(10) unsigned DEFAULT '0',
  `playlist_id` bigint(20) unsigned DEFAULT '0',
  `play_count` int(10) unsigned DEFAULT '0',
  `skip_count` int(1) unsigned DEFAULT '0',
  `time_skipped` int(10) unsigned DEFAULT '0',
  `user_id` bigint(20) unsigned DEFAULT '0',
  `music_id` bigint(20) unsigned DEFAULT '0',
  `service_provider` int(1) unsigned DEFAULT '1',
  `service_resource` varchar(80) DEFAULT NULL,
  `number` int(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=78994 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `hash` char(64) DEFAULT NULL,
  `group` int(1) unsigned DEFAULT '1',
  `email` varchar(50) DEFAULT 'email@example.com',
  `is_activated` int(1) unsigned DEFAULT '0',
  `age` int(1) unsigned DEFAULT '1',
  `country` int(10) unsigned DEFAULT '1',
  `join_time` int(10) unsigned DEFAULT '0',
  `login_time` int(10) unsigned DEFAULT '0',
  `avatar` varchar(50) DEFAULT NULL,
  `signature` text,
  `youtube_vid` varchar(20) DEFAULT NULL,
  `time_played` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19724 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
