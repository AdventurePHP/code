<?php
   import('core::session','sessionManager');


   /**
   *  @package modules::baumstruktur::data
   *  @module BaumManager
   *
   *  Implementiert den Baum-Manager.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 28.06.2005<br />
   *  Version 0.2, 01.07.2005<br />
   *  Version 0.3, 15.06.2006<br />
   *  Version 0.4. 18.03.2007 (Implementierung nach PC V2)<br />
   */
   class TreeManager extends coreObject
   {

      var $__Session;


      /**
      *  @module BaumManager
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      *  Version 0.3, 15.06.2006<br />
      *  Version 0.4. 18.03.2007 (Implementierung nach PC V2)<br />
      */
      function TreeManager(){
         $this->__Session = new sessionManager('BaumAnzeige');
       // end function
      }


      /**
      *  @module ladePfadeInSession()
      *  @public
      *
      *  Läd aktuell geöffnete Pfade in die Session, erweitert oder löscht diese.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 15.06.2006 (Session-Handling angepasst)<br />
      *  Version 0.3, 16.12.2006 (Fehler behoben, dass der Pfad kein Array sein kann)<br />
      *  Version 0.4, 17.12.2006 (Möglichkeit hinzugefügt, dass multiple Pfade geöffnet werden können)<br />
      */
      function ladePfadeInSession(){

         // Geöffnete Pfade in Session-Array einsetzen
         if($this->__Session->loadSessionData('Pfad') == false && !isset($_REQUEST['Pfad']) && !isset($_REQUEST['BaumAktion'])){
            $this->__Session->saveSessionData('Pfad',array());
          // end if
         }
         else{

            if(isset($_REQUEST['Pfad']) && isset($_REQUEST['BaumAktion'])){

               $Pfad = $this->__Session->loadSessionData('Pfad');


               // Fehler abfangen, dass $Pfad u.U. kein Array ist (Session wurde frisch inizialisiert)
               if(!is_array($Pfad)){
                  $Pfad = array();
                // end if
               }


               // Prüfen, ob mehrere Pfade übergeben wurden und behandle das Öffnen des Baumes
               if(substr_count($_REQUEST['Pfad'],';') > 0){

                  $PfadFolge = preg_split('/;/',$_REQUEST['Pfad']);

                  for($i = 0; $i < count($PfadFolge); $i++){

                     if($_REQUEST['BaumAktion'] == 'Oeffnen' && !in_array($PfadFolge[$i],$Pfad)){
                        $Pfad = array_merge($Pfad,array(trim($PfadFolge[$i])));
                      // end if
                     }

                   // end for
                  }

                  // Bearbeitete Pfade in Session speichern
                  $this->__Session->saveSessionData('Pfad',$Pfad);

                // end if
               }
               else{

                  if($_REQUEST['BaumAktion'] == 'Oeffnen' && !in_array($_REQUEST['Pfad'],$Pfad)){
                     $this->__Session->saveSessionData('Pfad',array_merge($Pfad,array(trim($_REQUEST['Pfad']))));
                   // end if
                  }

                // end else
               }


               // Behandelt das Schließen des Baumes
               if($_REQUEST['BaumAktion'] == 'Schliessen'){

                  foreach($Pfad AS $Key => $Wert){

                     if($Wert == $_REQUEST['Pfad']){
                        unset($Pfad[$Key]);
                      // end if
                     }

                   // end foreach
                  }


                  // Speichert die gewonnen Daten in der Session
                  $this->__Session->saveSessionData('Pfad',$Pfad);

                // end if
               }

             // end if
            }

          // end else
         }

       // end function
      }



      /**
      *  @module hatKnotenKinder()
      *  @public
      *
      *  Untersucht, ob ein Knoten Kinder hat.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      *  Version 0.2, 12.03.2006<br />
      */
      function hatKnotenKinder($Knoten){
         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');
         return $M->hatKnotenKinder($Knoten);
       // end function
      }


      /**
      *  @module loescheKnoten()
      *  @public
      *
      *  Löscht einen Knoten anhand des übergebenen Index.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      *  Version 0.2, 12.03.2006<br />
      */
      function loescheKnoten($Knoten){
         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');
         $M->loescheKnoten($Knoten);
       // end function
      }


      /**
      *  @module ladeEinzelnenKnotenPerIndex()
      *  @public
      *
      *  Läd einen Knoten anhand eines Indexes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      */
      function ladeEinzelnenKnotenPerIndex($Index){
         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');
         return $M->ladeEinzelnenKnotenPerIndex($Index);
       // end function
      }


      /**
      *  @module speicherNeuenOrdnerKnoten()
      *  @public
      *
      *  Speichert einen Ordner-Knoten ab.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      */
      function speicherNeuenOrdnerKnoten($Name,$VaterID){
         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');
         $M->speicherNeuenOrdnerKnoten($Name,$VaterID);
       // end function
      }


      /**
      *  @module speichereKnoten()
      *  @public
      *
      *  Speichert einen Knoten ab.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      */
      function speichereKnoten($Knoten){

         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');

         if($Knoten->zeigeTyp() == 'file'){
            $M->speichereDateiKnoten($Knoten);
          // end if
         }
         if($Knoten->zeigeTyp() == 'dir'){
            $M->speichereOrdnerKnoten($Knoten);
          // end if
         }

       // end function
      }


      /**
      *  @module ladeAlleOrdner()
      *  @public
      *
      *  Läd alle Ordner ohne Beziehungen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      */
      function ladeAlleOrdner($Ausnahme){

         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');

         $Ordner = $M->ladeAlleOrdner();

         $OrdnerNeu = array();

         // Ordner selbst rausfiltern
         for($i = 0; $i < count($Ordner); $i++){
            if($Ordner[$i]->zeigeIndex() != $Ausnahme){
               $OrdnerNeu[] = $Ordner[$i];
             // end if
            }
          // end for
         }


         return $OrdnerNeu;

       // end function
      }


      /**
      *  @module ladeBaum()
      *  @public
      *
      *  Läd den Baum anhand des Einsprungpunktes Root<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      */
      function ladeBaum(){

         $Baum = $this->ladeRoot();
         $this->ladeKnoten($Baum->zeigeIndex(),$Baum);
         return $Baum;

       // end function
      }


      /**
      *  @module ladeRoot()
      *  @public
      *
      *  Läd den Root-Knoten<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 12.03.2006<br />
      */
      function ladeRoot(){
         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');
         return $M->ladeDatenFuerRoot();
       // end function
      }


      /**
      *  @module ladeKnoten()
      *  @public
      *
      *  Läd die Knoten des Baumes nach<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 12.03.2006<br />
      *  Version 0.3, 15.06.2006 (Session-Handling angepasst)<br />
      *  Version 0.2, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      */
      function ladeKnoten($Pfad,&$Baum){

         $M = &$this->__getServiceObject('modules::baumstruktur::data','BaumMapper');

         // Baum-Teile per Pfad laden
         $PfadDaten = $M->ladeDatenFuerPfad($Pfad);

         // Pfad aus Session lesen
         $Pfad = $this->__Session->loadSessionData('Pfad');

         if($Pfad == false){
            $Pfad = array();
          // end if
         }


         // Baum rekursiv aufbauen
         for($i = 0; $i < count($PfadDaten); $i++){

            // Weitere Ebene erzeugen, oder nur Kind anfügen
            if(in_array($PfadDaten[$i]->zeigeIndex(),$Pfad)){

               // Neues Kind einfügen
               $Tmp = $PfadDaten[$i];

               // Zeige Kind-Knoten auf den ausgewählten Knoten anwenden
               $this->ladeKnoten($PfadDaten[$i]->zeigeIndex(),$Tmp);

               // Knoten in Vater einsetzen
               $Baum->setzeKind($Tmp);

             // end if
            }
            else{

               // Neues Kind einfügen
               $Tmp = $PfadDaten[$i];

               // Knoten in Vater einsetzen
               $Baum->setzeKind($Tmp);

             // end else
            }

          // end for
         }

       // end function
      }

    // end class
   }
?>