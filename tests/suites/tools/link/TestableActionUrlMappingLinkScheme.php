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
namespace APF\tests\suites\tools\link;

use APF\core\frontcontroller\ActionUrlMapping;

/**
 * Encapsulates link scheme overrides for action mappings used for testing purposes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.03.2014<br />
 */
trait TestableActionUrlMappingLinkScheme {

   /**
    * @var ActionUrlMapping[]
    */
   private $actionMappings = array();

   public function addActionMapping(ActionUrlMapping $mapping) {
      $this->actionMappings[] = $mapping;
   }

   protected function getActionUrlMapping($namespace, $name) {
      foreach ($this->actionMappings as $mapping) {
         if ($mapping->getNamespace() == $namespace && $mapping->getName() == $name) {
            return $mapping;
         }
      }

      return null;
   }

   protected function getActionUrMappingTokens() {
      $keys = array();
      foreach ($this->actionMappings as $mapping) {
         $keys[] = $mapping->getUrlToken();
      }

      return $keys;
   }

}
