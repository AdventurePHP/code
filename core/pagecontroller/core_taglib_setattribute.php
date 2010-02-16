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
    * @package core::pagecontroller
    * @class core_taglib_setattribute
    *
    * Provides the facility to set a document's attributes within a template file.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 04.04.2007<br />
    */
   class core_taglib_setattribute extends Document {

      public function core_taglib_setproperty(){
      }

      function onAfterAppend(){
         $this->__ParentObject->setAttribute($this->__Attributes['name'],$this->__Attributes['value']);
       // end function
      }

    // end class
   }
?>