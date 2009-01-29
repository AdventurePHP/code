CREATE TABLE IF NOT EXISTS `ent_application` (
  `ApplicationID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`ApplicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ent_user` (
  `UserID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `FirstName` VARCHAR(100) character set utf8 NOT NULL default '',
  `LastName` VARCHAR(100) character set utf8 NOT NULL default '',
  `StreetName` VARCHAR(100) character set utf8 NOT NULL default '',
  `StreetNumber` VARCHAR(100) character set utf8 NOT NULL default '',
  `ZIPCode` VARCHAR(100) character set utf8 NOT NULL default '',
  `City` VARCHAR(100) character set utf8 NOT NULL default '',
  `EMail` VARCHAR(100) character set utf8 NOT NULL default '',
  `Phone` VARCHAR(100) character set utf8 NOT NULL default '',
  `Mobile` VARCHAR(100) character set utf8 NOT NULL default '',
  `Username` VARCHAR(100) character set utf8 NOT NULL default '',
  `Password` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ent_group` (
  `GroupID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ent_role` (
  `RoleID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`RoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ent_permission` (
  `PermissionID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `Name` VARCHAR(100) character set utf8 NOT NULL default '',
  `Value` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`PermissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ent_permissionset` (
  `PermissionSetID` INT(5) NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`PermissionSetID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmp_application2group` (
  `CMPID` INT(5) NOT NULL auto_increment,
  `ApplicationID` INT(5) NOT NULL default '0',
  `GroupID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`CMPID`),
  KEY `JOININDEX` (`ApplicationID`,`GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ass_group2user` (
  `ASSID` INT(5) NOT NULL auto_increment,
  `GroupID` INT(5) NOT NULL default '0',
  `UserID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`ASSID`),
  KEY `JOININDEX` (`GroupID`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ass_role2user` (
  `ASSID` INT(5) NOT NULL auto_increment,
  `RoleID` INT(5) NOT NULL default '0',
  `UserID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`ASSID`),
  KEY `JOININDEX` (`RoleID`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ass_role2permissionset` (
  `ASSID` INT(5) NOT NULL auto_increment,
  `RoleID` INT(5) NOT NULL default '0',
  `PermissionSetID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`ASSID`),
  KEY `JOININDEX` (`RoleID`,`PermissionSetID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmp_application2user` (
  `CMPID` INT(5) NOT NULL auto_increment,
  `ApplicationID` INT(5) NOT NULL default '0',
  `UserID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`CMPID`),
  KEY `JOININDEX` (`ApplicationID`,`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmp_application2role` (
  `CMPID` INT(5) NOT NULL auto_increment,
  `ApplicationID` INT(5) NOT NULL default '0',
  `RoleID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`CMPID`),
  KEY `JOININDEX` (`ApplicationID`,`RoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmp_application2permissionset` (
  `CMPID` INT(5) NOT NULL auto_increment,
  `ApplicationID` INT(5) NOT NULL default '0',
  `PermissionSetID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`CMPID`),
  KEY `JOININDEX` (`ApplicationID`,`PermissionSetID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ass_permissionset2permission` (
  `ASSID` INT(5) NOT NULL auto_increment,
  `PermissionSetID` INT(5) NOT NULL default '0',
  `PermissionID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`ASSID`),
  KEY `JOININDEX` (`PermissionSetID`,`PermissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmp_application2permission` (
  `CMPID` INT(5) NOT NULL auto_increment,
  `ApplicationID` INT(5) NOT NULL default '0',
  `PermissionID` INT(5) NOT NULL default '0',
  PRIMARY KEY  (`CMPID`),
  KEY `JOININDEX` (`ApplicationID`,`PermissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



INSERT INTO `ent_application` (`ApplicationID`, `DisplayName`, `CreationTimestamp`) VALUES (1, 'Default application', NOW());