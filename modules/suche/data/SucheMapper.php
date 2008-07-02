<?php
   import('modules::suche::biz','SuchErgebnis');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  Package modules::suche::data<br />
   *  Klasse SucheMapper<br />
   *  Implementiert den Datenmapper für die Webseiten-Suche<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 29.03.2006<br />
   *  Version 0.2, 07.05.2006<br />
   */
   class SucheMapper extends coreObject
   {

      function SucheMapper(){
      }


      /**
      *  Funktion ladeSuchErgebnisPerBegriff()  [public/nonstatic]<br />
      *  Läd eine Liste von Suchergebnissen.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006 (MySQLHandler wird nun Singleton geladen)<br />
      *  Version 0.3, 29.10.2006 (Statement verbessert, da die OR-Verknüpfung nicht sauber aufgelöst wurde)<br />
      */
      function ladeSuchErgebnisPerBegriff($Begriff){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         $select = "SELECT Name, Inhalt, CIndex
                    FROM cms_content
                    WHERE
                       (Name LIKE '%".$Begriff."%' OR Inhalt LIKE '%".$Begriff."%')
                       AND
                       Public = '1'
                    ORDER BY Name ASC;";
         $result = $SQL->executeTextStatement($select);

         $dataList = array();

         while($data = $SQL->fetchData($result)){
            $dataList[] = $this->__mappeInDomainObjekt($data);
          // end while
         }

         return $dataList;

       // end function
      }


      /**
      *  Funktion __mappeInDomainObjekt()  [private/static]<br />
      *  Mappt ein SQL-Result-Set in das Domain-Objekt "SuchErgebnis".<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 29.03.2006<br />
      */
      function __mappeInDomainObjekt($ResultSet){

         $SE = new SuchErgebnis();

         if(isset($ResultSet['Name'])){
            $SE->setze('Name',$ResultSet['Name']);
          // end if
         }
         if(isset($ResultSet['Inhalt'])){
            $SE->setze('Inhalt',$ResultSet['Inhalt']);
          // end if
         }
         if(isset($ResultSet['CIndex'])){
            $SE->setze('ID',$ResultSet['CIndex']);
          // end if
         }

         return $SE;

       // end function
      }

    // end class
   }
?>
