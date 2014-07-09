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

   private $sectionDelimiter = '.';

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

   public function getSection($name) {

      if(isset($this->sections[$name])){

         return $this->sections[$name];
      }

      if(!$this->isDelimiterUsed($name)){
         return null;
      }

      $path = explode($this->sectionDelimiter, $name);

      return $this->getSectionByPath($path);

   }

   public function getSectionNames() {
      return array_keys($this->sections);
   }

   public function getValue($name, $defaultValue = null) {

      if(isset($this->values[$name])){
         return $this->values[$name];
      }

      if(!$this->isDelimiterUsed($name)){
         return $defaultValue;
      }

      list($sectionPath, $valueName) = $this->explodePath($name);

      $section = $this->getSectionByPath($sectionPath);

      return ($section !== null)? $section->getValue($valueName, $defaultValue): $defaultValue;

   }

   public function getValueNames() {
      return array_keys($this->values);
   }

   public function setSection($name, Configuration $section) {

      if (!$this->isDelimiterUsed($name)) {
         $this->sections[$name] = $section;
         return;
      }

      list($path, $sectionName) = $this->explodePath($name);
      $subSection = $this->getSectionByPath($path, true);
      $subSection ->setSection($sectionName, $section);
   }

   public function setValue($name, $value) {

      if(!$this->isDelimiterUsed($name)){
         $this->values[$name] = $value;

         return;
      }

      list($path, $valueName) = $this->explodePath($name, true);

      $subSection = $this->getSectionByPath($path, true);
      $subSection ->setValue($valueName, $value);
   }

   public function removeSection($name) {
      if(!$this->isDelimiterUsed($name)){
         unset($this->sections[$name]);

         return;
      }

      list($path, $sectionName) = $this->explodePath($name, true);

      $subSection = $this->getSectionByPath($path);
      $subSection ->removeSection($sectionName);


   }

   public function removeValue($name) {
      if(!$this->isDelimiterUsed($name)){
         unset($this->values[$name]);

         return;
      }

      list($path, $valueName) = $this->explodePath($name, true);

      $subSection = $this->getSectionByPath($path);
      $subSection ->removeValue($valueName);
   }

   private function getSectionByPath(array $pathAsArray, $createIfMissing = false) {

      $currentSection = $this;

      while(true){
         $foundDepth = key($pathAsArray);
         $sectionName = current($pathAsArray);

         $nextSection = $currentSection->getSection($sectionName);

         if($nextSection === null){
            break;
         }

         $currentSection = $nextSection;

         if(!next($pathAsArray)){
            break;
         }
      }


      if(!$createIfMissing){
         if($foundDepth === count($pathAsArray) - 1){
            return $currentSection;
         }else{
            return null;
         }
      }

      $configClass = get_class($this);

      do{
         $sectionName=current($pathAsArray);
         $currentSection->setSection($sectionName, new $configClass());
         $lastSection = $currentSection->getSection($sectionName);

      }while(next($pathAsArray) !== false);

      return $lastSection;

   }

   private function isDelimiterUsed($name){
      return (strpos($name,$this->sectionDelimiter)!== false);
   }

   private function explodePath($name){

      $pathArray = explode('.',$name);

      $length = count($pathArray);

      $lastElement = $pathArray[$length-1];

      unset($pathArray[$length-1]);

      return array($pathArray, $lastElement);

   }

}
