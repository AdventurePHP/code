<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\modules\genericormapper\data;

/**
 * @author Nicolas Pecher
 * @version
 * Version 0.1, 23.04.2012
 */
class TreeItem extends GenericDomainObject {

   /**
    * The parent object of this navigation node
    *
    * @var GenericDomainObject $parentItem
    */
   protected $parentItem = null;

   /**
    * The children of this navigation node
    *
    * @var GenericDomainObject $children
    */
   protected $children = [];

   /**
    * Sets the parent object
    *
    * @param TreeItem $parentItem The parent object
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 23.03.2012
    */
   public function setParentItem(TreeItem $parentItem) {
      $this->parentItem = $parentItem;
   }

   /**
    * Returns the parent object
    *
    * @return TreeItem The parent object
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 23.03.2012
    */
   public function getParentItem() {
      return $this->parentItem;
   }

   /**
    * Adds a child object
    *
    * @param TreeItem $child An child object
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 23.03.2012
    */
   public function addChild(TreeItem $child) {
      $this->children[] = $child;
   }

   /**
    * Adds an array of child objects
    *
    * @param array $children An array of child items
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 23.03.2012
    */
   public function addChildren(array $children) {
      foreach ($children as $child) {
         $this->addChild($child);
      }
   }

   /**
    * Returns all child objects
    *
    * @return TreeItem[] A list of the child objects
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 23.03.2012
    */
   public function getChildren() {
      return $this->children;
   }
}
