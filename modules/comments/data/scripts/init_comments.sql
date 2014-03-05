CREATE TABLE `article_comments` (
`ArticleCommentID` INT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Name` VARCHAR( 100 ) NOT NULL ,
`EMail` VARCHAR( 100 ) NOT NULL ,
`Comment` TEXT NOT NULL ,
`Date` DATE NOT NULL ,
`Time` TIME NOT NULL ,
`CategoryKey` VARCHAR( 100 ) NOT NULL ,
INDEX ( `Date` , `Time` , `CategoryKey` ) 
) ENGINE = MYISAM ;