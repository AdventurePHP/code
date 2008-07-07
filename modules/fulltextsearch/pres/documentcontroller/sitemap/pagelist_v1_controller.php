<?php
   import('modules::fulltextsearch::biz','fulltextsearchManager');


   /**
   *  @package sites::demosite::pres::documentcontroller
   *  @module website_v1_controller
   *
   *  Implementiert den DocumentController für das Design 'pagelist.html'.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 24.03.2008<br />
   */
   class pagelist_v1_controller extends baseController
   {

      /**
      *  @private
      *  Liste der Hosts, auf dem die Applikation ausgeführt werden darf.
      */
      var $__AllowedServers = array(
                                    'dev.adventure-php-framework.org',
                                    'stage.adventure-php-framework.org',
                                    'www.adventure-php-framework.org',
                                    'adventure-php-framework.org'
                                   );


      function pagelist_v1_controller(){
      }


      /**
      *  @module transformContent
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent".<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.03.2008<br />
      */
      function transformContent(){

         ///////////////////////////////////////////////////////////////////////////////////////////////
         // Quickhack, dass die Suche-Funktion nicht auf lokalen Installationen, oder anderen         //
         // Maschinen funktioniert. Dies wurde eingeführt, damit bei Auslieferung des Demo-Packages   //
         // die Dokumentations-Seiten funktionieren. Bei Einsatz der Applikation auf einem Produktiv- //
         // System müssen die folgenden Zeilen (if-Konstrukt)auskommentiert werden.                   //
         ///////////////////////////////////////////////////////////////////////////////////////////////
         if(!isset($_SERVER['SERVER_NAME']) || !in_array($_SERVER['SERVER_NAME'],$this->__AllowedServers)){
            $Template__Deactivated = &$this->__getTemplate('Deactivated_'.$this->__Language);
            $this->setPlaceHolder('Result',$Template__Deactivated->transformTemplate());
            return true;
          // end if
         }
         ///////////////////////////////////////////////////////////////////////////////////////////////

         // Manager holen
         $M = &$this->__getServiceObject('modules::fulltextsearch::biz','fulltextsearchManager');

         // Seiten laden
         $Pages = $M->loadPages();

         // Seiten ausgeben
         $count = count($Pages);
         $Buffer = (string)'';
         $Template__Page = &$this->__getTemplate('Page');

         for($i = 0; $i < $count; $i++){

            $Template__Page->setPlaceHolder('Title',$Pages[$i]->get('Title'));
            $Template__Page->setPlaceHolder('Name',$Pages[$i]->get('Name'));
            $Template__Page->setPlaceHolder('LastMod',$Pages[$i]->get('LastMod'));
            $Buffer .= $Template__Page->transformTemplate();

          // end for
         }

         // Liste in Content einsetzen
         $this->setPlaceHolder('PageList',$Buffer);

       // end function
      }

    // end class
   }
?>