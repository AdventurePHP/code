CREATE TABLE IF NOT EXISTS `ent_guestbook` (
  `GuestbookID` INT(5) UNSIGNED NOT NULL auto_increment,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_entry` (
  `EntryID` INT(5) UNSIGNED NOT NULL auto_increment,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`EntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_attribute` (
  `AttributeID` INT(5) UNSIGNED NOT NULL auto_increment,
  `Name` VARCHAR(100) character set utf8 NOT NULL default '',
  `Value` TEXT character set utf8 NOT NULL,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`AttributeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_language` (
  `LanguageID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `ISOCode` VARCHAR(2) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_user` (
  `UserID` INT(5) UNSIGNED NOT NULL auto_increment,
  `Name` VARCHAR(100) character set utf8 NOT NULL default '',
  `Email` VARCHAR(100) character set utf8 NOT NULL default '',
  `Website` VARCHAR(100) character set utf8 NOT NULL default '',
  `Username` VARCHAR(100) character set utf8 NOT NULL default '',
  `Password` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_guestbook2langdepvalues` (
  `GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`GuestbookID`, `AttributeID`),
  KEY `REVERSEJOIN` (`AttributeID`, `GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_entry2langdepvalues` (
  `EntryID` INT(5) UNSIGNED NOT NULL default '0',
  `AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`EntryID`, `AttributeID`),
  KEY `REVERSEJOIN` (`AttributeID`, `EntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_guestbook2adminstrator` (
  `GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`GuestbookID`, `UserID`),
  KEY `REVERSEJOIN` (`UserID`, `GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_editor2entry` (
  `UserID` INT(5) UNSIGNED NOT NULL default '0',
  `EntryID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`UserID`, `EntryID`),
  KEY `REVERSEJOIN` (`EntryID`, `UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_guestbook2entry` (
  `GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `EntryID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`GuestbookID`, `EntryID`),
  KEY `REVERSEJOIN` (`EntryID`, `GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_attribute2language` (
  `AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  `LanguageID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`AttributeID`, `LanguageID`),
  KEY `REVERSEJOIN` (`LanguageID`, `AttributeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;