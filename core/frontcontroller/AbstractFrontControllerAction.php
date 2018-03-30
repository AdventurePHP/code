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
namespace APF\core\frontcontroller;

use APF\core\http\mixins\GetRequestResponse;
use APF\core\pagecontroller\APFObject;

/**
 * Implements an action interface for a front controller action.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.01.2007<br />
 * Version 0.2, 24.02.2007 (Added param "KeepInURL")<br />
 * Version 0.3, 08.11.2007 (Switched default value for "KeepInURL" to false)<br />
 * Version 0.4, 07.08.2010 (Added the isActive() method to be able to self-deactivate actions on demand)<br />
 * Version 0.5, 12.04.2011 (Introduced constants for action types to be more type safe)<br />
 */
abstract class AbstractFrontControllerAction extends APFObject implements Action {

   use GetRequestResponse;

   /**
    * The namespace of the action.
    *
    * @var string $actionNamespace
    */
   protected $actionNamespace;

   /**
    * The name of the action (used to identify the action within the action stack).
    *
    * @var string $actionName
    */
   protected $actionName;

   /**
    * Input object of the action.
    *
    * @var ActionParameters $parameters
    */
   protected $parameters;

   /**
    * Defines the type of the action. Allowed values
    * <ul>
    *   <li>prepagecreate: executed before the page controller page is created</li>
    *   <li>pretransform: executed before transformation of the page</li>
    *   <li>posttransform: executed after transformation of the page</li>
    * </ul>
    * The default value is "prepagecreate".
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;

   /**
    * Indicates, if the action should be included in the URL. Values: true | false.
    *
    * @var boolean $keepInUrl
    */
   private $keepInUrl = false;

   /**
    * The front controller instance the action belongs to.
    *
    * @var FrontController $frontController
    */
   private $frontController;

   public function getParameters() {
      return $this->parameters;
   }

   public function setParameters(ActionParameters $input) {
      $this->parameters = $input;
   }

   public function setActionName(string $name) {
      $this->actionName = $name;
   }

   public function getActionName() {
      return $this->actionName;
   }

   public function setActionNamespace(string $namespace) {
      $this->actionNamespace = $namespace;
   }

   public function getActionNamespace() {
      return $this->actionNamespace;
   }

   public function setType(string $type) {
      $this->type = $type;
   }

   public function getType() {
      return $this->type;
   }

   public function setKeepInUrl(bool $keepInUrl) {
      $this->keepInUrl = $keepInUrl;
   }

   public function getKeepInUrl() {
      return $this->keepInUrl;
   }

   public function isActive() {
      return true;
   }

   public function allowExecution() {
      return true;
   }

   public function getPriority() {
      return 10;
   }

   public function getFrontController() {
      return $this->frontController;
   }

   public function setFrontController(FrontController $frontController) {
      $this->frontController = $frontController;
   }

}
