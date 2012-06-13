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
 * @package tools::html::taglib
 * @class iterator_taglib_item
 *
 * Represents an item within the iterator.
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
   public function __construct() {
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'html_taglib_placeholder', 'item', 'placeholder');
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'html_taglib_getstring', 'item', 'getstring');
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'core_taglib_addtaglib', 'item', 'addtaglib');
   }

   public function onParseTime() {
      $this->__extractTagLibTags();
   }

   /**
    * @public
    *
    * Returns the place holders defined within the item, to be filled
    * te desired values.
    *
    * @return html_taglib_placeholder[] The list of place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2009<br />
    */
   public function &getPlaceHolders() {
      $placeHolders = array();
      if (count($this->__Children) > 0) {
         foreach ($this->__Children as $objectId => $DUMMY) {
            if ($this->__Children[$objectId] instanceof html_taglib_placeholder) {
               $placeHolders[] = &$this->__Children[$objectId];
            }
         }
      }
      return $placeHolders;
   }

}