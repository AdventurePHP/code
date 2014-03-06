CREATE TABLE IF NOT  EXISTS `article_comments` (
  `ArticleCommentID` int(5) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `EMail` varchar(100) NOT NULL DEFAULT '',
  `Comment` text NOT NULL,
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Time` time NOT NULL DEFAULT '00:00:00',
  `CategoryKey` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`ArticleCommentID`),
  KEY `Category` (`CategoryKey`)
) DEFAULT CHARSET=utf8;