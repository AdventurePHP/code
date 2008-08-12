<?php
   import('tools::form::taglib','ui_element');
   import('tools::variablen','variablenHandler');
   import('tools::string','stringAssistant');


   /**
   *  @package modules::captcha::pres::taglib
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
            $_LOCALS = variablenHandler::registerLocal(array($this->__TextFieldName => null));

            // validate field
            if($_LOCALS[$this->__TextFieldName] != $CaptchaString){
               $this->__TextField->setAttribute('style',$this->__TextField->getAttribute('style').'; '.$this->__ValidatorStyle);
               $this->__ParentObject->set('isValid',false);
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
      */
      function transform(){

         // get url rewrite information from the registry
         $Registry = &Singleton::getInstance('Registry');
         $URLRewrite = $Registry->retrieve('apf::core','URLRewriting');

         // initialize base url
         if(isset($this->__Attributes['action_baseurl'])){
            $ActionBaseURL = $this->__Attributes['action_baseurl'];
          // end if
         }
         else{
            $ActionBaseURL = (string)'';
          // end else
         }

         // initialize captcha source
         if($URLRewrite == true){
            $CaptchaCode = '<img src="'.$ActionBaseURL.'/~/modules_captcha_biz-action/showCaptcha/name/'.$this->__TextFieldName.'" alt="CAPTCHA" align="absmiddle" ';
          // end if
         }
         else{
            $CaptchaCode = '<img src="'.$ActionBaseURL.'/?modules_captcha_biz-action:showCaptcha=name:'.$this->__TextFieldName.'" alt="CAPTCHA" align="absmiddle" ';
          // end else
         }

         // add class and style attributes if desired
         if(isset($this->__Attributes['image_class'])){
            $CaptchaCode .= 'class="'.$this->__Attributes['image_class'].'" ';
          // end if
         }
         if(isset($this->__Attributes['image_style'])){
            $CaptchaCode .= 'style="'.$this->__Attributes['image_style'].'" ';
          // end if
         }

         // concatinate the html code and return it
         return $CaptchaCode.'/> '.$this->__TextField->transform();

       // end function
      }

    // end class
   }
?>