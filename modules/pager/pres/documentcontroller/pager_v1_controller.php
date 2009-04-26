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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::request','RequestHandler');


   /**
   *  @namespace modules::schwarzesbrett::pres::documentcontroller::pager
   *  @class pager_v1_controller
   *
   *  Implementiert den DocumentController für den PagerManager. Einfacher Pager mit Anzeige von<br />
   *  Seitenzahlen.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   */
   class pager_v1_controller extends baseController
   {

      function pager_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode transformContent().<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 26.11.2006 (Pager gibt einen Leer-String zurück, falls keine Seiten vorhanden)<br />
      *  Version 0.3, 03.01.2007 (PageController V2 ready)<br />
      *  Version 0.4, 11.03.2007 (Komplett auf PageController V2 migriert)<br />
      *  Version 0.5, 29.08.2007 (Anker-Name mit eingebunden)<br />
      *  Version 0.6, 02.03.2008 (Mehrsprachigkeit eingeführt)<br />
      */
      function transformContent(){

         // LOCALS füllen
         $this->_LOCALS = RequestHandler::getValues(array($this->__Attributes['Config']['ParameterCountName'] => $this->__Attributes['Config']['EntriesPerPage']));


         // Puffer initialisieren
         $Buffer = (string)'';

         $count = count($this->__Attributes['Pages']);
         for($i = 0; $i < $count; $i++){

            if($this->__Attributes['Pages'][$i]->get('isSelected') == true){
               $Template__Page = &$this->__getTemplate('Page_Selected');
             // end if
            }
            else{
               $Template__Page = &$this->__getTemplate('Page_Normal');
             // end else
            }

            // Pager zusammenbauen
            if(isset($this->__Attributes['AnchorName'])){
               $Template__Page->setPlaceHolder('Link',$this->__Attributes['Pages'][$i]->get('Link').'#'.$this->__Attributes['AnchorName']);
             // end if
            }
            else{
               $Template__Page->setPlaceHolder('Link',$this->__Attributes['Pages'][$i]->get('Link'));
             // end else
            }
            $Template__Page->setPlaceHolder('Seite',$this->__Attributes['Pages'][$i]->get('Page'));

            // Aktuelle Seite ausgeben
            $Buffer .= $Template__Page->transformTemplate();

          // end for
         }

         // Anzeige sprachabhängig setzen
         $Template__Page = &$this->__getTemplate('Page_'.$this->__Language);
         $this->setPlaceHolder('Page',$Template__Page->transformTemplate());

         // Puffer in Seite einsetzen
         $this->setPlaceHolder('Content',$Buffer);

       // end function
      }

    // end class
   }
?>