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
  `Source_GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_GuestbookID`, `Target_AttributeID`),
  KEY `REVERSEJOIN` (`Target_AttributeID`, `Source_GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_entry2langdepvalues` (
  `Source_EntryID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_EntryID`, `Target_AttributeID`),
  KEY `REVERSEJOIN` (`Target_AttributeID`, `Source_EntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_guestbook2adminstrator` (
  `Source_GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_GuestbookID`, `Target_UserID`),
  KEY `REVERSEJOIN` (`Target_UserID`, `Source_GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_editor2entry` (
  `Source_UserID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_EntryID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_UserID`, `Target_EntryID`),
  KEY `REVERSEJOIN` (`Target_EntryID`, `Source_UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_guestbook2entry` (
  `Source_GuestbookID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_EntryID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_GuestbookID`, `Target_EntryID`),
  KEY `REVERSEJOIN` (`Target_EntryID`, `Source_GuestbookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_attribute2language` (
  `Source_AttributeID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_LanguageID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_AttributeID`, `Target_LanguageID`),
  KEY `REVERSEJOIN` (`Target_LanguageID`, `Source_AttributeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;