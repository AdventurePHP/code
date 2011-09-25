CREATE TABLE IF NOT EXISTS `ent_application` (
  `ApplicationID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_user` (
  `UserID` INT(5) UNSIGNED NOT NULL auto_increment,
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
  `DynamicSalt` VARCHAR(50) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_group` (
  `GroupID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `Description` TEXT character set utf8 NOT NULL,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_role` (
  `RoleID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `Description` TEXT character set utf8 NOT NULL,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_permission` (
  `PermissionID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `Name` VARCHAR(100) character set utf8 NOT NULL default '',
  `Value` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`PermissionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_appproxy` (
  `AppProxyID` INT(5) UNSIGNED NOT NULL auto_increment,
  `AppObjectId` BIGINT(5) UNSIGNED,
  `ReadPermission` BOOLEAN NOT NULL DEFAULT 1,
  `WritePermission` BOOLEAN NOT NULL DEFAULT 1,
  `LinkPermission` BOOLEAN NOT NULL DEFAULT 1,
  `DeletePermission` BOOLEAN NOT NULL DEFAULT 1,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`AppProxyID`),
  KEY `AppObjectIdINDEX` (`AppObjectId`),
  KEY `ReadPermissionINDEX` (`ReadPermission`),
  KEY `WritePermissionINDEX` (`WritePermission`),
  KEY `LinkPermissionINDEX` (`LinkPermission`),
  KEY `DeletePermissionINDEX` (`DeletePermission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_appproxytype` (
  `AppProxyTypeID` INT(5) UNSIGNED NOT NULL auto_increment,
  `AppObjectName` VARCHAR(20) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`AppProxyTypeID`),
  UNIQUE KEY `AppObjectNameUNIQUE` (`AppObjectName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2group` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_GroupID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_GroupID`),
  KEY `REVERSEJOIN` (`Target_GroupID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_group2user` (
  `Source_GroupID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_GroupID`, `Target_UserID`),
  KEY `REVERSEJOIN` (`Target_UserID`, `Source_GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_role2user` (
  `Source_RoleID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_RoleID`, `Target_UserID`),
  KEY `REVERSEJOIN` (`Target_UserID`, `Source_RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_role2group` (
  `Source_RoleID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_GroupID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_RoleID`, `Target_GroupID`),
  KEY `REVERSEJOIN` (`Target_GroupID`, `Source_RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2user` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_UserID`),
  KEY `REVERSEJOIN` (`Target_UserID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2role` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_RoleID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_RoleID`),
  KEY `REVERSEJOIN` (`Target_RoleID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2permission` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_PermissionID`),
  KEY `REVERSEJOIN` (`Target_PermissionID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_role2permission` (
  `Source_RoleID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_RoleID`, `Target_PermissionID`),
  KEY `REVERSEJOIN` (`Target_PermissionID`, `Source_RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2appproxy` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_AppProxyID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_AppProxyID`),
  KEY `REVERSEJOIN` (`Target_AppProxyID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2appproxytype` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_AppProxyTypeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_AppProxyTypeID`),
  KEY `REVERSEJOIN` (`Target_AppProxyTypeID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_appproxy2user` (
  `Source_AppProxyID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_UserID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_AppProxyID`, `Target_UserID`),
  KEY `REVERSEJOIN` (`Target_UserID`, `Source_AppProxyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_appproxy2group` (
  `Source_AppProxyID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_GroupID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_AppProxyID`, `Target_GroupID`),
  KEY `REVERSEJOIN` (`Target_GroupID`, `Source_AppProxyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_appproxy2appproxytype` (
  `Source_AppProxyID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_AppProxyTypeID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_AppProxyID`, `Target_AppProxyTypeID`),
  KEY `REVERSEJOIN` (`Target_AppProxyTypeID`, `Source_AppProxyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ent_application` (`ApplicationID`, `DisplayName`) VALUES (1, 'Umgt');