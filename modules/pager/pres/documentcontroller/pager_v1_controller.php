<?php
   import('tools::variablen','variablenHandler');


   /**
   *  @package modules::schwarzesbrett::pres::documentcontroller::pager
   *  @class pager_v1_controller
   *
   *  Implementiert den DocumentController f�r den PagerManager. Einfacher Pager mit Anzeige von<br />
   *  Seitenzahlen.<br />
   *
   *  @author Christian Sch�fer
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
      *  Version 0.2, 26.11.2006 (Pager gibt einen Leer-String zur�ck, falls keine Seiten vorhanden)<br />
      *  Version 0.3, 03.01.2007 (PageController V2 ready)<br />
      *  Version 0.4, 11.03.2007 (Komplett auf PageController V2 migriert)<br />
      *  Version 0.5, 29.08.2007 (Anker-Name mit eingebunden)<br />
      *  Version 0.6, 02.03.2008 (Mehrsprachigkeit eingef�hrt)<br />
      */
      function transformContent(){

         // LOCALS f�llen
         $this->_LOCALS = variablenHandler::registerLocal(array($this->__Attributes['Config']['ParameterCountName'] => $this->__Attributes['Config']['EntriesPerPage']));


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

         // Anzeige sprachabh�ngig setzen
         $Template__Page = &$this->__getTemplate('Page_'.$this->__Language);
         $this->setPlaceHolder('Page',$Template__Page->transformTemplate());

         // Puffer in Seite einsetzen
         $this->setPlaceHolder('Content',$Buffer);

       // end function
      }

    // end class
   }
?>