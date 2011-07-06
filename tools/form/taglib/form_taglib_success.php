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

   import('tools::form::taglib','success_taglib_placeholder');
   import('tools::form::taglib','success_taglib_getstring');
   import('tools::form::taglib','success_taglib_addtaglib');

   /**
    * @package tools::form::taglib
    * @class form_taglib_success
    *
    * Implements a taglib, that outputs it's content, in case the form, the
    * tag is defined in, is sent *and* valid. This let's you easily define
    * form success messages. The definition of the tag is as follows:
    * <pre>
    * &lt;form:success&gt;
    *   The content to display, in case the form is sent *and* valid!
    *   [&lt;success:getstring namespace="" config="" key="" /&gt;]
    *   [&lt;success:placeholder name="" /&gt;]
    *   [&lt;success:addtaglib namespace="" prefix="" class="" /&gt;]
    * &lt;/form:success&gt;
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.12.2009<br />
    */
   class form_taglib_success extends form_control {

      public function __construct() {
         $this->__TagLibs[] = new TagLib('tools::form::taglib', 'success', 'placeholder');
         $this->__TagLibs[] = new TagLib('tools::form::taglib', 'success', 'getstring');
         $this->__TagLibs[] = new TagLib('tools::form::taglib', 'success', 'addtaglib');
      }

      /**
       * @public
       *
       * Overwrites the parent's method, because there is nothing to do except
       * analyzing the child tags.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.12.2009<br />
       */
      public function onParseTime(){
         $this->__extractTagLibTags();
      }

      /**
       * @public
       *
       * Overwrites the parent's method, because there is nothing to do here.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.12.2009<br />
       */
      public function onAfterAppend(){
      }

      /**
       * @public
       *
       * Outputs the content of the tag, if the form, the tag is
       * defined in is sent *and* valid!
       *
       * @return string The content of the tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.12.2009<br />
       */
      public function transform(){
         if($this->getParentObject()->isSent() && $this->getParentObject()->isValid()){
            foreach($this->__Children as $objectId => $DUMMY){
               $this->__Content = str_replace(
                  '<'.$objectId.' />',$this->__Children[$objectId]->transform(),$this->__Content
               );
            }
            return $this->__Content;
         }
         return (string)'';
      }

   }
?>