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
namespace APF\core\configuration\provider;

use APF\core\configuration\Configuration;

/**
 * Provides base functionality for the concrete configuration object implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.10.2010<br />
 * Version 0.2, 13.03.2015 (ID#224: Introduced path expression capability)<br />
 */
abstract class BaseConfiguration implements Configuration {

   /**
    * Stores the values of the current configuration/section.
    *
    * @var array $values
    */
   protected $values = [];

   /**
    * Stores the sections of the current config.
    *
    * @var Configuration[] $sections
    */
   protected $sections = [];

   public function getSection($name) {

      // Exit with direct match (check for section path usage not necessary as ConfigurationProvider
      // don't produce section names with a delimiter).
      if (isset($this->sections[$name])) {
         return $this->sections[$name];
      }

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         $path = explode(Configuration::SECTION_PATH_SEPARATOR, $name);

         return $this->getSectionByPath($path);
      }

      // Whenever we have no direct match or path query the requested configuration
      // section is not existing. To avoid NPEs and allow fluent interface style
      // queries return new instance of the same type (Null Pattern!) in any case.
      //
      // PLEASE NOTE: This feature is only for safety reasons. Hence, the returned
      // section is not added to this DTO, so hasSection() will still return false.
      /* @var $configType Configuration */
      $configType = get_class($this);

      return (new $configType());
   }

   public function hasSection($name) {

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         $path = explode(Configuration::SECTION_PATH_SEPARATOR, $name);

         // Decided to use custom implementation here and not using getSection() or
         // getSectionByPath() as they would either return a section in any case
         // (Null Pattern) or create the section on-the-fly.
         //
         // Means: hasSection() will return false for a non-existing section as long as
         // setSection() has not been called - e.g. calling getSection().
         $exists = true;
         $section = $this;
         foreach ($path as $part) {
            $exists = isset($section->sections[$part]);
            if ($exists) {
               // iterate into the next level
               $section = $section->sections[$part];
            } else {
               // when we don't find a matching path break as one miss breaks the entire path
               break;
            }
         }

         return $exists;

      } else {
         return isset($this->sections[$name]);
      }
   }

   public function getSectionNames() {
      return array_keys($this->sections);
   }

   public function getValue($name, $defaultValue = null) {

      // Exit with direct match (check for value path usage not necessary as ConfigurationProvider
      // don't produce value names with a delimiter).
      if (isset($this->values[$name])) {
         return $this->values[$name];
      }

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {

         $parts = explode(Configuration::SECTION_PATH_SEPARATOR, $name);
         $length = count($parts);

         $valueName = $parts[$length - 1];
         unset($parts[$length - 1]);

         return $this->getSectionByPath($parts)->getValue($valueName, $defaultValue);
      }

      return $defaultValue;
   }

   public function getValueNames() {
      return array_keys($this->values);
   }

   public function hasValue($name) {
      return $this->getValue($name) !== null;
   }

   public function setSection($name, Configuration $section) {

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         list($path, $sectionName) = $this->getPathParts($name);
         $this->getSectionByPath($path)->setSection($sectionName, $section);
      } else {
         $this->sections[$name] = $section;
      }

      return $this;
   }

   public function setValue($name, $value) {

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         list($path, $valueName) = $this->getPathParts($name);
         $this->getSectionByPath($path)->setValue($valueName, $value);
      } else {
         $this->values[$name] = $value;
      }

      return $this;
   }

   public function removeSection($name) {

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         list($path, $sectionName) = $this->getPathParts($name);
         $this->getSectionByPath($path)->removeSection($sectionName);
      } else {
         unset($this->sections[$name]);
      }

      return $this;
   }

   public function removeValue($name) {

      if (strpos($name, Configuration::SECTION_PATH_SEPARATOR) !== false) {
         list($path, $valueName) = $this->getPathParts($name);
         $this->getSectionByPath($path)->removeValue($valueName);
      } else {
         unset($this->values[$name]);
      }

      return $this;
   }

   /**
    * Splits up a section or value path expression into the real path and the name
    * of the last section or the value name.
    *
    * @param string $name The section or value name containing a path expression.
    *
    * @return array A list of the path and the identifier (section or value name).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.03.2015<br />
    */
   protected function getPathParts($name) {
      $parts = explode(Configuration::SECTION_PATH_SEPARATOR, $name);
      $length = count($parts);

      $identifier = $parts[$length - 1];
      unset($parts[$length - 1]);

      return [$parts, $identifier];
   }

   /**
    * Returns a configuration section starting at this instance addressed by
    * the provided part. In case the section does not exist, it is created
    * on the fly (Null Pattern).
    *
    * @param array $path The path to the configuration section.
    *
    * @return Configuration The desired configuration instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.03.2015<br />
    */
   protected function getSectionByPath(array $path) {

      $section = $this;

      $configType = get_class($this);

      foreach ($path as $part) {
         if (!$section->hasSection($part)) {
            $section->setSection($part, new $configType());
         }
         $section = $section->getSection($part);
      }

      return $section;
   }

}
