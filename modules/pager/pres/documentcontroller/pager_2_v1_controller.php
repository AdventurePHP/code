<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::link','frontcontrollerLinkHandler');
   import('tools::request','RequestHandler');


   /**
   *  @namespace modules::schwarzesbrett::pres::documentcontroller::pager
   *  @class pager_2_v1_controller
   *
   *  Implementiert den DocumentController für den PagerManager. Folgende Features sind enthalten<br />
   *  <br />
   *    - Anzeige der Seiten<br />
   *    - Vor- & Zurück-Button<br />
   *    - Dynamisches Wählen der Anzahl der Einträge pro Seite<br />
   *  <br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   *  Version 0.2, 26.11.2006 (Pager gibt einen Leer-String zurück, falls keine Seiten vorhanden)<br />
   *  Version 0.3, 03.01.2007 (PageController V2 ready)<br />
   *  Version 0.4, 11.03.2007 (Komplett auf PageController V2 migriert)<br />
   *  Version 0.5, 16.11.2007 (Auf frontcontrollerlinkHandler umgestellt)<br />
   */
   class pager_2_v1_controller extends baseController
   {

      /**
      *  @private
      *  List of local variables.
      */
      private $_LOCALS;


      function pager_2_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode 'transformContent'.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 26.11.2006 (Pager gibt einen Leer-String zurück, falls keine Seiten vorhanden)<br />
      *  Version 0.3, 03.01.2007 (PageController V2 ready)<br />
      *  Version 0.4, 11.03.2007 (Komplett auf PageController V2 migriert)<br />
      *  Version 0.5, 29.08.2007 (Anker-Name mit eingebunden)<br />
      *  Version 0.6, 16.11.2007 (Auf frontcontrollerlinkHandler umgestellt)<br />
      *  Version 0.7, 02.03.2008 (Mehrsprachigkeit der Beschriftung eingeführt)<br />
      */
      function transformContent(){

         // LOCALS füllen
         $this->_LOCALS = RequestHandler::getValues(array($this->__Attributes['Config']['ParameterCountName'] => $this->__Attributes['Config']['EntriesPerPage']));

         // Pager leer zurückgeben, falls keine Seiten vorhanden sind.
         if(count($this->__Attributes['Pages']) == 0){

            // Content des aktuellen Designs leeren
            $this->__Content = '';

            // Funktion verlassen
            return '';

          // end if
         }

         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('Pager');

         // Anzahl der Seiten generieren
         $PageCount = (int) 0;

         // Aktuelle Seite generieren
         $CurrentPage = (int) 0;

         // Anzahl der Einträge
         $EntriesCount = (int) 0;

         // Puffer initialisieren
         $Buffer = (string)'';

         for($i = 0; $i < count($this->__Attributes['Pages']); $i++){

            if($this->__Attributes['Pages'][$i]->get('isSelected') == true){

               // Referenz auf Template holen
               $Template = &$this->__getTemplate('Page_Selected');

               // Aktuelle Page auslesen
               $CurrentPage = $this->__Attributes['Pages'][$i]->get('Page');

             // end if
            }
            else{

               // Referenz auf Template holen
               $Template = &$this->__getTemplate('Page');

             // end else
            }

            // Pager zusammenbauen
            if(isset($this->__Attributes['AnchorName'])){
               $Template->setPlaceHolder('Link',$this->__Attributes['Pages'][$i]->get('Link').'#'.$this->__Attributes['AnchorName']);
             // end if
            }
            else{
               $Template->setPlaceHolder('Link',$this->__Attributes['Pages'][$i]->get('Link'));
             // end else
            }
            $Template->setPlaceHolder('Seite',$this->__Attributes['Pages'][$i]->get('Page'));

            // Template transformieren
            $Buffer .= $Template->transformTemplate();

            // Anzahl der Seiten setzen
            $PageCount = $this->__Attributes['Pages'][$i]->get('pageCount');

            // Anzahl der Datensätze setzen
            $EntriesCount = $this->__Attributes['Pages'][$i]->get('entriesCount');

          // end for
         }

         // Puffer in Inhalt einsetzen
         $this->setPlaceHolder('Inhalt',$Buffer);


         // VorherigeSeite
         if($CurrentPage > 1){

            // Werte berechnen
            $Page = $CurrentPage - 1;
            $EntriesPerPage = $this->_LOCALS[$this->__Attributes['Config']['ParameterCountName']];
            $Start = ($Page * $EntriesPerPage) - $EntriesPerPage;

            // Link generieren
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__Attributes['Config']['ParameterStartName'] => $Start));


            // Template vorherige Seite ausgeben
            $Template__VorherigeSeite_Aktiv = & $this->__getTemplate('VorherigeSeite_Aktiv');


            if(isset($this->__Attributes['AnchorName'])){
               $Template__VorherigeSeite_Aktiv->setPlaceHolder('Link',$Link.'#'.$this->__Attributes['AnchorName']);
             // end if
            }
            else{
               $Template__VorherigeSeite_Aktiv->setPlaceHolder('Link',$Link);
             // end else
            }
            $this->setPlaceHolder('VorherigeSeite',$Template__VorherigeSeite_Aktiv->transformTemplate());

          // end if
         }
         else{

            // Template vorherige Seite (inaktiv) ausgeben
            $Template__VorherigeSeite = & $this->__getTemplate('VorherigeSeite_Inaktiv');
            $this->setPlaceHolder('VorherigeSeite',$Template__VorherigeSeite->transformTemplate());

          // end else
         }


         // NaechsteSeite
         if($CurrentPage < $PageCount){

            // Werte berechnen
            $Page = $CurrentPage + 1;
            $EntriesPerPage = $this->_LOCALS[$this->__Attributes['Config']['ParameterCountName']];
            $Start = ($Page * $EntriesPerPage) - $EntriesPerPage;

            // Link generieren
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__Attributes['Config']['ParameterStartName'] => $Start));

            $Template__NaechsteSeite_Aktiv = & $this->__getTemplate('NaechsteSeite_Aktiv');

            if(isset($this->__Attributes['AnchorName'])){
               $Template__NaechsteSeite_Aktiv->setPlaceHolder('Link',$Link.'#'.$this->__Attributes['AnchorName']);
             // end if
            }
            else{
               $Template__NaechsteSeite_Aktiv->setPlaceHolder('Link',$Link);
             // end else
            }

            $this->setPlaceHolder('NaechsteSeite',$Template__NaechsteSeite_Aktiv->transformTemplate());

          // end if
         }
         else{

            $Template__NaechsteSeite_Inaktiv = & $this->__getTemplate('NaechsteSeite_Inaktiv');
            $this->setPlaceHolder('NaechsteSeite',$Template__NaechsteSeite_Inaktiv->transformTemplate());

          // end else
         }


         // Einträge / Seite
         $EntriesPerPage = array(5,10,15,20);
         $Buffer = (string)'';

         foreach($EntriesPerPage as $Key => $Value){

            if($this->_LOCALS[$this->__Attributes['Config']['ParameterCountName']] == $Value){
               $Template = &$this->__getTemplate('EntriesPerPage_Aktiv');
             // end if
            }
            else{
               $Template = & $this->__getTemplate('EntriesPerPage_Inaktiv');
             // end else
            }

            // Link generieren
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__Attributes['Config']['ParameterStartName'] => '0',$this->__Attributes['Config']['ParameterCountName'] => $Value));

            if(isset($this->__Attributes['AnchorName'])){
               $Template->setPlaceHolder('Link',$Link.'#'.$this->__Attributes['AnchorName']);
             // end if
            }
            else{
               $Template->setPlaceHolder('Link',$Link);
             // end else
            }

            // Anzahl einsetzen
            $Template->setPlaceHolder('Count',$Value);

            // Template in Puffer einsetzen
            $Buffer .= $Template->transformTemplate();

          // end foreach
         }

         $this->setPlaceHolder('EntriesPerPage',$Buffer);

         // Beschriftung für Einträge/Seite einfügen
         $Template__EntriesPerPage = &$this->__getTemplate('EntriesPerPage_'.$this->__Language);
         $this->setPlaceHolder('EntriesPerPage_Display',$Template__EntriesPerPage->transformTemplate());

         // Timer stoppen
         $T->stop('Pager');

       // end function
      }

    // end class
   }
?>