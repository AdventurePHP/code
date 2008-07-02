<?php
   /**
   *  @package tools::html::taglib::actions
   *  @module ChangeLanguageAction
   *
   *  Action für das Datenbank-Backup.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 29.09.2007<br />
   */
   class ChangeLanguageAction extends AbstractFrontcontrollerAction
   {

      function ChangeLanguageAction(){
      }


      /**
      *  @module run()
      *  @public
      *
      *  Implementiert die Interface-Methode "run()" der AbstractFrontcontrollerAction.<br />
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 29.09.2007<br />
      */
      function run(){

         // Sprache des Input-Objektes holen
         $Language = $this->__Input->getAttribute('Language');

         // Sprache des Frontcontrollers setzen
         $this->__ParentObject->set('Language',$Language);

       // end function
      }

    // end class
   }
?>