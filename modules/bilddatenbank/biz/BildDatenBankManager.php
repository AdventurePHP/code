<?php
   import('modules::bilddatenbank::data','BildDatenBankMapper');
   import('tools::image','imageManager');
   import('modules::pager::biz','pagerManager');
   import('core::singleton','Singleton');


   /**
   *  @package modules::bilddatenbank::biz
   *  @module BildDatenBankManager
   *
   *  Implementiert die Business-Schicht.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 29.05.2005<br />
   *  Version 0.2, 16.08.2006 (Re-Implementierung wegen neuer Pager-Komponente)<br />
   */
   class BildDatenBankManager extends coreObject
   {

      function BildDatenBankManager(){
      }


      /**
      *  @module loadPictures()
      *  @public
      *
      *  Liefert die aktuell anzuzeigende Menge von Bildern zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 16.08.2006<br />
      *  Version 0.2, 03.03.2007 (Calltime-Pass-Reference-Problem aufgel�st)<br />
      */
      function loadPictures(){

         // ID's der Eintr�ge vom Pager laden
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchadbildDatenbank');
         $entries = $pM->loadEntries();

         // Eintr�ge laden
         $pictures = array();

         // Mapper-Instanz holen
         $bdbM = &$this->__getServiceObject('modules::bilddatenbank::data','BildDatenBankMapper');

         for($i = 0; $i < count($entries); $i++){
            $pictures[] = $bdbM->loadPicture($entries[$i]);
          // end for
         }

         // Anzeige-Fenster-Gr��e ermitteln
         $this->__calculateWindowSize($pictures);

         return $pictures;

       // end function
      }


      /**
      *  @module getPagerOutput()
      *  @public
      *
      *  Gibt den HTML-Code der Pager-Ausgabe-Implementierung an die Pr�sentations-Schichtzur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 16.08.2006<br />
      */
      function getPagerOutput(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchadbildDatenbank');
         return $pM->getPager();
       // end function
      }


      /**
      *  @module getPagerStartName()
      *  @public
      *
      *  Gibt den URL-Parameter 'StartName' des Pagers zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.03.2007<br />
      */
      function getPagerStartName(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchadbildDatenbank');
         $Param = $pM->getPagerURLParameters();
         return $Param['StartName'];
       // end function
      }


      /**
      *  @module getPagerStartName()
      *  @public
      *
      *  Gibt den URL-Parameter 'CountName' des Pagers zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.03.2007<br />
      */
      function getPagerCountName(){
         $pM = &$this->__getAndInitServiceObject('modules::pager::biz','pagerManager','SchadbildDatenbank');
         $Param = $pM->getPagerURLParameters();
         return $Param['CountName'];
       // end function
      }


      /**
      *  @module ladeBildDatenPerIndex()
      *  @public
      *
      *  L�d ein BildElement. Wrapper f�r die Daten-Schicht-Methode loadPicture().<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      */
      function loadPicture($ID){
         $bdbM = &$this->__getServiceObject('modules::bilddatenbank::data','BildDatenBankMapper');
         return $bdbM->loadPicture($ID);
       // end function
      }


      /**
      *  @module __calculateWindowSize()
      *  @private
      *
      *  Ermittelt die Anzeige-Fenster-Ma�e f�r ein Objekt.<br />
      *  WICHTIG: Array mit Objekten muss als Referenz �bergeben werden.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      */
      function __calculateWindowSize(&$pictures){

         for($i = 0; $i < count($pictures); $i++){

            $properties = imageManager::showImageAttributes(SCHADBILDER_MEDIA_PATH.'/'.basename($pictures[$i]->zeigeBild()));
            $pictures[$i]->setzeAnsichtFensterBreite((int) $properties['Width'] + 40);
            $pictures[$i]->setzeAnsichtFensterHoehe((int) $properties['Height'] + 40);

          // end function
         }

       // end function
      }

    // end class
   }
?>