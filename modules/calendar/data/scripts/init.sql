CREATE TABLE termine (
  Datum date NOT NULL default '0000-00-00',
  Text varchar(100) NOT NULL default '',
  Link varchar(100) NOT NULL default '',
  DetailText text NOT NULL,
  TIndex int(3) NOT NULL auto_increment,
  PRIMARY KEY  (TIndex)
) ENGINE=MyISAM;
