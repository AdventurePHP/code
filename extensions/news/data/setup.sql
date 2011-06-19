CREATE TABLE IF NOT EXISTS `ent_news` (
  `NewsID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `AppKey` varchar(100) NOT NULL DEFAULT '',
  `Author` varchar(100) NOT NULL DEFAULT '',
  `Title` varchar(100) NOT NULL DEFAULT '',
  `Text` longtext,
  `CreationTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ModificationTimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`NewsID`),
  KEY `AppKeyINDEX` (`AppKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;