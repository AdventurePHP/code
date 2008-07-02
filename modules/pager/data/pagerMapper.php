<?php
   import('core::database','MySQLHandler');
   import('modules::pager::biz','pageObject');


   /**
   *  @package modules::pager::data
   *  @class pagerMapper
   *
   *  Repräsentiert die Daten-Schicht der Pagers.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   */
   class pagerMapper extends coreObject
   {

      function pagerMapper(){
      }


      /**
      *  @public
      *
      *  Zeigt die aktuelle Anzahl an Datensätzen an.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 16.08.2006 (Zusätzliche Parameter für das Count-Statement eingeführt)<br />
      */
      function getEntriesCountValue($Namespace,$Statement,$Params = array()){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $result = $SQL->executeStatement($Namespace,$Statement,$Params);
         $data = $SQL->fetchData($result);
         return $data['EntriesCount'];

       // end function
      }


      /**
      *  @public
      *
      *  Läd die aktuellen IDs, die auf der aktuellen Seite angezeigt werden sollen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      */
      function loadEntries($Namespace,$Statement,$Params = array()){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $result = $SQL->executeStatement($Namespace,$Statement,$Params);

         $list = array();

         while($data = $SQL->fetchData($result)){
            $list[] = $data['DB_ID'];
          // end while
         }

         return $list;

       // end function
      }

    // end class
   }
?>