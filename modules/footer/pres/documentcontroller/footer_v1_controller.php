<?php
   import('tools::variablen','variablenHandler');
   import('tools::link','frontcontrollerLinkHandler');


   /**
   *  @package modules::footer::pres
   *  @module footer_v1_controller
   *
   *  Implementiert den DocumentController des Stylesheets 'footer.html'<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.12.2006<br />
   *  Version 0.2, 18.12.2006<br />
   *  Version 0.3, 28.10.2007 (Konfiguration durch Tag-Attribute ersetzt, __implodeRequest() entfernt)<br />
   */
   class footer_v1_controller extends baseController
   {

      function footer_v1_controller(){
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent()" des baseControllers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.12.2006<br />
      *  Version 0.2, 18.12.2006<br />
      *  Version 0.3, 04.03.2007 (Auf ConfigurationManager umgestellt)<br />
      *  Version 0.4, 28.10.2007 (Konfiguration durch Tag-Attribute ersetzt)<br />
      */
      function transformContent(){

         // Drucken-Link generieren
         $ParamsArray = array_merge(array('perspective' => 'print'),$_REQUEST);
         $PrintURL = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],$ParamsArray);
         $this->setPlaceHolder('DruckenLink',$PrintURL);


         // Weiterempfehlen-Link generieren
         $TargetPage = $this->__Document->getAttribute('targetpage');

         if($TargetPage == null){
            trigger_error('[footer_v1_controller::transformContent()] No attribute "targetpage" defined!');
          // end if
         }


         // DefaultPage aus den Tag-Parametern auslesen
         $DefaultPage = $this->__Document->getAttribute('defaultpage');

         if($DefaultPage == null){
            trigger_error('[footer_v1_controller::transformContent()] No attribute "defaultpage" defined!');
          // end if
         }


         // Weiterempfehlen-Link nur ausgeben, wenn targetpage gefüllt
         if($TargetPage != null){

            // Aktuelle Seite auslesen
            $_LOCALS = variablenHandler::registerLocal(array('Seite' => $DefaultPage));

            // Link zusammenfügen
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('Seite' => $TargetPage,'Weiterempfehlen' => $_LOCALS['Seite']));
            $this->setPlaceHolder('EMailLink',$Link);

          // end if
         }
         else{
            $this->setPlaceHolder('EMailLink','#');
          // end else
         }

       // end function
      }

    // end class
   }
?>