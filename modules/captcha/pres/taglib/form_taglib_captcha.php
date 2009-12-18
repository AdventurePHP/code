<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::form::taglib','form_control');
   import('tools::request','RequestHandler');
   import('tools::string','stringAssistant');
   import('core::session','SessionManager');
   import('tools::link','FrontcontrollerLinkHandler');

   /**
    * @namespace modules::captcha::pres::taglib
    * @module form_taglib_captcha
    *
    * Implements a CAPTCHA-Taglib to extend a form's features. Inherits from form_control
    * in order to be a fully qualified form element.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.07.2008<br />
    * Version 0.2, 20.07.2008 (Moved in a separate module folder to deliver it with the framework release)<br />
    */
   class form_taglib_captcha extends form_control {

      /**
      *  @protected
      *  Contains the instance of the captcha text field.
      */
      protected $__TextField = null;

      /**
      *  @protected
      *  Contains the name of the captcha text field.
      */
      protected $__TextFieldName = null;

      /**
       * @var string The captcha string of the current request.
       */
      protected $__CaptchaString = null;

      public function form_taglib_captcha(){
      }

      /**
       * @public
       * 
       * Re-implements the addValidator() method for the captcha taglib.
       * Can be used to add the shipped validator or a custom one
       * directly within the form definition.
       *
       * @param AbstractFormValidator $validator The desired validator.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function addValidator(AbstractFormValidator &$validator){
         $this->__TextField->addValidator($validator);
       // end function
      }

      /**
       * @public
       * @since 1.11
       *
       * Re-implements the addFiler() method for the captcha taglib.
       * Can be used to add the shipped filter or a custom one
       * directly within the form definition.
       *
       * @param AbstractFormFilter $filter The desired filter.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function addFilter(AbstractFormFilter &$filter){
         $this->__TextField->addFilter($filter);
       // end function
      }

      /**
       * @pubic
       *
       * Re-implements the isValid() method for this special case.
       *
       * @return boolean The validation status of the inner text field.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function isValid(){
         return $this->__TextField->isValid();
       // end function
      }

      /**
      *  @public
      *
      *  Implements the onAfterAppend method from the ui_element class.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.06.2008<br />
      *  Version 0.2, 10.11.2008 (Added the "clearonerror" attribute. If set to "true", the field is cleared on error.)<br />
      */
      public function onParseTime(){

         // create text field
         $this->__TextField = new form_taglib_text();
         $this->__TextField->set('ObjectID',xmlParser::generateUniqID());

         // prepare the text field
         if(isset($this->__Attributes['text_class'])){
            $this->__TextField->setAttribute('class',$this->__Attributes['text_class']);
          // end if
         }
         if(isset($this->__Attributes['text_style'])){
            $this->__TextField->setAttribute('style',$this->__Attributes['text_style']);
          // end if
         }

         $this->__TextFieldName = md5($this->__ParentObject->getAttribute('name').'_captcha');
         $this->__TextField->setAttribute('name',$this->__TextFieldName);
         $this->__TextField->setAttribute('maxlength','5');

         // apply the onParseTime method to guarantee native APF environment
         $this->__TextField->set('Language',$this->__Language);
         $this->__TextField->set('Context',$this->__Context);
         $this->__TextField->onParseTime();

         // apply the onAfterAppend method to guarantee native APF environment
         $this->__TextField->setByReference('ParentObject',$this->__ParentObject);
         $this->__TextField->onAfterAppend();

         // get the captcha string from session
         $sessMgr = new SessionManager('modules::captcha');
         $this->__CaptchaString = $sessMgr->loadSessionData($this->__TextFieldName);
         $sessMgr->saveSessionData($this->__TextFieldName,stringAssistant::generateCaptchaString(5));

       // end function
      }

      /**
       * @public
       *
       * Returns the current captcha string to be able to validate it
       * against the user input.
       * 
       * @return string The current captcha string.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function getCurrentCaptcha(){
         return $this->__CaptchaString;
      }

      /**
       * @public
       *
       * Returns a reference on the captcha control's text field.
       *
       * @return form_taglib_text The captcha's text field.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function &getCaptchaTextField(){
         return $this->__TextField;
      }

      /**
       * @public
       *
       * Generate and return HTML code of the captcha tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.06.2008<br />
       * Version 0.2, 05.11.2008 (Changed action base url generation)<br />
       * Version 0.3, 07.11.2008 (Fixed the action URL generation. See class ui_mediastream for more details.)<br />
       */
      public function transform(){

         // get url rewrite information from the registry
         $reg = &Singleton::getInstance('Registry');
         $urlrewrite = $reg->retrieve('apf::core','URLRewriting');
         $actionurl = $reg->retrieve('apf::core','CurrentRequestURL');

         // build action statement
         if($urlrewrite === true){
            $actionParam = array(
                                 'modules_captcha_biz-action/showCaptcha' => 'name/'.$this->__TextFieldName
                                );
          // end if
         }
         else{
            $actionParam = array(
                                 'modules_captcha_biz-action:showCaptcha' => 'name:'.$this->__TextFieldName
                                );
          // end else
         }

         // create desired media url
         $actionURL = FrontcontrollerLinkHandler::generateLink($actionurl,$actionParam);

         // initialize captcha source
         $captchaCode = '<div class="captcha"><img src="'.$actionURL.'" alt="CAPTCHA" style="float:left;" ';

         // add class and style attributes if desired
         if(isset($this->__Attributes['image_class'])){
            $captchaCode .= 'class="'.$this->__Attributes['image_class'].'" ';
          // end if
         }
         if(isset($this->__Attributes['image_style'])){
            $captchaCode .= 'style="'.$this->__Attributes['image_style'].'" ';
          // end if
         }

         // concatinate the html code and return it
         return $captchaCode.'/> <div style="line-height: 40px; float: left; margin-left: 20px;">'
            .$this->__TextField->transform().'</div><br style="clear: left;" /></div>';

       // end function
      }

    // end class
   }
?>