CREATE TABLE search_articles (
  ArticleID int(11) NOT NULL auto_increment,
  Title varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  Language char(2) character set latin1 collate latin1_general_ci NOT NULL default '',
  Name varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  ModificationTimestamp timestamp NULL default NULL,
  PRIMARY KEY  (ArticleID),
  KEY Title (Title),
  KEY Language (Language),
  KEY Name (Name),
  KEY ModificationTimestamp (ModificationTimestamp)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE search_index (
  IndexID int(5) NOT NULL auto_increment,
  WordID int(5) NOT NULL default '0',
  ArticleID int(5) NOT NULL default '0',
  WordCount int(5) NOT NULL default '0',
  PRIMARY KEY  (IndexID),
  KEY WordCount (WordCount),
  KEY WordID_ArticleID (WordID,ArticleID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE search_word (
  WordID int(5) NOT NULL auto_increment,
  Word varchar(100) NOT NULL default '',
  PRIMARY KEY  (WordID),
  UNIQUE KEY Word (Word)
) ENGINE=MyISAM DEFAULT CHARSET=latin;
