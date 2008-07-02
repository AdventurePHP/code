<?php
   import('core::database','MySQLHandler');


   /**
   *  @package modules::webstat
   *  @module webStatMapper
   *
   *  Implementiert den Data-Mapper des Webstat-Moduls. Zur Auswertung muss eine<br />
   *  Tabelle mit folgenden Statement angelegt werden:<br />
   *  <br /><pre>
   *      CREATE TABLE statistiken (
   *                    Name varchar(60) NOT NULL default '',
   *                    RequestURI text NOT NULL,
   *                    Tag int(2) NOT NULL default '0',
   *                    Monat int(2) NOT NULL default '0',
   *                    Jahr int(4) NOT NULL default '0',
   *                    Stunde int(2) NOT NULL default '0',
   *                    Minute int(2) NOT NULL default '0',
   *                    Sekunde int(2) NOT NULL default '0',
   *                    BenutzerName varchar(40) NOT NULL default '',
   *                    SessionID varchar(40) NOT NULL default '',
   *                    Browser varchar(60) NOT NULL default '',
   *                    Sprache varchar(20) NOT NULL default '',
   *                    Betriebssystem varchar(60) NOT NULL default '',
   *                    IPAdresse varchar(15) NOT NULL default '',
   *                    DNSAdresse varchar(40) NOT NULL default '',
   *                    Herkunft varchar(100) NOT NULL default '',
   *                    UserAgent text NOT NULL,
   *                    STATIndex int(10) NOT NULL auto_increment,
   *                    PRIMARY KEY (STATIndex),
   *                    KEY Tag (Tag),
   *                    KEY Monat (Monat),
   *                    KEY Jahr (Jahr),
   *                    KEY Stunde (Stunde),
   *                    KEY Minute (Minute),
   *                    KEY Sekunde (Sekunde),
   *                    KEY BenutzerName (BenutzerName),
   *                    KEY SessionID (SessionID),
   *                    KEY IPAdresse (IPAdresse),
   *                    KEY DNSAdresse (DNSAdresse),
   *                    KEY Browser (Browser),
   *                    KEY Betriebssystem (Betriebssystem),
   *                    KEY Herkunft (Herkunft)
   *                  ) ENGINE=MyISAM;
   *  </pre>
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.12.2005<br />
   *  Version 0.2, 22.12.2005<br />
   *  Version 0.3, 08.03.2006<br />
   */
   class webStatMapper extends coreObject
   {

      function webStatMapper(){
      }


      /**
      *  @module showPageName()
      *  @public
      *
      *  Ermittelt den Namen einer Seite, die durch eine Nummer spezifiziert ist.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.12.2005<br />
      *  Version 0.2, 05.06.2006 (MySQLHandler wird nun singleton instanziiert)<br />
      */
      function showPageName($PageNumber){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         $select = 'SELECT Name AS Name FROM cms_content WHERE CIndex = \''.$PageNumber.'\';';
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);

         return $data['Name'];

       // end function
      }


      /**
      *  @module createStatEntry()
      *  @public
      *
      *  Erzeugt einen Statistik-Eintrag in der Datenbank.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 10.04.2004<br />
      *  Version 0.2, 23.10.2004<br />
      *  Version 0.3, 19.01.2005<br />
      *  Version 0.4, 05.04.2005<br />
      *  Version 0.5, 22.12.2005<br />
      *  Version 0.6, 23.02.2006 (Anlegen der Tabelle wird nicht mehr geprüft)<br />
      *  Version 0.7, 05.06.2006 (MySQLHandler wird nun singleton instanziiert)<br />
      */
      function createStatEntry($Name,$RequestURI,$Tag,$Monat,$Jahr,$Stunde,$Minute,$Sekunde,$BenutzerName,$SessionID,$Browser,$Sprache,$Betriebssystem,$IPAdresse,$DNSAdresse,$Herkunft,$UserAgent){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $insert = "INSERT INTO statistiken (Name, RequestURI, Tag, Monat, Jahr, Stunde, Minute, Sekunde, BenutzerName, SessionID, Browser, Sprache, Betriebssystem, IPAdresse, DNSAdresse, Herkunft,UserAgent) VALUES ('".$Name."', '".$RequestURI."', '".$Tag."', '".$Monat."', '".$Jahr."', '".$Stunde."', '".$Minute."', '".$Sekunde."', '".$BenutzerName."', '".$SessionID."', '".$Browser."', '".$Sprache."', '".$Betriebssystem."', '".$IPAdresse."', '".$DNSAdresse."', '".$Herkunft."','".$UserAgent."');";
         $SQL->executeTextStatement($insert);

       // end function
      }

    // end class
   }
?>