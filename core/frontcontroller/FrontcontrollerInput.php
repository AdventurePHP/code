<?php
namespace APF\core\frontcontroller;

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
use APF\core\pagecontroller\APFObject;

/**
 * Implements a base class for input parameters for front controller actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.01.2007<br />
 * Version 0.2, 17.06.2014 (Introduced interface and separated from APFObject's attributes implementation)<br />
 */
class FrontcontrollerInput extends APFObject implements ActionParameters {

   /**
    * @var Action The action the input belongs to.
    */
   private $action;

   /**
    * @var array Action parameters provided via configuration and/or URL.
    */
   protected $parameters = array();

   public function &getAction() {
      return $this->action;
   }

   public function setAction(Action &$action) {
      $this->action = & $action;
   }

   public function setParameter($name, $value) {
      $this->parameters[$name] = $value;
   }

   public function setParameters(array $parameters) {
      $this->parameters = array_merge($this->parameters, $parameters);
   }

   public function getParameter($name, $default = null) {
      return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
   }

   public function getParameters() {
      return $this->parameters;
   }

}
