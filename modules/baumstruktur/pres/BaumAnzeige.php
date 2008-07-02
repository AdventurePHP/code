<?php
   import('modules::baumstruktur::biz','BaumManager');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');
   import('core::session','sessionManager');
   import('tools::string','stringAssistant');


   /**
   *  @package modules::baumstruktur::pres
   *  @module BaumAnzeige
   *
   *  Implementiert die Präsentationsschicht des Moduls Baum<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.06.2005<br />
   *  Version 0.2, 10.07.2005<br />
   *  Version 0.3, 04.12.2005<br />
   *  Version 0.4, 15.06.2006<br />
   *  Version 0.5, 17.12.2006 (Methoden privatisiert;Dokumentation ergänzt)<br />
   *  Version 0.6, 18.03.2007 (Implementierung nach PC V2)<br />
   */
   class BaumAnzeige extends coreObject
   {

      var $__Puffer;
      var $_LOCALS;
      var $__Ebene;
      var $__AdminAnsicht;
      var $__Config;
      var $__Session;
      var $__URLBasePath;


      /**
      *  @module BaumAnzeige()
      *  @public
      *
      *  Initialisiert Variablen, die zur Ausgabe und zur Navigation des Baumes notwendig sind<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 15.06.2006<br />
      *  Version 0.3, 18.03.2007 (Implementierung nach PC V2)<br />
      */
      function BaumAnzeige($Abschnitt = 'Standard'){

         // Ausgabe-Puffer initialisieren
         $this->__Puffer = (string)'';

         // Variablen lokal registrieren
         $this->_LOCALS = variablenHandler::registerLocal(array('Pfad','Seite'));

         // Ebene auf Null setzen
         $this->__Ebene = 0;

         // Admin-Ansicht generieren
         $this->__AdminAnsicht = 0;

         // Session-Manager instanziieren
         $this->__Session = new sessionManager('BaumAnzeige');

         // initialize base path
         $Reg = &Singleton::getInstance('Registry');
         $this->__URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

       // end function
      }


      /**
      *  @module zeigeBaum()
      *  @public
      *
      *  Interface für die Ausgabe des Baumes. Gibt den Baum in HTML-Quelltext zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      *  Version 0.3, 18.03.2007 (Implementierung nach PC V2)<br />
      */
      function zeigeBaum($Baum){

         // Konfiguration Laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumanzeige');
         $this->__Config = $Config->getSection('Standard');

         // Baum-Ansicht erstellen
         $this->__erzeugeBaumAnsicht($Baum);

         // Ausgabe zurückgeben
         return $this->__Puffer;

       // end function
      }


      /**
      *  @module aktiviereAdminAnsicht()
      *  @public
      *
      *  Aktiviert die Admin-Ansicht des Baumes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.07.2005<br />
      */
      function aktiviereAdminAnsicht(){
         $this->__AdminAnsicht = 1;
       // end function
      }


      /**
      *  @module __erzeugeOrdnerKnoten()
      *  @public
      *
      *  Erzeugt einen Knoten im Ordner-Design.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      */
      function __erzeugeOrdnerKnoten($Knoten){

         $Puffer = (string)'';

         if(count($Knoten->zeigeKinder()) > 0){

            $Link = $this->__erzeugeOrdnerLink($Knoten,'Schliessen');

            // Admin Menü erzeugen
            if($this->__AdminAnsicht == 1){
               $Admin = $this->__erzeugeAdminMenu($Knoten);
             // end if
            }
            else{
               $Admin = (string)'';
             // end else
            }

            $Puffer = "<a href=\"".$Link."\"><img src=\"".$this->__erzeugeBildLink('minus.gif')."\" align=\"absmiddle\" border=\"0\" style=\"margin-right: 8px;\" /></a><img src=\"".$this->__erzeugeBildLink($this->__Config['OrdnerBild'])."\" align=\"absmiddle\" border=\"0\" style=\"margin-right: 8px;\" /></a><a href=\"".$Link."\" title=\"".stringAssistant::escapeSpecialCharacters($Knoten->zeigeName())."\">".$Knoten->zeigeName()."</a>".$Admin."<br />\n";

          // end if
         }
         else{

            $Link = $this->__erzeugeOrdnerLink($Knoten,'Oeffnen');

            // Admin Menü erzeugen
            if($this->__AdminAnsicht == 1){
               $Admin = $this->__erzeugeAdminMenu($Knoten);
             // end if
            }
            else{
               $Admin = (string)'';
             // end else
            }

            $Puffer = "<a href=\"".$Link."\"><img src=\"".$this->__erzeugeBildLink('plus.gif')."\" align=\"absmiddle\" border=\"0\" style=\"margin-right: 8px;\" /></a><img src=\"".$this->__erzeugeBildLink($this->__Config['OrdnerBild'])."\" align=\"absmiddle\" border=\"0\" style=\"margin-right: 8px;\" /><a href=\"".$Link."\" title=\"".$Knoten->zeigeName()."\">".$Knoten->zeigeName()."</a>".$Admin."<br />\n";

          // end else
         }

         return $Puffer;

       // end function
      }


      /**
      *  @module __erzeugeOrdnerLink()
      *  @public
      *
      *  Erzeugt einen Link für die Ordnerdarstellung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 07.07.2005 (Caching funktionierte nicht richtig)<br />
      *  Version 0.3, 04.12.2005 (Versuch, Caching zu verbessern)<br />
      *  Version 0.4, 13.03.2006 (Versuch 2, Caching zu verbessern)<br />
      *  Version 0.5, 15.06.2006 (Session-Handling angepasst)<br />
      *  Version 0.6, 15.06.2006 (Version 3 der Caching-ID-Generierung)<br />
      *  Version 0.7, 19.08.2006 (Linkgenerierung wg. URL-Rewriting angepasst)<br />
      */
      function __erzeugeOrdnerLink($Knoten,$Aktion){

         //$Link = linkHandler::loescheURIParameter($_SERVER['REQUEST_URI'],array('Pfad','BaumAktion','BaumID','Aktion','Knoten','SuchText','Suche'));
         // GEHT NICHT: $BaumID = md5(crypt($Knoten->zeigeIndex()));
         // Geht nicht bei mehreren Hosts: $BaumID = md5(serialize($_SESSION['Pfad']));

         // $CacheID = serialize($_SESSION['Pfad']).'::'.$_SERVER['REMOTE_ADDR'].'::'.date('Y-m-d').'::'.date('h');

         // $CacheID = serialize($this->__Session->loadSessionData('Pfad')).$_SERVER['REMOTE_ADDR'].date('Y-m-d').$this->__Session->getSessionID();
         // $BaumID = md5($CacheID);

         // Session-ID in Kombination mit der Remote-IP muss eindeutig sein
         // $BaumID = $_SERVER['REMOTE_ADDR'].':'.date('H:i').':'.$this->__Session->getSessionID();

         // Es hat bisher nichts richtig funktioniert -> Zufallszahl anhängen
         //$BaumID = date('Y-m-d-H-i-s');

         //list($usec, $sec) = explode(' ', microtime());
         //$BaumID = (float)$usec + (float)$sec;

         //$BaumID = (string)'';
         //trigger_error($BaumID);

         //$Link = linkHandler::pruefeLink($Link.'&Pfad='.$Knoten->zeigeIndex().'&BaumAktion='.$Aktion.'&BaumID='.$BaumID);

         //return linkHandler::pruefeLink($Link.'&Pfad='.$Knoten->zeigeIndex().'&BaumAktion='.$Aktion);


         // Neue Link-Generierung wg. URL-Rewriting eingeführt
         return linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => $Knoten->zeigeIndex(),'BaumAktion' => $Aktion,'BaumID' => '','Aktion' => '','Knoten' => '','SuchText' => '','Suche' => ''));

       // end function
      }


      /**
      *  @module __erzeugeBildLink()
      *  @public
      *
      *  Erzeugt einen Link für einen übergebenen Bildnamen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.07.2005<br />
      *  Version 0.2, 02.12.2005<br />
      */
      function __erzeugeBildLink($BildName){
         return $this->__URLBasePath.'/bild.php?Bild='.trim($BildName);
       // end function
      }


      /**
      *  @module __erzeugeAdminMenu()
      *  @public
      *
      *  Erzeugt das Admin-Menü für einen Knoten.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.07.2005<br />
      *  Version 0.2, 02.12.2005<br />
      *  Version 0.3, 19.08.2005 (Link-Generierung wg. URL-Rewriting angepasst)<br />
      */
      function __erzeugeAdminMenu($Knoten){

         $Erstellen = "<a href=\"".linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => '','BaumAktion' => '','BaumID' => '','Aktion' => '','Knoten' => $Knoten->zeigeIndex(),'pagepart' => 'erstellen'))."\"><img src=\"frontend/media/kulturinfopanel/erstellen.gif\" border=\"0\" title=\"Ordner oder Datei im aktuellen Ordner erstellen!\" /></a>";
         $Bearbeiten = "<a href=\"".linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => '','BaumAktion' => '','BaumID' => '','Aktion' => '','Knoten' => $Knoten->zeigeIndex(),'pagepart' => 'bearbeiten'))."\"><img src=\"frontend/media/kulturinfopanel/bearbeiten.gif\" border=\"0\" title=\"Ordner oder Datei bearbeiten!\" /></a>";
         $Loeschen = "<a href=\"".linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => '','BaumAktion' => '','BaumID' => '','Aktion' => '','Knoten' => $Knoten->zeigeIndex(),'pagepart' => 'loeschen'))."\"><img src=\"frontend/media/kulturinfopanel/loeschen.gif\" border=\"0\" title=\"Ordner oder Datei l&ouml;schen!\" /></a>";

         if($Knoten->zeigeTyp() == 'file'){
            $Return = "&nbsp;&nbsp;&nbsp;&nbsp;".$Bearbeiten."&nbsp;&nbsp;".$Loeschen;
          // end if
         }
         else{
            $Return = "&nbsp;&nbsp;".$Erstellen."&nbsp;&nbsp;".$Bearbeiten."&nbsp;&nbsp;".$Loeschen;
          // end else
         }

         return $Return;

       // end function
      }


      /**
      *  @module __erzeugeDateiKnoten()
      *  @public
      *
      *  Erzeugt einen Link für die Dateidarstellung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 10.07.2005<br />
      */
      function __erzeugeDateiKnoten($Knoten){

         // Admin Menü erzeugen
         if($this->__AdminAnsicht == 1){
            $Admin = $this->__erzeugeAdminMenu($Knoten);
          // end if
         }
         else{
            $Admin = (string)'';
          // end else
         }

         return "<img src=\"".$this->__erzeugeBildLink($this->__Config['DateiBild'])."\" align=\"absmiddle\" border=\"0\" style=\"margin-left: 20px; margin-right: 6px;\" /><span style=\"color: blue; cursor: pointer;\" onClick=\"window.open('".$this->__erzeugeDateiLink($Knoten)."','DokBib','toolbar=no,location=no,directories=no,status=no,menubar=no,closed=no,scrollbars=yes,resizable=yes,copyhistory=yes,width=600,height=500')\" title=\"".stringAssistant::escapeSpecialCharacters($Knoten->zeigeName())."\">".$Knoten->zeigeName()."</span>".$Admin."<br />\n";

       // end function
      }


      /**
      *  @module __erzeugeDateiLink()
      *  @public
      *
      *  Erzeugt einen Link für die Ordnerdarstellung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.07.2005<br />
      *  Version 0.2, 02.12.2005<br />
      *  Version 0.3, 12.03.2006<br />
      *  Version 0.4, 24.07.2006 (Anzeige-Element auf das standardisierte datei.php umgebogen)<br />
      */
      function __erzeugeDateiLink($Knoten){

         //$Link = $this->__URLBasePath.'/pdfbildeinblenden.php?Datei='.$Knoten->zeigeLink().'&Pfad='.$this->__Config['DokumentenPfad'];
         $Link = $this->__URLBasePath.'/datei.php?Datei='.$Knoten->zeigeLink().'&Pfad='.$this->__Config['DokumentenPfad'];
         return $Link;

       // end function
      }


      /**
      *  @module __erzeugeBaumAnsicht()
      *  @public
      *
      *  Erzeugt die eigentliche Baum-Ansicht.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 11.06.2006 (Bilder-Quellen verschoben)<br />
      *  Version 0.3, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      */
      function __erzeugeBaumAnsicht(&$Baum){

         // Root darstellen
         if($Baum->zeigeVaterID() == '0'){

            // Admin Menü erzeugen
            if($this->__AdminAnsicht == 1){
               $Admin = $this->__erzeugeAdminMenu($Baum);
             // end if
            }
            else{
               $Admin = (string)'';
             // end else
            }

            $this->__Puffer .= "<img src=\"".$this->__erzeugeBildLink($this->__Config['RootBild'])."\" align=\"absmiddle\" border=\"0\" style=\"margin-left: 7px; margin-right: 8px;\"/><strong>".$Baum->zeigeName()."</strong>".$Admin."<br />\n";

          // end if
         }


         // Ebene erhöhen
         $this->__Ebene++;


         // Kinder auslesen
         $Kinder = $Baum->zeigeKinder();


         // Ausgabe generieren
         for($i = 0; $i < count($Kinder); $i++){

            /*
               Einrückung anhand der Ebene generieren

               Erster Faktor beschreibt die Lage innerhalb des Baumes,
               zweiter Faktor die vertikale Verschiebung
            */
            $PrefixCount = round($this->__Ebene * 5.75 - 2,0);
            $Prefix = (string)'';
            for($k = 0; $k < $PrefixCount; $k++){
               $Prefix .= "&nbsp;";
             // end for
            }

            // Symbole hinzuladen
            if($Kinder[$i]->zeigeTyp() == 'dir'){

               if(count($Kinder[$i]->zeigeKinder()) > 0){
                  $this->__Puffer .= $Prefix.$this->__erzeugeOrdnerKnoten($Kinder[$i]);
                  $this->__erzeugeBaumAnsicht($Kinder[$i]);
                  $this->__Ebene--;  // Ebene zurücksetzen, da sonst Ausgabe falsch
                // end if
               }
               else{
                  $this->__Puffer .= $Prefix.$this->__erzeugeOrdnerKnoten($Kinder[$i]);
                // end else
               }

             // end
            }
            else{
               $this->__Puffer .= $Prefix.$this->__erzeugeDateiKnoten($Kinder[$i]);
             // end else
            }

          // end for
         }

       // end function
      }

    // end class
   }
?>