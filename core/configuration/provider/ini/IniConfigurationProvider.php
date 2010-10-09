<?php
   import('core::configuration::provider::ini','IniConfiguration');

   /**
    * @package core::configuration::provider::ini
    * @class IniConfigurationProvider
    *
    * Implements the configuration provider for the default APF ini format. The
    * following features can be activated:
    * <ul>
    * <li>
    *    Disable context: in case $omitContext is set to true, the context will not be
    *    added to the configuration file path.
    * </li>
    * <li>
    *    Activate environment fallback: in case $activateEnvironmentFallback is set to true,
    *    the configuration provider first looks up the desired configuration file with the
    *    current environment prefix and falls back to DEFAULT environment. Having this feature
    *    activated you may only specify the configuration files, that are really depending on
    *    the environment.
    * </li>
    * <li>
    *    Parse sub sections: in case $parseSubSections is set to true, configuration keys containing
    *    a dot (".") are interpreted as sub-sections of the current section. Providing a key named
    *    "conf.abc" and "conf.def" will generate a section "conf" with the two keys "abc" and "def".
    * </li>
    * </ul>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   class IniConfigurationProvider implements ConfigurationProvider {
      
      /**
       * @var boolean Set to true, the context is omitted within the configuration file path.
       */
      private $omitContext = false;

      /**
       * @var boolean Set to true, the environment fallback will be activated.
       */
      private $activateEnvironmentFallback = false;
      
      /**
       * @private
       * @var string The sub key delimiter.
       */
      private static $NAMESPACE_DELIMITER = '.';

      public function setOmitContext($omitContext) {
         $this->omitContext = $omitContext;
      }

      public function setActivateEnvironmentFallback($activateEnvironmentFallback) {
         $this->activateEnvironmentFallback = $activateEnvironmentFallback;
      }

      public function loadConfiguration($namespace, $context, $language, $environment, $name) {

         $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

         if(file_exists($fileName)){
            $rawConfig = parse_ini_file($fileName, true);
            return $this->parseConfig($rawConfig);
         } else {
            if($this->activateEnvironmentFallback){
               return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
            } else {
               throw new ConfigurationException('[IniConfigurationProvider::loadConfiguration()] '
                       .'Configuration with namepace "'.$namespace.'", context "'.$context.'", '
                       .' language "'.$language.'", environment "'.$environment.'", and name '
                       .'"'.$name.'" cannot be loaded!', E_USER_ERROR);
            }
         }
      }

      /**
       * @private
       *
       * Creates the configuration representation for all sections.
       *
       * @param string[] $entries The sections of the current configuration.
       * @return IniConfiguration The appropriate configuration.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.10.2010<br />
       */
      private function parseConfig($entries) {

         $config = new IniConfiguration();
         foreach ($entries as $section => $entries) {
            $config->setSection($section, $this->parseSection($entries));
         }
         return $config;

      }

      /**
       * @private
       *
       * Creates the configuration representation of one single section.
       *
       * @param string[] $entries The entries of the current main section.
       * @return IniConfiguration The configuration, that represents the applied entries.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.10.2010<br />
       */
      private function parseSection(array $entries){

         $config = new IniConfiguration();
         foreach($entries as $name => $value){
            $config->setValue($name, $value);

            // do always parse sub sections to have a clear API for the configuration provider
            $dot = strpos($name, self::$NAMESPACE_DELIMITER);
            if($dot !== false){
               $this->parseSubSection($config, $name, $value);
            }
         }
         return $config;
      }

      /**
       * @private
       *
       * Creates the sub-section configuration representation in case the
       * parse sub section feature is activated.
       *
       * @param Configuration $config The current configuration.
       * @param string $name The name of the current section.
       * @param string $value The value of the current section.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.10.2010<br />
       */
      private function parseSubSection(Configuration &$config, $name, $value){
         
         $dot = strpos($name, self::$NAMESPACE_DELIMITER);
         if ($dot === false) {
            $config->setValue($name, $value);
         } else {
            $subSectionName = substr($name, 0, $dot);
            $remainingName = substr($name, $dot + strlen(self::$NAMESPACE_DELIMITER));

            $nextSection = $config->getSection($subSectionName);
            if($nextSection === null){
               $nextSection = new IniConfiguration();
            }

            $this->parseSubSection($nextSection, $remainingName, $value);
            $config->setSection($subSectionName, $nextSection);
         }
      }

      public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {
         $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

         $buffer = '';
         /* @var $config IniConfiguration */
         foreach ($config->getSections() as $name => $section) {
            $buffer .= '[' . $name . ']' . PHP_EOL;
            $buffer .= $this->processSection($section);
            $buffer .= PHP_EOL;
         }

         file_put_contents($fileName, $buffer);
      }

      private function processSection(IniConfiguration $section) {
         $buffer = '';
         foreach ($section->getValues() as $name => $value) {
            $buffer .= $name . ' = "' . $value . '"' . PHP_EOL;
         }
         return $buffer;
      }

      /**
       * @param string $namespace The namespace of the desired config.
       * @param string $context The current application's context.
       * @param string $language The current application's language.
       * @param string $environment The current environment.
       * @param string $name The name of the desired config.
       * @return string The appropriate file path.
       */
      private function getFilePath($namespace, $context, $language, $environment, $name) {

         // fallback for missing file extensions (backward compatibility for pre-1.13 config files)
         if (!preg_match('/\.ini$/i', $name)) {
            $name = $name . '.ini';
         }

         // assemble the context
         $contextPath = ($this->omitContext || $context === null )? '' : '/' . str_replace('::', '/', $context);

         // assemble file name
         $fileName = ($environment === null) ? '/' . $name : '/' . $environment . '_' . $name;

         // using APPS__PATH is about 50 times faster than the registry!
         return APPS__PATH
            . '/config'
            . '/' . str_replace('::', '/', $namespace)
            . $contextPath
            . $fileName;
      }

   }
?>