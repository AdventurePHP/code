<?php
   import('modules::bilddatenbank::biz','BildDaten');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  @package modules::bilddatenbank::data
   *  @module BildDatenBankMapper
   *
   *  Implementiert die Datenschicht.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.05.2005<br />
   *  Version 0.2, 16.08.2006 (An neue Pager-Implementierung angepasst)<br />
   */
   class BildDatenBankMapper extends coreObject
   {

      function BildDatenBankMapper(){
      }


      /**
      *  @module loadPicture()
      *  @public
      *
      *  Läd ein Eintrags-Objekt per ID.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 16.08.2006 (An neue Pager-Implementierung angepasst)<br />
      */
      function loadPicture($ID){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT * FROM schadbildarchiv WHERE SDIndex = '".$ID."';";
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);
         return $this->__mappeDatenAlsBildDatenBankObjekt($data);

       // end function
      }


      /**
      *  @module __mappeDatenAlsBildDatenBankObjekt()
      *  @private
      *
      *  Mappt ein DB-Resultset in ein BildDaten-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 16.08.2006 (An neue Pager-Implementierung angepasst)<br />
      */
      function __mappeDatenAlsBildDatenBankObjekt($ResultSet){

         $oBD = new BildDaten();

         if(isset($ResultSet['Kultur'])){
            $oBD->setzeKultur($ResultSet['Kultur']);
          // end if
         }
         if(isset($ResultSet['Ursache'])){
            $oBD->setzeUrsache($ResultSet['Ursache']);
          // end if
         }
         if(isset($ResultSet['Linkklein'])){
            $oBD->setzePictogramm($ResultSet['Linkklein']);
          // end if
         }
         if(isset($ResultSet['Linkgross'])){
            $oBD->setzeBild($ResultSet['Linkgross']);
          // end if
         }
         if(isset($ResultSet['SDIndex'])){
            $oBD->setzeIndex($ResultSet['SDIndex']);
          // end if
         }

         return $oBD;

       // end function
      }

    // end class
   }
?>