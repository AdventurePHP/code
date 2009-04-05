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

   import('modules::comments::pres::documentcontroller','commentBaseController');
   import('tools::request','RequestHandler');
   import('modules::comments::biz','commentManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('tools::string','stringAssistant');


   /**
   *  @namespace modules::comments::pres::documentcontroller
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
      *  @protected
      *  Contains locally used variables.
      */
      protected $_LOCALS = array();


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
         $this->_LOCALS = RequestHandler::getValues(array('Name','EMail','Comment','CaptchaString'));
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

         // Pr�fen, ob Formular abgesendet und erforlgreich validiert wurde
         if($Form__AddComment->get('isSent') == true){

            // Kategorie-Schl�ssel laden
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

            // Pr�fen, ob Formular korrekt ausgef�llt wurde
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
      private function __buildForm(){

         // Referenz auf das Formular holen
         $Form__AddComment = &$this->__getForm('AddComment');

         // action setzen
         $Form__AddComment->setAttribute('action',$_SERVER['REQUEST_URI'].'#comments');

         // Button beschriften
         $Config = &$this->__getConfiguration('modules::comments','language');
         $Button = &$Form__AddComment->getFormElementByName('Save');
         $Button->setAttribute('value',$Config->getValue($this->__Language,'form.button'));

         // CaptchaImage f�llen
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

         // Zur�cklink darstellen
         $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('coview' => 'listing'));
         $this->setPlaceHolder('Zurueck',$Link);

       // end function
      }

    // end class
   }
?>