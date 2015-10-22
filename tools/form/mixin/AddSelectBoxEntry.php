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
namespace APF\tools\form\mixin;

use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\XmlParser;

/**
 * Provides functionality to add a select option to either a (multi-)select
 * field or a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.10.2015 (Extracted functionality to avoid code duplication)<br />
 */
trait AddSelectBoxEntry {

   /**
    * Appends a tag instance (option or group) to the current select box.
    *
    * @param DomNode $tag The tag instance to add.
    *
    * @return DomNode The newly created instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.10.2015<br />
    */
   protected function &addEntry(DomNode $tag) {

      $objectId = XmlParser::generateUniqID();
      $this->children[$objectId] = $tag;
      $this->children[$objectId]->setObjectId($objectId);

      $this->children[$objectId]->setLanguage($this->language);
      $this->children[$objectId]->setContext($this->context);
      $this->children[$objectId]->onParseTime();

      // inject parent object (=this) to guarantee native DOM tree environment
      $this->children[$objectId]->setParentObject($this);
      $this->children[$objectId]->onAfterAppend();

      // add xml marker, necessary for transformation
      $this->content .= '<' . $objectId . ' />';

      return $this->children[$objectId];
   }

}
