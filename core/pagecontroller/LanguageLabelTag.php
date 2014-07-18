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

use APF\core\registry\Registry;
use InvalidArgumentException;

/**
 * Implements a class for the taglibs &lt;html:getstring /&gt; and &lt;template:getstring /&gt;. This
 * lib fetches the desired configuration value and returns it on transformation time. The configuration
 * files must be structured as follows:
 * <p/>
 * <pre>
 * [de]
 * key = "german value"
 *
 * [en]
 * key = "english value"
 *
 * ...
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.04.2006<br />
 * Version 0.2, 17.09.2009 (Refactored due to form taglib changes)<br />
 * Version 0.3, 11.02.2012 (Added LanguageLabelTag to core (refactoring!))
 */
class LanguageLabelTag extends Document {

   /**
    * A list of place holder names and values.
    *
    * @var array $placeHolders
    */
   private $placeHolders = array();

   /**
    * Implements the functionality to retrieve a language dependent value form a
    * configuration file. Checks the attributes needed for displaying data.
    *
    * @return string The desired translation text.
    * @throws InvalidArgumentException In case of parameter issues.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2006<br />
    * Version 0.2, 17.10.2008 (Enhanced error messages)<br />
    */
   public function transform() {

      // check for attribute "namespace"
      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
               . '"namespace" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "config"
      $configName = $this->getAttribute('config');
      if ($configName === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
               . '"config" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "entry"
      $entry = $this->getAttribute('entry');
      if ($entry === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
               . '"entry" given in tag definition!', E_USER_ERROR);
      }

      $deliminter = $this->getAttribute('delimiter', null);

      // get configuration values
      $config = $this->getConfiguration($namespace, $configName);
      $value = $config->getSection($this->getLanguage()) === null
            ? null
            : $config->getSection($this->getLanguage())->getValue($entry, null, $deliminter);

      if ($value == null) {

         // get environment variable from registry to have nice exception message
         $env = Registry::retrieve('APF\core', 'Environment');

         throw new InvalidArgumentException('[' . get_class($this) . '::transform()] Given entry "'
               . $entry . '" is not defined in section "' . $this->getLanguage() . '" in configuration "'
               . $env . '_' . $configName . '" in namespace "' . $namespace . '" and context "'
               . $this->getContext() . '"!', E_USER_ERROR);
      }

      return $this->replace($value);
   }

   /**
    * Let's you add a place holder that is replaced into the current label. Each place holder
    * must be defined with square brackets ("{" and "}") with the key between the opening and
    * the closing bracket (e.g. "{foo}" in case the name of the place holder is "foo").
    *
    * @param string $name The name of the place holder.
    * @param string $value The value of the place holder.
    * @param bool $append True in case the applied value should be appended, false otherwise.
    *
    * @return LanguageLabelTag This instance for further usage (e.g. adding further place holders).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.01.2012<br />
    * Version 0.2, 06.08.2013 (Added support for appending content to place holders)<br />
    */
   public function &setPlaceHolder($name, $value, $append = false) {
      // false handled first, since most usages don't append --> slightly faster
      if ($append === false) {
         $this->placeHolders[$name] = $value;
      } else {
         $this->placeHolders[$name] = $this->placeHolders[$name] . $value;
      }

      return $this;
   }

   /**
    * Resets the list of place holders that have been defined so far.
    *
    * @return LanguageLabelTag This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.05.2013<br />
    */
   public function &clearPlaceHolders() {
      $this->placeHolders = array();

      return $this;
   }

   /**
    * Replaces all place holders within the current label that are registered within this instance.
    *
    * @param string $label The raw label.
    *
    * @return string The label with replaced place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.01.2012<br />
    */
   protected function replace($label) {
      foreach ($this->placeHolders as $key => $value) {
         $label = str_replace('{' . $key . '}', $value, $label);
      }

      return $label;
   }

}
