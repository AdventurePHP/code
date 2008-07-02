<?php
   import('modules::cmscontentparser::biz','cmsArticle');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  @package modules::cmscontentparser::data
   *  @module cmsContentMapper
   *
   *  Implementiert die Daten-Schicht.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 14.08.2006<br />
   *  Version 0.2, 29.03.2007 (MySQLHandler wird nun als ServiceObject geladen)<br />
   */
   class cmsContentMapper extends coreObject
   {

      function cmsContentMapper(){
      }


      /**
      *  @module getPageContent()
      *  @public
      *
      *  Liest einen Artikel aus der Datenbank und gibt den Inhalt zurück.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.08.2006<br />
      *  Version 0.2, 03.01.2008 (Umbau auf Adressierung via Name und Nummer)<br />
      */
      function getPageContent($PageNoOrName,$publicOnly){

         // Referenz auf den MySQLHandler holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Herausfinden, ob Seite öffentlich zugänglich
         $select = 'SELECT Public FROM cms_content
                    WHERE
                       CIndex = \''.$PageNoOrName.'\'
                       OR
                       Name = \''.$PageNoOrName.'\'
                    ;';
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);
         $public = $data['Public'];

         if($publicOnly == true && $public != '1'){
            $content = (string)'';
          // end if
         }
         else{

            $select = 'SELECT Inhalt FROM cms_content
                       WHERE
                          CIndex = \''.$PageNoOrName.'\'
                          OR
                          Name = \''.$PageNoOrName.'\'
                       ;';
            $result = $SQL->executeTextStatement($select);
            $data = $SQL->fetchData($result);
            $content = $data['Inhalt'];

          // end else
         }

         return $content;

       // end function
      }


      /**
      *  @module __map2DomainObject()
      *  @private
      *
      *  Mappt ein Result-Set ein ein Domain-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.08.2006<br />
      */
      function __map2DomainObject($resultSet){

         $a = new cmsArticle();

         if(isset($resultSet['Name'])){
            $a->setName($resultSet['Name']);
          // end if
         }
         if(isset($resultSet['Inhalt'])){
            $a->setContent($resultSet['Inhalt']);
          // end if
         }
         if(isset($resultSet['Version'])){
            $a->setVersion($resultSet['Version']);
          // end if
         }
         if(isset($resultSet['Public'])){
            if($resultSet['Public'] == '1'){
               $a->setStatus('public');
             // end if
            }
            else{
               $a->setStatus('private');
             // end if
            }
          // end if
         }
         if(isset($resultSet['CIndex'])){
            $a->setID($resultSet['CIndex']);
          // end if
         }

         return $a;

       // end function
      }

    // end class
   }
?>