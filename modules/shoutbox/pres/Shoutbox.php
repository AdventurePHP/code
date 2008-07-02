<?php
   import('modules::shoutbox::biz','ShoutboxEintragObjekt');
   import('modules::shoutbox::biz','ShoutboxManager');
   import('modules::pager::pres','Pager');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');
   import('tools::variablen','variablenHandler');
   import('tools::validator','myValidator');
   import('tools::datetime','dateTimeManager');
   import('core::contentparser','ContentParser');
   import('tools::string','bbCodeParser');
   import('tools::form','formManager');


   /**
   *  Modul Shoutbox
   *  Klasse Shoutbox implementiert die Präsentationsschicht des Shoutboxmoduls
   *
   *  Christian Schäfer
   *  Version 0.1, 05.05.2005
   */
   class Shoutbox extends coreObject
   {
      var $_LOCALS;

      function Shoutbox(){

         $this->_LOCALS = variablenHandler::registerLocal(array('Aktion' => 'anzeigen',
                                                                'Text','Pager_Start' => '0',
                                                                'Pager_Anzahl' => '6',
                                                                'Pager_MaxZahl',
                                                                'Pager_Seite' => $_SERVER['REQUEST_URI']
                                                               )
                                                         );
       // end function
      }


      /**
      *  Funktion parseShoutboxTag()
      *
      *  Die Funktion stellt den Eventhandler für die verschiedenen Aktionen dar.
      *
      *  Christian Schäfer
      *  Version 0.1, 05.05.2005
      */
      function parseShoutboxTag($Text){

         // Ausgabepuffer
         $Ausgabe = (string)'';


         // Aktionen ausführen
         if($this->_LOCALS['Aktion'] == 'anzeigen'){
            $Ausgabe = str_replace('[Shoutbox]',$this->erzeugeAusgabeAnsicht(),$Text);
          // end if
         }
         if($this->_LOCALS['Aktion'] == 'erstellen'){

            if(isset($_REQUEST['NeuerEintragButton']) && myValidator::validateText($this->_LOCALS['Text'])){

               // ShoutboxEintragObjekt (Domänen-Objekt) erzeugen
               $ShoutboxEintragObjekt = new ShoutboxEintragObjekt();
               $ShoutboxEintragObjekt->setzeText($this->_LOCALS['Text']);
               $ShoutboxEintragObjekt->setzeDatum(dateTimeManager::generateDate());
               $ShoutboxEintragObjekt->setzeUhrzeit(dateTimeManager::generateTime());


               // Eintrag erzeugen
               $ShoutboxManager = new ShoutboxManager();
               $ShoutboxManager->erzeugeEintrag($ShoutboxEintragObjekt);

             // end if
            }
            else{
               $Ausgabe = str_replace('[Shoutbox]',$this->erzeugeFormularAnsicht(),$Text);
             // end else
            }

          // end if
         }

         // Ausgabe zurückgeben
         return $Ausgabe;

       // end function
      }


      function erzeugeAusgabeAnsicht(){

         $CP = new ContentParser();
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Ausgabe-Design
         $CP->erzeugeTemplate('Ausgabe');
         $CP->Templates['Ausgabe']->ladeDesign('config::modules::shoutbox','shoutbox_ausgabe.html');


         // Bild und Bild-URL setzen
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         $CP->Templates['Ausgabe']->setzePlatzhalter('Link',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Aktion' => 'erstellen','Pager_Start' => '','Pager_Anzahl' => '','Pager_MaxZahl' => '')));
         $CP->Templates['Ausgabe']->setzePlatzhalter('BildURL',$URLBasePath.'/bild.php?Bild=shoutbox_eintrag_neu.gif');


         // Eintraege-Design
         $CP->erzeugeTemplate('Eintrag');
         $CP->Templates['Eintrag']->ladeDesign('config::modules::shoutbox','shoutbox_eintraege.html');


         // MaximalZahl der Einträge für Pager zur Verfügung stellen
         $select_pager = "SELECT Datum FROM shoutbox;";
         $result_pager = $SQL->executeTextStatement($select_pager);
         $this->_LOCALS['Pager_MaxZahl'] = $SQL->getNumRows($result_pager);


         // Pager ausgeben
         $Pager = new MyPager($this->_LOCALS['Pager_Start'],$this->_LOCALS['Pager_Anzahl'],$this->_LOCALS['Pager_MaxZahl'],$_SERVER['REQUEST_URI']);
         $CP->Templates['Ausgabe']->setzePlatzhalter('Pager',$Pager->erzeugePagerAnsicht());


         // Daten vom Manager laden
         $ShoutboxManager = new ShoutboxManager();
         $Daten = $ShoutboxManager->ladeDatenPerLimit($this->_LOCALS['Pager_Start'],$this->_LOCALS['Pager_Anzahl']);


         // Ausgabe erzeugen
         $EintraegePuffer = (string)'';

         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');

         for($i = 0; $i < count($Daten); $i++){

            $CP->Templates['Eintrag']->setzePlatzhalter('Datum',dateTimeManager::convertDate2Normal($Daten[$i]->zeigeDatum()));
            $CP->Templates['Eintrag']->setzePlatzhalter('Uhrzeit',$Daten[$i]->zeigeUhrzeit());
            $CP->Templates['Eintrag']->setzePlatzhalter('Text',$bbCP->parseText($Daten[$i]->zeigeText()));

            $EintraegePuffer .= $CP->Templates['Eintrag']->erzeugeAusgabe();

          // end while
         }

         // Inhalt einsetzen
         $CP->Templates['Ausgabe']->setzePlatzhalter('Eintraege',$EintraegePuffer);


         // Ausgabe erzeugen
         $Ausgabe = $CP->Templates['Ausgabe']->erzeugeAusgabe();


         // Ausgabe zurückgeben
         return $Ausgabe;

       // end function
      }


      function erzeugeFormularAnsicht(){

         $CP = new ContentParser();

         // Form für neuen Eintrag
         $CP->erzeugeTemplate('NeuerEintrag');
         $CP->Templates['NeuerEintrag']->ladeDesign('config::modules::shoutbox','shoutbox_neuer_eintrag.html');

         // FormBeginn
         $FORM = new formManager();

         // FormBeginn
         $CP->Templates['NeuerEintrag']->setzePlatzhalter('FormBeginn',$FORM->FormBeginn('ShoutboxNeuerEintrag',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pager_Start' => '','Pager_Anzahl' => '','Pager_MaxZahl' => ''))));

         // TextFeld
         $FORM->TextArea('Text',$this->_LOCALS['Text'],'eingabe_feld','overflow: auto; width: 100%; height: 100px;','0');
         $FORM->TextArea['Text']->setzeValidator('NeuerEintragButton');
         $CP->Templates['NeuerEintrag']->setzePlatzhalter('TextFeld',$FORM->TextArea['Text']->generiereTextArea());

         // Button
         $CP->Templates['NeuerEintrag']->setzePlatzhalter('Button',$FORM->Button('NeuerEintragButton','Eintragen','eingabe_feld',''));

         // FormEnde
         $CP->Templates['NeuerEintrag']->setzePlatzhalter('FormEnde',$FORM->FormEnde());

         // Inhalt erzeugen
         $Ausgabe = $CP->Templates['NeuerEintrag']->erzeugeAusgabe();


         // Ausgabe zurückgeben
         return $Ausgabe;

       // end function
      }

    // end class
   }
?>
