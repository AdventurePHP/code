<?php
   import('modules::comments::pres::documentcontroller','commentBaseController');
   import('tools::variablen','variablenHandler');
   import('modules::comments::biz','commentManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('tools::string','stringAssistant');


   /**
   *  @package modules::comments::pres::documentcontroller
   *  @class comment_form_v1_controller
   *
   *  Implements the document controller for the 'form.html' template.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.08.2007<br />
   */
   class comment_form_v1_controller extends commentBaseController
   {

      /**
      *  @private
      *  Contains locally used variables.
      */
      var $_LOCALS = array();


      /**
      *  @public
      *
      *  Constructor of the class. Initializes the variables used in this view.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2007<br />
      *  Version 0.2, 28.12.2007 (Added the CaptchaString)<br />
      */
      function comment_form_v1_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Name','EMail','Comment','CaptchaString'));
       // end function
      }


      /**
      *  @public
      *
      *  Displays the form view.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2007<br />
      *  Version 0.2, 08.11.2007 (Implemented multi language support)<br />
      *  Version 0.3, 28.12.2007 (Added a captcha)<br />
      */
      function transformContent(){

         // Referenz auf das Formular holen
         $Form__AddComment = &$this->__getForm('AddComment');

         // Prüfen, ob Formular abgesendet und erforlgreich validiert wurde
         if($Form__AddComment->get('isSent') == true){

            // Kategorie-Schlüssel laden
            $this->__loadCategoryKey();

            // Mapper holen
            $M = &$this->__getAndInitServiceObject('modules::comments::biz','commentManager',$this->__CategoryKey);

            // Validieren des Captchas
            $CaptchaString = $M->get('CaptchaString');

            if($CaptchaString != $this->_LOCALS['CaptchaString']){
               $Captcha = &$Form__AddComment->getFormElementByName('CaptchaString');
               $Captcha->set('isValid',false);
               $Form__AddComment->set('isValid',false);
             // end if
            }

            // Prüfen, ob Formular korrekt ausgefüllt wurde
            if($Form__AddComment->get('isValid') == true){

               // Eintrag erstellen
               $ArticleComment = new ArticleComment();
               $ArticleComment->set('Name',$this->_LOCALS['Name']);
               $ArticleComment->set('EMail',$this->_LOCALS['EMail']);
               $ArticleComment->set('Comment',$this->_LOCALS['Comment']);

               // Eintrag speichern
               $M->saveEntry($ArticleComment);

             // end if
            }
            else{
               $this->__buildForm();
             // end else
            }

          // end if
         }
         else{
            $this->__buildForm();
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Generates the comment form.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      *  Version 0.2, 09.10.2008 (Changed captcha image url generation)<br />
      */
      function __buildForm(){

         // Referenz auf das Formular holen
         $Form__AddComment = &$this->__getForm('AddComment');

         // action setzen
         $Form__AddComment->setAttribute('action',$_SERVER['REQUEST_URI'].'#comments');

         // Button beschriften
         $Config = &$this->__getConfiguration('modules::comments','language');
         $Button = &$Form__AddComment->getFormElementByName('Save');
         $Button->setAttribute('value',$Config->getValue($this->__Language,'form.button'));

         // CaptchaImage füllen
         $Reg = &Singleton::getInstance('Registry');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
         if($URLRewriting === true){
            $Form__AddComment->setPlaceHolder('CaptchaImage','/~/modules_comments-action/showCaptcha');
          // end if
         }
         else{
            $Form__AddComment->setPlaceHolder('CaptchaImage','./?modules_comments-action:showCaptcha');
          // end else
         }

         // Formular darstellen
         $this->setPlaceHolder('Form',$Form__AddComment->transformForm());

         // Zurücklink darstellen
         $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('coview' => 'listing'));
         $this->setPlaceHolder('Zurueck',$Link);

       // end function
      }

    // end class
   }
?>