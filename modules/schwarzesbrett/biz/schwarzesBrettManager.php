<?php
   import('core::filesystem','filesystemHandler');
   import('core::singleton','Singleton');
   import('tools::variablen','variablenHandler');
   import('tools::datetime','dateTimeManager');
   import('tools::cache','cacheV4Manager');
   import('modules::schwarzesbrett::biz','schwarzesBrettEintrag');
   import('modules::schwarzesbrett::data','schwarzesBrettMapper');
   import('modules::pager::biz','pagerManager');


   /**
   *  @package modules::schwarzesbrett:biz
   *  @module schwarzesBrettManager
   *
   *  Implementiert die Business-Schicht des Schwarzen Brettes.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 04.01.2006<br />
   *  Version 0.2, 11.03.2006<br />
   *  Version 0.3, 06.08.2006 (An neuen Pager angepasst)<br />
   */
   class schwarzesBrettManager extends coreObject
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      /**
      *  @module schwarzesBrettManager()
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert die verwendeten Attribute.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 11.03.2006<br />
      *  Version 0.3, 29.03.2007 (Implementierung nach PC V2)<br />
      */
      function schwarzesBrettManager(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Seite'));
       // end function
      }


      /**
      *  @module getPagerStartName()
      *  @public
      *
      *  Gibt den URL-Parameter 'StartName' des Pagers zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function getPagerStartName(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchwarzesBrett');
         $Param = $pM->getPagerURLParameters();
         return $Param['StartName'];
       // end function
      }


      /**
      *  @module getPagerStartName()
      *  @public
      *
      *  Gibt den URL-Parameter 'CountName' des Pagers zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function getPagerCountName(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchwarzesBrett');
         $Param = $pM->getPagerURLParameters();
         return $Param['CountName'];
       // end function
      }


      /**
      *  @module speichereEintrag()
      *  @public
      *
      *  Speichert einen Eintrag.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 11.03.2006<br />
      *  Version 0.3, 28.10.2007 (PNGs ergänzt)<br />
      */
      function speichereEintrag($E){

         // Datei bei Bedarf hochladen
         if(!empty($_FILES['Anhang']['tmp_name'])){

            $MIME = array();
            $MIME[] = 'application/pdf';
            $MIME[] = 'image/jpeg';
            $MIME[] = 'image/jpg';
            $MIME[] = 'image/pjpeg';
            $MIME[] = 'image/gif';
            $MIME[] = 'image/png';
            $MIME[] = 'application/msword';
            $MIME[] = 'application/vnd.ms-excel';
            $MIME[] = 'application/vnd.ms-powerpoint';

            // Datei hochladen
            $FS = new filesystemHandler(MEDIA_PATH);
            $FileName = $FS->uploadFile(MEDIA_PATH,$_FILES['Anhang']['tmp_name'],$_FILES['Anhang']['name'],$_FILES['Anhang']['size'],'2097152',$_FILES['Anhang']['type'],$MIME);
            if($FileName != 'error_mime_size' && $FileName != 'error'){
               $E->setzeAttribut('Anhang',$FileName);
            }
            else{
               $E->setzeAttribut('Anhang','');
             // end else
            }

          // end if
         }
         else{
            $E->setzeAttribut('Anhang','');
          // end else
         }


         // Datensatz speichern
         $M = &$this->__getServiceObject('modules::schwarzesbrett::data','schwarzesBrettMapper');
         $M->speichereEintrag($E);


         // Cache des Schwarzen Brettes löschen
         $cM = &$this->__getAndInitServiceObject('tools::cache','cacheV4Manager','cms');
         $cM->clearPageCache($this->_LOCALS['Seite']);


         // Header senden um auf die Anzeige-Seite zu kommen
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Aktion' => 'anzeigen'));
         header('Location: '.$Link);

       // end function
      }


      /**
      *  @module __loescheAlteEintraege()
      *  @private
      *
      *  Löscht beim Laden diejenigen Einträge, die älter als 14 Tagen sind. Diese<br />
      *  werden jedoch nur logisch per Flag gelöscht.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      */
      function __loescheAlteEintraege(){

         $Config = &$this->__getConfiguration('modules::schwarzesbrett','schwarzesbrett');
         $Gueltigkeit = $Config->getValue('Standard','Gueltigkeit');

         $AblaufDatum = dateTimeManager::calculateDate(dateTimeManager::generateDate(),array('Jahr' => '0', 'Monat' => '0', 'Tag' => $Gueltigkeit));

         $M = &$this->__getServiceObject('modules::schwarzesbrett::data','schwarzesBrettMapper');
         $M->loescheAlteEintraege($AblaufDatum);

       // end function
      }


      /**
      *  @module ladeEintraegeZurAnzeige()
      *  @public
      *
      *  Läd Einträge des Schwarzenbrettes. Pager wird als neue Business-Komponente eingesetzt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      */
      function ladeEintraegeZurAnzeige(){

         // Alte Einträge löschen
         $this->__loescheAlteEintraege();

         // Referenz auf den Mapper holen
         $M = &$this->__getServiceObject('modules::schwarzesbrett::data','schwarzesBrettMapper');

         // Einträge vom Pager laden lassen
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchwarzesBrett');
         $Entries = $pM->loadEntries();

         $EntryArray = array();

         for($i = 0; $i < count($Entries); $i++){
            $EntryArray[] = $M->ladeEintrag($Entries[$i]);
          // end for
         }

         // Objekte zurückgeben
         return $EntryArray;

       // end function
      }


      /**
      *  @module generatePager()
      *  @public
      *
      *  Wrapt die Erzeugung der Pager-Anzeige.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      */
      function generatePager(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchwarzesBrett');
         return $pM->getPager();
       // end function
      }

    // end class
   }
?>