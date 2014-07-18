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
namespace APF\core\configuration\provider;

use APF\core\configuration\Configuration;

/**
 * Provides base functionality for the concrete configuration object implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.10.2010<br />
 */
abstract class BaseConfiguration implements Configuration{

   /**
    * Stores the values of the current configuration/section.
    *
    * @var array $values
    */
   private $values = array();

   /**
    * Stores the sections of the current config.
    *
    * @var Configuration[] $sections
    */
   private $sections = array();

   public function getSection($name, $delimiter = null) {

      if ($delimiter === null) {
         return isset($this->sections[$name]) ? $this->sections[$name] : null;
      }

      $names = explode($delimiter, $name);
      $depth = count($names);

      return $this->getSectionByPath($depth, $names);

   }

   private function getSectionByPath($depth, array $names, &$foundDepth = null) {
      $lastSection = $this;

      for ($foundDepth = 0; $foundDepth < $depth; $foundDepth++) {

         $nextSection = $lastSection->getSection($names[$foundDepth]);

         if ($nextSection === null) {
            return $lastSection;
         }

         $lastSection = $nextSection;
      }

      return $nextSection;
   }

   public function getSectionNames() {
      return array_keys($this->sections);
   }

   public function getValue($name, $defaultValue = null, $delimiter = null) {
      if ($delimiter === null) {
         return isset($this->values[$name]) ? $this->values[$name] : $defaultValue;
      }

      $names = explode($delimiter, $name);
      $depth = count($names) - 1;

      $section = $this->getSectionByPath($depth, $names);

      return $section->getValue($names[$depth], $defaultValue);

   }

   public function getValueNames() {
      return array_keys($this->values);
   }

   public function setSection($name, Configuration $section, $delimiter = null) {
      if ($delimiter === null) {
         $this->sections[$name] = $section;
         return;
      }

      $names = explode($delimiter, $name);
      $depth = count($names) - 1;

      $previousSection = $this->getSectionByPath($depth, $names, $foundDepth);

      if ($depth === $foundDepth) {
         $previousSection->setSection($names[$foundDepth], $section);

         return;
      }
      $configClass = get_class($this);
      do {
         $previousSection->setSection($names[$foundDepth], new $configClass());
         $previousSection = $previousSection->getSection($names[$foundDepth++]);
      } while ($foundDepth<$depth-1);

      $previousSection->setSection($names[$foundDepth], $section);

   }

   public function setValue($name, $value, $delimiter = null) {
      if($delimiter===null){
         $this->values[$name] = $value;
         return;
      }

      $names = explode($delimiter, $name);
      $depth = count($names) - 1;

      $previousSection = $this->getSectionByPath($depth, $names, $foundDepth);

      if ($depth === $foundDepth) {
         $previousSection->setValue($names[$foundDepth], $value);

         return;
      }

      $configClass = get_class($this);
      do {
         $previousSection->setSection($names[$foundDepth], new $configClass());
         $previousSection = $previousSection->getSection($names[$foundDepth++]);
      } while ($foundDepth<$depth-1);

      $previousSection->setValue($names[$foundDepth], $value);
   }

   public function removeSection($name, $delimiter = null) {
      if($delimiter === null){
         unset($this->sections[$name]);
         return;
      }

      $names = explode($delimiter, $name);
      $depth = count($names) - 1;

      $previousSection = $this->getSectionByPath($depth, $names, $foundDepth);

      if($depth === $foundDepth){
         $previousSection->removeSection($names[$depth]);
      }
   }

   public function removeValue($name, $delimiter = null) {
      if($delimiter === null){
         unset($this->values[$name]);
         return;
      }

      $names = explode($delimiter, $name);
      $depth = count($names) - 1;

      $previousSection = $this->getSectionByPath($depth, $names, $foundDepth);

      if($depth === $foundDepth){
         $previousSection->removeValue($names[$depth]);
      }
   }

}
