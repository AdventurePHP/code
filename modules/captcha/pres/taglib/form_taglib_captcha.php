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

   import('tools::form::taglib','ui_element');
   import('tools::request','RequestHandler');
   import('tools::string','stringAssistant');
   import('core::session','sessionManager');
   import('tools::link','frontcontrollerLinkHandler');


   /**
   *  @namespace modules::captcha::pres::taglib
   *  @module form_taglib_captcha
   *
   *  Implements a CAPTCHA-Taglib to extend a form's features. Inherits from ui_element in order to
   *  be a represent qualified form element.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 16.07.2008<br />
   *  Version 0.2, 20.07.2008 (Moved in a separate module folder to deliver it with the framework release)<br />
   */
   class form_taglib_captcha extends ui_element
   {

      /**
      *  @private
      *  Contains the instance of the captcha text field.
      */
      var $__TextField = null;


      /**
      *  @private
      *  Contains the name of the captcha text field.
      */
      var $__TextFieldName = null;


      function form_taglib_captcha(){
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
      function onAfterAppend(){

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
         if(isset($this->__Attributes['validate'])){
            $this->__TextField->setAttribute('validate',$this->__Attributes['validate']);
          // end if
         }
         if(isset($this->__Attributes['button'])){
            $this->__TextField->setAttribute('button',$this->__Attributes['button']);
          // end if
         }
         $this->__TextFieldName = md5($this->__ParentObject->getAttribute('name').'_captcha');
         $this->__TextField->setAttribute('name',$this->__TextFieldName);
         $this->__TextField->setAttribute('maxlength','5');

         // apply the onParseTime method
         $this->__TextField->onParseTime();

         // apply the onAfterAppend method
         $this->__TextField->setByReference('ParentObject',$this->__ParentObject);
         $this->__TextField->onAfterAppend();

         // get the captcha string from session
         $sessMgr = new sessionManager('modules::captcha');
         $CaptchaString = $sessMgr->loadSessionData($this->__TextFieldName);
         $sessMgr->saveSessionData($this->__TextFieldName,stringAssistant::generateCaptchaString(5));

         // validate the captcha field and input if desired
         if($this->__ParentObject->get('isSent') == true && isset($this->__Attributes['validate']) && $this->__Attributes['validate'] == 'true'){

            // register field name from the request
            $_LOCALS = RequestHandler::getValues(array($this->__TextFieldName => null));

            // validate field
            if($_LOCALS[$this->__TextFieldName] != $CaptchaString){
               $this->__TextField->setAttribute('style',$this->__TextField->getAttribute('style').'; '.$this->__ValidatorStyle);
               $this->__ParentObject->set('isValid',false);

               // clear captcha field, if desired
               if($this->getAttribute('clearonerror') === 'true'){
                  $this->__TextField->setAttribute('value','');
                // end if
               }

             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Generate and return HTML code of the captcha tag.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.06.2008<br />
      *  Version 0.2, 05.11.2008 (Changed action base url generation)<br />
      *  Version 0.3, 07.11.2008 (Fixed the action URL generation. See class ui_mediastream for more details.)<br />
      */
      function transform(){

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
         $actionURL = frontcontrollerLinkHandler::generateLink($actionurl,$actionParam);

         // initialize captcha source
         $captchaCode = '<img src="'.$actionURL.'" alt="CAPTCHA" align="absmiddle" ';

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
         return $captchaCode.'/> '.$this->__TextField->transform();

       // end function
      }

    // end class
   }
?>
