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
   import('modules::comments::biz','commentManager');
   import('tools::link','frontcontrollerLinkHandler');

   /**
    * @namespace modules::comments::pres::documentcontroller
    * @class comment_form_v1_controller
    *
    * Implements the document controller for the 'form.html' template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.08.2007<br />
    */
   class comment_form_v1_controller extends commentBaseController {

      /**
       * @public
       *
       * Displays and handles the form view.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.08.2007<br />
       * Version 0.2, 08.11.2007 (Implemented multi-language support)<br />
       * Version 0.3, 28.12.2007 (Added a captcha)<br />
       * Version 0.4, 13.06.2009 (Removed the captcha handling, introduced the captcha module)<br />
       */
      function transformContent(){

         $form = &$this->__getForm('AddComment');

         if($form->get('isSent') == true){

            $this->__loadCategoryKey();
            $M = &$this->__getAndInitServiceObject('modules::comments::biz','commentManager',$this->__CategoryKey);

            if($form->get('isValid') == true){

               $articleComment = new ArticleComment();
               $name = &$form->getFormElementByName('Name');
               $articleComment->set('Name',$name->getAttribute('value'));

               $email = &$form->getFormElementByName('EMail');
               $articleComment->set('EMail',$email->getAttribute('value'));

               $comment = &$form->getFormElementByName('Comment');
               $articleComment->set('Comment',$comment->get('Content'));

               $M->saveEntry($articleComment);

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
       * @private
       *
       * Generates the comment form.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2008<br />
       * Version 0.2, 09.10.2008 (Changed captcha image url generation)<br />
       * Version 0.3, 13.06.2009 (Removed the captcha handling, introduced the captcha module)<br />
       */
      private function __buildForm(){

         $form = &$this->__getForm('AddComment');
         $form->setAttribute('action',$_SERVER['REQUEST_URI'].'#comments');

         $config = &$this->__getConfiguration('modules::comments','language');
         $button = &$form->getFormElementByName('Save');
         $button->setAttribute('value',$config->getValue($this->__Language,'form.button'));

         $form->transformOnPlace();

         $link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('coview' => 'listing'));
         $this->setPlaceHolder('back',$link);

       // end function
      }

    // end class
   }
?>