<?php
   /**
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
   */

   /**
   *  @namespace core::pagecontroller
   *  @class core_taglib_setattribute
   *
   *  Bietet die Möglichkeit ein Attribut eines Documents in der Template-Datei zu setzen.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 04.04.2007<br />
   */
   class core_taglib_setattribute extends coreObject
   {

      function core_taglib_setproperty(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend" und setzt ein Attribut der Eltern-Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.04.2007<br />
      */
      function onAfterAppend(){
         $this->__ParentObject->setAttribute($this->__Attributes['name'],$this->__Attributes['value']);
       // end function
      }

    // end class
   }
?>