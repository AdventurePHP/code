<?php
namespace APF\tools\html\taglib;

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
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\PlaceHolderTag;
use APF\core\pagecontroller\TagLib;

/**
 * @package APF\tools\html\taglib
 * @class HtmlIteratorItemTag
 *
 * Represents an item within the iterator.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2008<br />
 * Version 0.3, 11.05.2014 (ID#187: allow template expressions within iterator items)<br />
 */
class HtmlIteratorItemTag extends Document {

   /**
    * @public
    *
    * Initializes the known tags.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function __construct() {
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'item', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'item', 'getstring');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'item', 'addtaglib');
   }

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * @public
    *
    * Returns the place holders defined within the item, to be filled
    * te desired values.
    *
    * @return PlaceHolderTag[] The list of place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2009<br />
    */
   public function &getPlaceHolders() {
      $placeHolders = array();
      if (count($this->children) > 0) {
         foreach ($this->children as $objectId => $DUMMY) {
            if ($this->children[$objectId] instanceof PlaceHolderTag) {
               $placeHolders[] = & $this->children[$objectId];
            }
         }
      }
      return $placeHolders;
   }

}