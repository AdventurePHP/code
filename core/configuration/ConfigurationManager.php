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

   /**
    * @package core::configuration
    * @class Configuration
    *
    * Represents a configuration object, that is loaded by the ConfigurationManager. It stores
    * section or subsections and their corresponding values.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.01.2007<br />
    */
   final class Configuration {

      /**
       * @private
       * @var string[] Container for configuration entries.
       */
      private $__Configuration = array();

      public function Configuration(){
      }

      /**
       * @public
       *
       * Returns a configuration section as an associative array.
       *
       * @param string $Name; Name of the cection
       * @return string[] Section or null.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.01.2007<br />
       */
      public function getSection($name){

         if(isset($this->__Configuration[$name])){
            return $this->__Configuration[$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Returns a configuration sub section as an associative array.
       *
       * @param string $section name of the section.
       * @param string $name name of the subsection.
       * @return string[] Value of the configuration key or null.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 30.01.2007<br />
       * Version 0.2, 31.01.2007 (Added check for Subsection to be an array)<br />
       * Version 0.3, 16.11.2007 (removed senseless trim() during return)<br />
       * Version 0.4, 19.04.2009 (Bugfix: added check for the subsection to exist)<br />
       */
      public function getSubSection($section,$name){

         if(isset($this->__Configuration[$section][$name]) && is_array($this->__Configuration[$section][$name])){
            return $this->__Configuration[$section][$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Returns a configuration value by section and attribute name.
       *
       * @param string $section name of the section.
       * @param string $name name of the config key.
       * @return string Value of the configuration key or null.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.01.2007<br />
       */
      public function getValue($section,$name){

         if(isset($this->__Configuration[$section][$name])){
            return $this->__Configuration[$section][$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Fills the internal configuration container with the configuration content.
       *
       * @param array $list configuration array
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.01.2007<br />
       */
      public function setConfiguration($list){
         $this->__Configuration = $list;
       // end function
      }

      /**
       * @public
       *
       * Returns the entire configuration as an associative array.
       *
       * @return array $list the configuration array
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 03.02.2007<br />
       */
      public function getConfiguration(){
         return $this->__Configuration;
       // end function
      }

    // end class
   }

   /**
    * @package core::configuration
    * @class ConfigurationManager
    *
    * The ConfigurationManager represents a configuration utility, that loads and handles configurations
    * that depend on the context and the environment tzhe application or module is executed in. The
    * manager must be instanciated singleton!
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.01.2007<br />
    * Version 0.2, 07.03.2007 (Refactoring due to context introduction)<br />
    * Version 0.3, 02.04.2007 (Additional testing with some fine tuning)<br />
    * Version 0.4, 16.11.2007 (Re-added $__NamespaceDelimiter)<br />
    * Version 0.5, 21.06.2008 (Introduced Registry to get the current Environment string)<br />
    */
   class ConfigurationManager extends APFObject {

      /**
       * @private
       * @var string[] Caches the configurations loaded before.
       */
      private $__Configurations = array();

      /**
       * @private
       * @var string Subkey delimiter.
       */
      private $__NamespaceDelimiter = '.';

      public function ConfigurationManager(){
      }

      /**
       * @public
       *
       * Loads a configuration described through the given param. Configuration files must be stored
       * within the config namespace. Usage:
       * <pre>
       * $cM = &Singleton::getInstance('ConfigurationManager');<br />
       * $config = &$cM->getConfiguration('sites::mysite','actions','permanentactions');
       * </pre>
       * Within classes inherited from APFObject, the <em>__getConfiguration()</em> wrapper can be used.
       *
       * @param string $namespace namespace of the requested configuration (will be prefixed with "config::")
       * @param string $context context of the configuration file
       * @param string $configName the name of the configuration file
       * @param bool $parseSubsections defines if subsections ("." notation) should be parsed as subsections
       * @return Configuration The configuration or null in case of failure.
       * @throws InvalidArgumentException In case the configuration cannot be loaded.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.03.2007<br />
       * Version 0.2, 02.04.2007 (An error is triggered if the config cannot be loaded)<br />
       * Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
       */
      public function &getConfiguration($namespace,$context,$configName,$parseSubsections = false){

         // calculate config hash
         $configHash = md5($namespace.$context.$configName);

         // check if config exists
         if($this->configurationExists($namespace,$context,$configName) == true){

            // check if config is already loaded
            if(!isset($this->__Configurations[$configHash])){

               // load config
               $Configuration = $this->__loadConfiguration($namespace,$context,$configName);
               $CfgObj = new Configuration();

               if($parseSubsections == true){
                  $CfgObj->setConfiguration($this->__parseConfiguration($Configuration));
                // end if
               }
               else{
                  $CfgObj->setConfiguration($Configuration);
                // end else
               }

               // cache config
               $this->__Configurations[$configHash] = $CfgObj;

             // end if
            }

          // end if
         }
         else{
            $env = Registry::retrieve('apf::core','Environment');
            throw new InvalidArgumentException('[ConfigurationManager->getConfiguration()] Requested '
                    .'configuration with name "'.$env.'_'.$configName.'.ini" cannot be '
                    .'loaded from namespace "'.$namespace.'" with context "'.$context.'"!',E_USER_ERROR);
         }

         return $this->__Configurations[$configHash];

       // end function
      }

      /**
       * @public
       *
       * Checks, if a configuration file exists or not.
       *
       * @param string $namespace namespace of the requested configuration
       * @param string $context context of the configuration file
       * @param string $configName the name of the configuration file
       * @return bool $configurationExistent true | false
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.02.2007<br />
       * Version 0.2, 07.03.2007 (Renamed to configurationExists())<br />
       */
      public function configurationExists($namespace,$context,$configName){
         return file_exists($this->__getConfigurationFileName($namespace,$context,$configName));
      }

      /**
       * @private
       *
       * Loads a configuration file.
       *
       * @param string $namespace namespace of the requested configuration
       * @param string $context context of the configuration file
       * @param string $configName the name of the configuration file
       * @return array $configuration | null configuration array or null
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.01.2007<br />
       * Version 0.2, 03.02.2007 (Outsourced the file name generation)<br />
       * Version 0.3, 07.03.2007<br />
       * Version 0.4, 02.04.2007 (Removed else, because the existance is already checked before.)<br />
       */
      private function __loadConfiguration($namespace,$context,$configName){
         $configFile = $this->__getConfigurationFileName($namespace,$context,$configName);
         return parse_ini_file($configFile,true);
       // end function
      }

      /**
       * @protected
       *
       * Creates the fully qualified path of the configuration file. If you want to have an own
       * config file and folder layout, create a new class derived from the ConfigurationManager
       * and overwrite this method.
       * See http://forum.adventure-php-framework.org/de/viewtopic.php?f=1&t=80&start=30#p531 for
       * details on the discussion.
       *
       * @param string $namespace Namespace of the requested configuration (will be prefixed with "config")
       * @param string $context Context of the configuration file
       * @param string $configName The name of the configuration file
       * @return string Name of the configuration file name
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.02.2007<br />
       * Version 0.2, 07.03.2007<br />
       * Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
       * Version 0.4, 09.05.2009 (Made the function protected.)<br />
       */
      protected function __getConfigurationFileName($namespace,$context,$configName){

         if(strlen($context) > 0){
            $path = str_replace('::','/',$namespace).'/'.str_replace('::','/',$context);
          // end if
         }
         else{
            $path = str_replace('::','/',$namespace);
          // end else
         }

         // build the file name
         $env = Registry::retrieve('apf::core','Environment');
         return APPS__PATH.'/config/'.$path.'/'.$env.'_'.$configName.'.ini';

       // end function
      }

      /**
       * @private
       *
       * Parses the configuration file.
       *
       * @param string[] $configuration configuration array created with the parse_ini_file function.
       * @return string[] $configurationArray the parsed configuration list.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.01.2007<br />
       */
      private function __parseConfiguration($configuration = array()){

         $configurationArray = array();

         foreach($configuration as $Key => $Value){

            // if value is an array, parse subsections
            if(is_array($Value)){
               $configurationArray[$Key] = $this->__parseSubsections($Value);
             // end if
            }
            else{
               $configurationArray[$Key] = $Value;
             // end else
            }

          // end foreach
         }

         return $configurationArray;

       // end function
      }

      /**
       * @private
       *
       * Parses subsections.
       *
       * @param string[] $subsectionArray The configuration array.
       * @return string[] $parsedArray The parsed array.
       * @throws InvalidArgumentException In case the sub section cannot be parsed.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.01.2007<br />
       * Version 0.2, 19.04.2009 (Bugfix: Parsing subsections returned an empty array!)<br />
       * Version 0.3, 27.10.2009 (Bugfix: introduced __mergeArrayRecursive() due to problems with the PHP array merge function)<br />
       */
      private function __parseSubsections($subsectionArray){

         $concatenatedArray = array();

         if(is_array($subsectionArray)){

            foreach($subsectionArray as $key => $value){
               $concatenatedArray = $this->__mergeArrayRecursive($concatenatedArray,$this->__generateSubArray($key,$value));
             // end foreach
            }

          // end if
         }
         else{
            throw new InvalidArgumentException('[ConfigurationManager::__parseSubsections()] Given '
                    .'value is not an array!',E_USER_ERROR);
          // end else
         }

         return $concatenatedArray;

       // end function
      }

      /**
       * @private
       *
       * Generates sub arrays from the dot notated directices.
       *
       * @param string $key The current configuration directive possibly containg a dot.
       * @param string[] $value Value of the offset specified with $key.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.01.2007<br />
       */
      private function __generateSubArray($key,$value){

         $subArray = array();

         if(substr_count($key,$this->__NamespaceDelimiter) > 0){

            // search for the dot
            $delPos = strpos($key,$this->__NamespaceDelimiter);

            // extract offset
            $offset = substr($key,0,$delPos);

            // create remaining string
            $remainingString = substr($key,$delPos + strlen($this->__NamespaceDelimiter),strlen($key));

            // generate new offset recursivly
            $subArray[$offset] = $this->__generateSubArray($remainingString,$value);

          // end if
         }
         else{
            $subArray[$key] = $value;
          // end els
         }

         return $subArray;

       // end function
      }

      /**
       * @private
       *
       * Implements a pendant to php's array_merge_recursive(), because the native php
       * function does not support numeric key merging.
       *
       * @see http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=223&p=1669#p1669
       *
       * @param string[] $one The first array.
       * @param string[] $two The array to merge into the first array.
       * @return string[] The merged array.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.10.2009<br />
       */
      private function __mergeArrayRecursive($one,$two){
         foreach($two as $key => $value){
            if(isset($one[$key])){
               $one[$key] = $this->__mergeArrayRecursive($one[$key],$value);
            }
            else{
               $one[$key] = $value;
            }
         }
         return $one;
       // end function
      }

    // end class
   }
?>