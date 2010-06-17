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
    
   /**
    * @package tools::form::taglib
    * @class form_element_observer
    * 
    * Implements a base class for the <em>form:addfilter</em> and
    * <em>form:addvalidator</em> taglibs. Constructs a form control
    * observer (filter or validator) using the tag attributes.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2009<br />
    */
   class form_control_observer extends form_control {

      /**
       * @since 1.12
       * @var string The name of the attribute, that specifies the validator's type.
       */
      private static $TYPE_ATTRIBUTE_NAME = 'type';

      /**
       * Overwrite the parent's onParseTime() method to not
       * initiate presetting.
       */
      public function onParseTime(){
      }

      /**
       * @public
       *
       * Overwrites the transform() method to generate empty output.
       *
       * @return string Empty string, because the tag generates no output.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.08.2009<br />
       */
      public function transform(){
         return (string)'';
       // end function
      }

      /**
       * @protected
       *
       * Constructs the desired form control observer using tag attributes.
       *
       * @@param string $injectionMethod The name of the method to inject the observer with.
       * @return AbstractFormValidator The form control observer.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.08.2009<br />
       */
      protected function __addObserver($injectionMethod){

         // validate the required attributes
         $controlDef = $this->getAttribute('control');
         $buttonName = $this->getAttribute('button');
         $namespace = $this->getAttribute('namespace');
         $class = $this->getAttribute('class');

         if(empty($controlDef) || empty($buttonName) || empty($class) || empty($namespace)){
            $formName = $this->__ParentObject->getAttribute('name');
            throw new FormException('['.get_class($this).'::onAfterAppend()] Required attribute '
               .'"control", "button", "class" or "namespace" missing. Please review your '
               .'&lt;form:addvalidator /&gt; or &lt;form:addfilter /&gt; taglib definition in form "'
                    .$formName.'"!');
         }

         // handle multiple controls, that are separated by pipe to make form definition easier.
         $controlNames = explode('|',$controlDef);

         foreach($controlNames as $controlName){

            // sanitize control name to avoid errors while addressing a control!
            $controlName = trim($controlName);

            // retrieve elements to pass to the validator and validate them
            $control = &$this->__ParentObject->getFormElementByName($controlName);
            $button = &$this->__ParentObject->getFormElementByName($buttonName);
            $type = $this->getAttribute(self::$TYPE_ATTRIBUTE_NAME);

            if($control === null || $button === null){
               $formName = $this->__ParentObject->getAttribute('name');
               throw new FormException('['.get_class($this).'::onAfterAppend()] The form with name '
                  .'"'.$formName.'" does not contain a control with name "'.$controlName.'" or '
                  .'a button with name "'.$buttonName.'". Please check your taglib definition!');
            }

            // include observer class
            import($namespace,$class);

            // Construct observer injecting the control to validate and the button,
            // that triggers the event. As of 1.12, the type is presented to the
            // validator to enable special listener notification.
            $observer = new $class($control,$button,$type);
            $observer->setContext($this->__Context);
            $observer->setLanguage($this->__Language);

            // inject the observer into the form control
            $control->{$injectionMethod}($observer);

          // end foreach
         }

        // end function
       }
   
    // end class
   }
?>