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

   import('tools::html::taglib','item_taglib_placeholder');
   import('tools::html::taglib','item_taglib_getstring');
   import('tools::html::taglib','item_taglib_addtaglib');


   /**
    * @namespace tools::html::taglib
    * @class iterator_taglib_item
    *
    * Implementiert die Repr�sentation eines Items.<br />
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   class iterator_taglib_item extends Document {

      /**
       * @public
       *
       * Initializes the known taglibs.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 01.06.2008<br />
       */
      function iterator_taglib_item(){
         $this->__TagLibs[] = new TagLib('tools::html::taglib','item','placeholder');
         $this->__TagLibs[] = new TagLib('tools::html::taglib','item','getstring');
         $this->__TagLibs[] = new TagLib('tools::html::taglib','item','addtaglib');
       // end functioin
      }

      /**
       * @public
       *
       * Analyzes the child tags and creates the DOM tree using
       * the included parser method.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 01.06.2008<br />
       */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }

      /**
       * @public
       *
       * Returns the place holders defined within the item, to be filled
       * te desired values.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 19.10.2009<br />
       */
      public function &getPlaceHolders(){

         $placeHolders = array();
         if(count($this->__Children) > 0){
            foreach($this->__Children as $objectId => $DUMMY){
               if(get_class($this->__Children[$objectId]) == 'item_taglib_placeholder'){
                  $placeHolders[] = &$this->__Children[$objectId];
               }
            }
         }
         return $placeHolders;

       // end function
      }

    // end class
   }
?>