--
--  Database initialisation script
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `CommentID` int(11) NOT NULL auto_increment,
  `Title` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Text` text collate utf8_general_ci NOT NULL,
  `Date` date NOT NULL default '0000-00-00',
  `Time` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`CommentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `comp_entry_comment`;
CREATE TABLE IF NOT EXISTS `comp_entry_comment` (
  `CompID` int(11) NOT NULL auto_increment,
  `EntryID` int(11) NOT NULL default '0',
  `CommentID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CompID`),
  KEY `EntryID` (`EntryID`),
  KEY `CommentID` (`CommentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `comp_guestbook_entry`;
CREATE TABLE IF NOT EXISTS `comp_guestbook_entry` (
  `CompID` int(11) NOT NULL auto_increment,
  `GuestbookID` int(11) NOT NULL default '0',
  `EntryID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CompID`),
  KEY `GuestbookID` (`GuestbookID`),
  KEY `EntryID` (`EntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `entry`;
CREATE TABLE IF NOT EXISTS `entry` (
  `EntryID` int(11) NOT NULL auto_increment,
  `Name` varchar(100) collate utf8_general_ci NOT NULL default '',
  `EMail` varchar(100) collate utf8_general_ci NOT NULL default '',
  `City` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Website` varchar(100) collate utf8_general_ci NOT NULL default '',
  `ICQ` varchar(100) collate utf8_general_ci NOT NULL default '',
  `MSN` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Skype` varchar(100) collate utf8_general_ci NOT NULL default '',
  `AIM` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Yahoo` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Text` text collate utf8_general_ci NOT NULL,
  `Date` date NOT NULL default '0000-00-00',
  `Time` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`EntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


DROP TABLE IF EXISTS `guestbook`;
CREATE TABLE IF NOT EXISTS `guestbook` (
  `GuestbookID` int(11) NOT NULL auto_increment,
  `Name` varchar(100) collate utf8_general_ci NOT NULL default '',
  `Description` text collate utf8_general_ci NOT NULL,
  `Admin_Username` varchar(50) collate utf8_general_ci NOT NULL default '',
  `Admin_Password` varchar(100) collate utf8_general_ci default NULL,
  PRIMARY KEY  (`GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;