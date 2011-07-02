CREATE TABLE IF NOT EXISTS `ent_application` (
  `ApplicationID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_user` (
  `UserID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `DisplayName` varchar(100) NOT NULL DEFAULT '',
  `FirstName` varchar(100) NOT NULL DEFAULT '',
  `LastName` varchar(100) NOT NULL DEFAULT '',
  `StreetName` varchar(100) NOT NULL DEFAULT '',
  `StreetNumber` varchar(100) NOT NULL DEFAULT '',
  `ZIPCode` varchar(100) NOT NULL DEFAULT '',
  `City` varchar(100) NOT NULL DEFAULT '',
  `EMail` varchar(100) NOT NULL DEFAULT '',
  `Phone` varchar(100) NOT NULL DEFAULT '',
  `Mobile` varchar(100) NOT NULL DEFAULT '',
  `Username` varchar(100) NOT NULL DEFAULT '',
  `Password` varchar(100) NOT NULL DEFAULT '',
  `DynamicSalt` varchar(50) NOT NULL DEFAULT '',
  `CreationTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_group` (
  `GroupID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_role` (
  `RoleID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
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

CREATE TABLE IF NOT EXISTS `ent_permissionset` (
  `PermissionSetID` INT(5) UNSIGNED NOT NULL auto_increment,
  `DisplayName` VARCHAR(100) character set utf8 NOT NULL default '',
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`PermissionSetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ent_appproxy` (
  `AppProxyID` INT(5) UNSIGNED NOT NULL auto_increment,
  `AppObjectId` BIGINT(5) UNSIGNED,
  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`AppProxyID`),
  KEY `AppObjectIdINDEX` (`AppObjectId`)
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

CREATE TABLE IF NOT EXISTS `ass_role2permissionset` (
  `Source_RoleID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionSetID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_RoleID`, `Target_PermissionSetID`),
  KEY `REVERSEJOIN` (`Target_PermissionSetID`, `Source_RoleID`)
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

CREATE TABLE IF NOT EXISTS `cmp_application2permissionset` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionSetID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_PermissionSetID`),
  KEY `REVERSEJOIN` (`Target_PermissionSetID`, `Source_ApplicationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ass_permissionset2permission` (
  `Source_PermissionSetID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_PermissionSetID`, `Target_PermissionID`),
  KEY `REVERSEJOIN` (`Target_PermissionID`, `Source_PermissionSetID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cmp_application2permission` (
  `Source_ApplicationID` INT(5) UNSIGNED NOT NULL default '0',
  `Target_PermissionID` INT(5) UNSIGNED NOT NULL default '0',
  KEY `JOIN` (`Source_ApplicationID`, `Target_PermissionID`),
  KEY `REVERSEJOIN` (`Target_PermissionID`, `Source_ApplicationID`)
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

INSERT INTO `ent_application` (`ApplicationID`, `DisplayName`, `CreationTimestamp`) VALUES (1, 'Default application', NOW());