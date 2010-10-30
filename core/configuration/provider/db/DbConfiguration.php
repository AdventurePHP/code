<?php
   class DbConfiguration implements Configuration {

      /**
       * @var array Stores the values of the current configuration/section.
       */
      private $values = array();

      /**
       * @var DbConfiguration[] Stores the sections of the current config.
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

      public function removeSection($name) {
         unset($this->sections[$name]);
      }

      public function removeValue($name) {
         unset($this->values[$name]);
      }

   }
?>