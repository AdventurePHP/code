<?php
   import('core::database','MySQLHandler');
   import('modules::pager::biz','pageObject');


   /**
   *  @package modules::pager::data
   *  @class pagerMapper
   *
   *  Repr�sentiert die Daten-Schicht der Pagers.<br />
   *
   *  @author Christian Sch�fer
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
      *  Zeigt die aktuelle Anzahl an Datens�tzen an.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 16.08.2006 (Zus�tzliche Parameter f�r das Count-Statement eingef�hrt)<br />
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
      *  L�d die aktuellen IDs, die auf der aktuellen Seite angezeigt werden sollen.<br />
      *
      *  @author Christian Sch�fer
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