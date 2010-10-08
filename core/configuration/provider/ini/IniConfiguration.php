<?php
   /**
    * @package core::configuration::provider::ini
    * @class IniConfiguration
    * 
    * Implements the configuration interface for the default APF ini scheme.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   class IniConfiguration implements Configuration {

      /**
       * @var array Stores the values of the current configuration/section.
       */
      private $values = array();

      /**
       * @var IniConfiguration[] Stores the sections of the current config.
       */
      private $sections = array();

      public function getSection($name) {
         return isset($this->sections[$name]) ? $this->sections[$name] : null;
      }

      public function getValue($name) {
         return isset($this->values[$name]) ? $this->values[$name] : null;
      }

      public function setSection($name, Configuration $section) {
         $this->sections[$name] = $section;
      }

      public function setValue($name, $value) {
         $this->values[$name] = $value;
      }

      public function getSections() {
         return $this->sections;
      }

      public function getValues() {
         return $this->values;
      }

      public function getSectionNames() {
         return array_keys($this->sections);
      }

      public function getValueNames() {
         return array_keys($this->values);
      }

   }
?>