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

   import('tools::form::validator','AbstractFormValidator');

   /**
    * @package tools::form::validator
    * @class TextFieldValidator
    * @abstract
    *
    * Implements a base class for all text field validators.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   abstract class TextFieldValidator extends AbstractFormValidator {

      /**
       * @public
       *
       * Notifies the form control to be invalid.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function notify(){
         $this->__Control->markAsInvalid();
         $this->markControl($this->__Control);
         $this->__Control->notifyValidationListeners();
       // end function
      }

      /**
       * @protected
       *
       * Marks a form control als invalid using a css class. See
       * http://wiki.adventure-php-framework.org/de/Weiterentwicklung_Formular-Validierung
       * for details.
       *
       * @param form_control $control The control to mark as invalid.
       *
       * @since 1.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.02.2010<br />
       */
      protected function markControl(&$control){
         $marker = $this->getCssMarkerClass($control);
         $this->appendCssClass($control,$marker);
         $control->deleteAttribute(self::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
       // end function
      }

      /**
       * @protected
       *
       * Evaluates the css class used to mark an invalid form control.
       *
       * @param form_control $control The control to validate.
       * @return string The css marker class for validation notification.
       *
       * @since 1.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.02.2010<br />
       */
      protected function getCssMarkerClass(&$control){
         $marker = $control->getAttribute(self::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
         if(empty($marker)){
            $marker = self::$DEFAULT_MARKER_CLASS;
         }
         return $marker;
       // end function
      }

      /**
       * @protected
       *
       * Savely appends a css class. Resolves missing attribute.
       *
       * @param form_control $control The control to mark as invalid.
       * @param string $class The css class to append.
       *
       * @since 1.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.02.2010<br />
       */
      protected function appendCssClass(&$control,$class){
         
         $attr = $control->getAttribute('class');

         // initialize empty attribute
         if(empty($attr)){
            $attr = $class;
         }
         else {
            $attr .= ' '.$class;
         }
         $control->setAttribute('class',$attr);

       // end function
      }

    // end function
   }
?>