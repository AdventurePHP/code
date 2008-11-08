<?php
   import('modules::fulltextsearch::biz','fulltextsearchManager');
   import('tools::link','linkHandler');


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
      *  Version 0.2, 31.08.2008 (Removed server quick hack)<br />
      *  Version 0.3, 31.08.2008 (Changed link generation)<br />
      */
      function transformContent(){

         // Manager holen
         $M = &$this->__getServiceObject('modules::fulltextsearch::biz','fulltextsearchManager');

         // Seiten laden
         $Pages = $M->loadPages();

         // PageIndicator definieren
         if($this->__Language == 'de'){
            $PageIndicator = 'Seite';
          // end if
         }
         else{
            $PageIndicator = 'Page';
          // end else
         }

         // Seiten ausgeben
         $count = count($Pages);
         $Buffer = (string)'';
         $Template__Page = &$this->__getTemplate('Page');

         for($i = 0; $i < $count; $i++){

            // set page title
            $Template__Page->setPlaceHolder('Title',utf8_encode($Pages[$i]->get('Title')));

            // build link
            $URLName = $Pages[$i]->get('URLName');
            $PageID = $Pages[$i]->get('PageID');
            $Template__Page->setPlaceHolder('Link',linkHandler::generateLink('',array($PageIndicator => $PageID.'-'.$URLName)));

            // set last mod
            $Template__Page->setPlaceHolder('LastMod',$Pages[$i]->get('LastMod'));

            // display current page
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