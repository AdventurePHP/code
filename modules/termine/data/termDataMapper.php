<?php
   import('modules::termine::biz','termObject');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  @package modules::termine::data
   *  @module termDataMapper
   *
   *  Implementiert den Datenmapper für TerminDaten.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.04.2005<br />
   *  Version 0.2, 01.09.2006 (Umstellung Verwendung des MySQLHandler)<br />
   *  Version 0.3, 17.03.2007 (Klasse in termDataMapper umbenannt)<br />
   */
   class termDataMapper extends coreObject
   {

      function termDataMapper(){
      }


      /**
      *  @module loadTermByIndex()
      *  @public
      *
      *  Gibt Termin-Daten an die biz-Schicht zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.04.2005<br />
      *  Version 0.2, 01.09.2006 (Umstellung Verwendung des MySQLHandler)<br />
      *  Version 0.3, 17.03.2007 (Methode in loadTermByIndex() umbenannt)<br />
      */
      function loadTermByIndex($Index){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = 'SELECT * FROM termine WHERE TIndex = \''.$Index.'\';';
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);
         return $this->__map2DomainObject($data);

       // end function
      }


      /**
      *  @module loadTerms()
      *  @public
      *
      *  Gibt Termin-Daten an die biz-Schicht zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.04.2005<br />
      *  Version 0.2, 01.09.2006 (Umstellung auf Verwendung des MySQLHandler)<br />
      *  Version 0.3, 17.03.2007 (Methode in loadTerms() umbenannt)<br />
      */
      function loadTerms(){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = 'SELECT * FROM termine ORDER BY Datum ASC;';
         $result = $SQL->executeTextStatement($select);

         $Terms = array();

         while($data = $SQL->fetchData($result)){
            $Terms[] = $this->__map2DomainObject($data);
          // end while
         }

         return $Terms;

       // end function
      }


      /**
      *  @module __map2DomainObject()
      *  @private
      *
      *  Mappt eine Resultset in ein Domain-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.04.2005<br />
      *  Version 0.3, 17.03.2007 (Methode in __map2DomainObject() umbenannt)<br />
      */
      function __map2DomainObject($TerminResultSet){

         $TO = new termObject();

         if(isset($TerminResultSet['Datum'])){
            $TO->setzeDatum($TerminResultSet['Datum']);
          // end if
         }
         if(isset($TerminResultSet['Text'])){
            $TO->setzeText($TerminResultSet['Text']);
          // end if
         }
         if(isset($TerminResultSet['Link'])){
            $TO->setzeLink($TerminResultSet['Link']);
          // end if
         }
         if(isset($TerminResultSet['DetailText'])){
            $TO->setzeDetailText($TerminResultSet['DetailText']);
          // end if
         }
         if(isset($TerminResultSet['TIndex'])){
            $TO->setzeTIndex($TerminResultSet['TIndex']);
          // end if
         }

         return $TO;

       // end function
      }

    // end class
   }
?>