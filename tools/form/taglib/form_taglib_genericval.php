<?php
   import('tools::form::taglib','ui_validate');


   /**
   *  @package tools::form::taglib
   *  @class form_taglib_genericval
   *
   *  Generischer Validator-Tag, mit dem der Inhalt des Tags bei falscher Validierung ausgegeben wird.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 22.09.2007<br />
   */
   class form_taglib_genericval extends ui_validate
   {


      function form_taglib_genericval(){
      }


      /**
      *  @public
      *
      *  Implementiert die Interface-Methode aus "coreObject". Validiert ein Feld und gibt bei<br/>
      *  nicht erfolgreicher Validierung den Text innerhalb des Tags aus.<br />
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 22.09.2007<br />
      */
      function onAfterAppend(){

         // Feld auslesen
         if(isset($this->__Attributes['field']) && !empty($this->__Attributes['field']) && isset($this->__Attributes['button']) && !empty($this->__Attributes['button'])){
            $Field = $this->__Attributes['field'];
            $Button = $this->__Attributes['button'];
          // end if
         }
         else{
            $Name = $this->__ParentObject->getAttribute('name');
            trigger_error('['.get_class($this).'::onAfterAppend()] Generic validator tag in form "'.$Name.'" has no or an empty button- or field- attributes!',E_USER_ERROR);
            $this->__Content = (string)'';
            $Field = null;
            $Button = null;
          // end else
         }


         // Validiere Wert, falls Button geklickt wurde
         if(isset($_REQUEST[$Button])){

            // Validierungs-Methode auslesen
            if(isset($this->__Attributes['validator']) && !empty($this->__Attributes['validator'])){
               $Validator = $this->__Attributes['validator'];
             // end if
            }
            else{
               $Validator = 'Text';
             // end else
            }

            $ValidatorMethode = 'validate'.$Validator;


            // String, der zu validieren ist, auslesen
            if(isset($_REQUEST[trim($this->__Attributes['field'])])){
               $String = $_REQUEST[trim($this->__Attributes['field'])];
             // end if
            }
            else{
               $String = (string)'';
             // end else
            }


            // Validierung durchführen
            if(myValidator::$ValidatorMethode($String) == true){
               $this->__Content = (string)'';
             // end if
            }

          // end if
         }
         else{
            $this->__Content = (string)'';
          // end else
         }

       // end function
      }

    // end class
   }
?>