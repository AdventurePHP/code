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
namespace APF\tools\html\taglib;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\PlaceHolderTag;

/**
 * Represents an item within the iterator.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2008<br />
 * Version 0.2, 11.05.2014 (ID#187: allow template expressions within iterator items)<br />
 * Version 0.3, 07.03.2015 (ID#118: added iterator stacking support)<br />
 */
class HtmlIteratorItemTag extends Document {

   use GetIterator;

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   public function onAfterAppend() {
      $this->extractExpressionTags();
   }

   /**
    * Returns the list of place holder names defined within the item, to be filled
    * with the desired values.
    *
    * @return string[] The list of place holder names.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2009<br />
    * Version 0.2, 12.03.2016 (ID#287: switched to different place holder concept)<br />
    */
   public function &getPlaceHolderNames() {
      $placeHolderNames = [];
      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {
            if ($child instanceof PlaceHolderTag) {
               $placeHolderNames[] = $child->getAttribute('name');
            }
         }
      }

      return $placeHolderNames;
   }

}
