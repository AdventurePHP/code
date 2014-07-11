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
namespace APF\core\pagecontroller;

/**
 * Represents the functionality of the core:addtaglib tag. Adds a further taglib to the known
 * tags of the tag's parent object. This can be used to enhance the known tag list if a
 * desired APF DOM node.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 */
class AddTaglibTag extends Document {

   /**
    * Implements the onParseTime() method of the Document class. Adds the desired
    * taglib to the parent object.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 10.11.2008 (Changed implementation. We now use getAttribute() instead of direct internal attribute addressing)<br />
    * Version 0.3, 14.02.2011 (Adapted to new Document::addTaglib() signature)<br />
    * Version 0.4, 11.06.2012 (Introduced fallback mechanism for old tag definitions)<br />
    * Version 0.5, 20.12.2012 (Removed fallback mechanism for 1.17)<br />
    */
   public function onParseTime() {

      $scope = $this->getAttribute('scope', 'global');

      // add tag to parent instance in case overriding is explicitly desired
      if ($scope === 'local') {
         $this->addInstanceTagLib(
               $this->getRequiredAttribute('class'),
               $this->getRequiredAttribute('prefix'),
               $this->getRequiredAttribute('name')
         );
      } else {
         self::addTagLib(
               $this->getRequiredAttribute('class'),
               $this->getRequiredAttribute('prefix'),
               $this->getRequiredAttribute('name')
         );
      }

      // Resets the attributes list to avoid selection issues with the
      // getChildNode() and getChildNodes() methods that may select this
      // node when requesting a node by the "name" attribute. See bug
      // http://forum.adventure-php-framework.org/viewtopic.php?f=8&t=1192
      // for more details.
      $this->attributes = array();
   }

   /**
    * Implements the Document's transform() method. Returns an empty string, because the addtaglib
    * tag generates no output.
    *
    * @return string Nothing, since this tag produces no output.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.04.2007<br />
    */
   public function transform() {
      return '';
   }

}
