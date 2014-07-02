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
namespace APF\core\frontcontroller;

/**
 * Represents an action url mapping to simplify and prettify URLs.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.03.2014<br />
 */
class ActionUrlMapping {

   /**
    * The URL shortcut for the action.
    *
    * @var string $urlToken
    */
   private $urlToken;

   /**
    * The action namespace.
    *
    * @var string $namespace
    */
   private $namespace;

   /**
    * The action name.
    *
    * @var string $name
    */
   private $name;

   /**
    * @param string $urlToken The URL shortcut for the action.
    * @param string $namespace The action namespace.
    * @param string $name The action name.
    */
   public function __construct($urlToken, $namespace, $name) {
      $this->urlToken = $urlToken;
      $this->namespace = $namespace;
      $this->name = $name;
   }

   public function getUrlToken() {
      return $this->urlToken;
   }

   public function getName() {
      return $this->name;
   }

   public function getNamespace() {
      return $this->namespace;
   }

}
