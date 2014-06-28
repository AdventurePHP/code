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
use APF\core\service\APFDIService;

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
interface Action extends APFDIService {

   const TYPE_PRE_PAGE_CREATE = 'prepagecreate';
   const TYPE_PRE_TRANSFORM = 'pretransform';
   const TYPE_POST_TRANSFORM = 'posttransform';

   /**
    * Returns the input object of the action.
    *
    * @return FrontcontrollerInput The input object associated with the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.02.2007<br />
    */
   public function getInput();

   /**
    * Injects the input param wrapper of the current action.
    *
    * @param FrontcontrollerInput $input The input object associated with the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setInput($input);

   /**
    * Returns the associated front controller instance.
    *
    * @return Frontcontroller The associated front controller instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getFrontController();

   /**
    * Allows you to manipulate the priority of this instance in comparison to other actions
    * of the same type on the action stack.
    * <p/>
    * In case priority of another action is higher than the value returned by this method for
    * the current instance, this action takes higher priority and is executed first.
    * <p/>
    * Default value is 10 to allow easier prioritization at a granular level. Example: returning
    * <em>1</em> means lower priority, <em>20</em> means higher priority.
    *
    * @return int The action's priority on the action stack.
    */
   public function getPriority();

   /**
    * Let's the front controller inject itself.
    *
    * @param Frontcontroller $frontController The current front controller instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setFrontController(Frontcontroller &$frontController);

   /**
    * Returns the name of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @return string The name of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getActionName();

   /**
    * Returns the indicator, whether the action should be kept in the url
    * generating a fully qualified front controller link.
    *
    * @return bool The url generation indicator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getKeepInUrl();

   /**
    * Sets the name of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @param string $name The name of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setActionName($name);

   /**
    * Sets the type of the action, that defines the execution time.
    *
    * @param string $type The type of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setType($type);

   /**
    * Returns the type of the action, that defines the execution time.
    *
    * @return string The type of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getType();

   /**
    * Sets the namespace of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @param string $namespace The namespace of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setActionNamespace($namespace);

   /**
    * Set the indicator, whether the action should be kept in the url
    * generating a fully qualified front controller link.
    *
    * @param bool $keepInUrl The url generation indicator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setKeepInUrl($keepInUrl);

   /**
    * Indicates, whether the action should be executed by the front controller or not.
    * This method can be overridden in case an action should not be executed due to
    * special concerns. This maybe the execution of another action or certain url params.
    *
    * @return boolean True, in case the action should be executed, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.08.2010<br />
    */
   public function isActive();

   /**
    * Returns the namespace of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @return string The namespace of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getActionNamespace();

   /**
    * Defines the interface method, that must be implemented by each concrete action. The method
    * is called by the front controller when the action is executed.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.01.2007<br />
    */
   public function run();

}