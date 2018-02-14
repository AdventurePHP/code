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

/**
 * Defines the structure of a front controller action parameter instance.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.06.2014 (Introduced within ID#207)<br />
 */
interface ActionParameters {

   /**
    * Let's you set an action (URL) parameter.
    *
    * @param string $name The name of the (URL) parameter.
    * @param string $value The value to set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function setParameter($name, $value);

   /**
    * Let's you set multiple action (URL) parameters.
    *
    * @param array $parameters A list of (URL) parameters.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function setParameters(array $parameters);

   /**
    * Returns an action (URL) parameter value for the given parameter name.
    *
    * @param string $name The name of the (URL) parameter.
    * @param string $default Default value to return in case the (URL) parameter is not set.
    *
    * @return string The desired value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function getParameter($name, $default = null);

   /**
    * Returns all action (URL) parameters with their values.
    *
    * @return array The action (URL) parameters with their respective values.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function getParameters();

   /**
    * Returns the action associated with the current parameter instance.
    *
    * @return Action The front controller action this parameter instance belongs to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function getAction();

   /**
    * Let's you inject the action associated with the current parameter instance.
    *
    * @param Action $action The front controller action this parameter instance belongs to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2014<br />
    */
   public function setAction(Action &$action);

}
