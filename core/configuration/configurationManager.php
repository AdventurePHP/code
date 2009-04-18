<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace core::configuration
   *  @class Configuration
   *
   *  Represents a configuration object, that is loaded by the configurationManager. It stores
   *  section or subsections and their corresponding values.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.01.2007<br />
   */
   class Configuration
   {

      /**
      *  @private
      *  Container for configuration entries.
      */
      private $__Configuration = array();


      function Configuration(){
      }


      /**
      *  @public
      *
      *  Returns a configuration section as an associative array.
      *
      *  @param string $Name; Name of the cection
      *  @return array $Section | null; Section or null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function getSection($name){

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
      *  @public
      *
      *  Returns a configuration sub section as an associative array.
      *
      *  @param string $section name of the section
      *  @param string $name name of the subsection
      *  @return array $value | null value of the configuration key or null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.01.2007<br />
      *  Version 0.2, 31.01.2007 (Subsection wird auf array geprüft)<br />
      *  Version 0.3, 16.11.2007 (trim() bei return entfernt, da es keinen Sinn macht)<br />
      */
      function getSubSection($section,$name){

         if(is_array($this->__Configuration[$section][$name])){
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
      *  @public
      *
      *  Returns a configuration value by section and attribute name.
      *
      *  @param string $section name of the section
      *  @param string $name name of the config key
      *  @return string $value | null value of the configuration key or null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function getValue($section,$name){

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
      *  @public
      *
      *  Fills the internal configuration container with the configuration content.
      *
      *  @param array $list configuration array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function setConfiguration($list){
         $this->__Configuration = $list;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the entire configuration as an associative array.
      *
      *  @return array $list the configuration array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.02.2007<br />
      */
      function getConfiguration(){
         return $this->__Configuration;
       // end function
      }

    // end class
   }


   /**
   *  @namespace core::configuration
   *  @class configurationManager
   *
   *  The configurationManager represents a configuration utility, that loads and handles configurations
   *  that depend on the context and the environment tzhe application or module is executed in. The
   *  manager must be instanciated singleton!
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.01.2007<br />
   *  Version 0.2, 07.03.2007 (Kompletter Umbau wegen Strukturänderung (Context!!!)<br />
   *  Version 0.3, 02.04.2007 (Abschließend getestet und Feinheiten optimiert)<br />
   *  Version 0.4, 16.11.2007 (Definition von $__NamespaceDelimiter wiederhergestellt)<br />
   *  Version 0.5, 21.06.2008 (Introduced Registry to get the current Environment string)<br />
   */
   class configurationManager extends coreObject
   {

      /**
      *  @private
      *  Caches the configurations loaded before.
      */
      private $__Configurations = array();


      /**
      *  @private
      *  Subkey delimiter.
      */
      private $__NamespaceDelimiter = '.';


      function configurationManager(){
      }


      /**
      *  @public
      *
      *  Loads a configuration described through the given param. Configuration files must be stored
      *  within the config namespace. Usage:
      *  <pre>
      *  $cM = &Singleton::getInstance('configurationManager');<br />
      *  $config = &$cM->getConfiguration('sites::mysite','actions','permanentactions');
      *  </pre>
      *  Within classes inherited from coreObject, the __getConfiguration() wrapper can be used.
      *
      *  @param string $namespace namespace of the requested configuration (will be prefixed with "config::")
      *  @param string $context context of the configuration file
      *  @param string $configName the name of the configuration file
      *  @param bool $parseSubsections defines if subsections ("." notation) should be parsed as subsections
      *  @return Configuration $CfgObj | bool NULL configuration object or null in case of failure
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 02.04.2007 (An error is triggered if the config cannot be loaded)<br />
      *  Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
      */
      function &getConfiguration($namespace,$context,$configName,$parseSubsections = false){

         // calculate config hash
         $ConfigHash = md5($namespace.$context.$configName);

         // check if config exists
         if($this->configurationExists($namespace,$context,$configName) == true){

            // check if config is already loaded
            if(!isset($this->__Configurations[$ConfigHash])){

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
               $this->__Configurations[$ConfigHash] = $CfgObj;

             // end if
            }

          // end if
         }
         else{

            // retrieve environment configuration from Registry
            $Reg = &Singleton::getInstance('Registry');
            $Environment = $Reg->retrieve('apf::core','Environment');

            // trigger error
            trigger_error('[configurationManager->getConfiguration()] Requested configuration with name "'.$Environment.'_'.$configName.'.ini" cannot be loaded from namespace "'.$namespace.'" with context "'.$context.'"!',E_USER_ERROR);
            exit();

          // end else
         }

         return $this->__Configurations[$ConfigHash];

       // end function
      }


      /**
      *  @public
      *
      *  Checks, if a configuration file exists or not.
      *
      *  @param string $namespace namespace of the requested configuration
      *  @param string $context context of the configuration file
      *  @param string $configName the name of the configuration file
      *  @return bool $configurationExistent true | false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.02.2007<br />
      *  Version 0.2, 07.03.2007 (Renamed to configurationExists())<br />
      */
      function configurationExists($namespace,$context,$configName){

         if(file_exists($this->__getConfigurationFileName($namespace,$context,$configName))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Loads a configuration file.
      *
      *  @param string $namespace namespace of the requested configuration
      *  @param string $context context of the configuration file
      *  @param string $configName the name of the configuration file
      *  @return array $configuration | null configuration array or null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      *  Version 0.2, 03.02.2007 (Outsourced the file name generation)<br />
      *  Version 0.3, 07.03.2007<br />
      *  Version 0.4, 02.04.2007 (Removed else, because the existance is already checked before.)<br />
      */
      private function __loadConfiguration($namespace,$context,$configName){
         $configFile = $this->__getConfigurationFileName($namespace,$context,$configName);
         return parse_ini_file($configFile,true);
       // end function
      }


      /**
      *  @private
      *
      *  Setzt den ConfigFileName aus Namespace und ConfigName zusammen.<br />
      *
      *  @param string $namespace namespace of the requested configuration (will be prefixed with "config")
      *  @param string $context context of the configuration file
      *  @param string $configName the name of the configuration file
      *  @return string $ConfigurationFileName name of the configuration file name
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.02.2007<br />
      *  Version 0.2, 07.03.2007<br />
      *  Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
      */
      private function __getConfigurationFileName($namespace,$context,$configName){

         if(strlen($context) > 0){
            $path = str_replace('::','/',$namespace).'/'.str_replace('::','/',$context);
          // end if
         }
         else{
            $path = str_replace('::','/',$namespace);
          // end else
         }

         // build the file name
         $reg = &Singleton::getInstance('Registry');
         $env = $reg->retrieve('apf::core','Environment');
         return APPS__PATH.'/config/'.$path.'/'.$env.'_'.$configName.'.ini';

       // end function
      }


      /**
      *  @private
      *
      *  Parses the configuration file.
      *
      *  @param array $configuration configuration array created with the parse_ini_file function
      *  @return array $configurationArray the parsed configuration list
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
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
      *  @private
      *
      *  Parses subsections.
      *
      *  @param array $subsectionArray the configuration array
      *  @return array $parsedArray the parsed array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      *  Version 0.2, 19.04.2009 (Bugfix: Parsing subsections returned an empty array!)<br />
      */
      private function __parseSubsections($subsectionArray){

         $concatenatedArray = array();

         if(is_array($subsectionArray)){

            foreach($subsectionArray as $key => $value){
               $concatenatedArray = array_merge_recursive($concatenatedArray,$this->__generateSubArray($key,$value));
             // end foreach
            }

          // end if
         }
         else{
            trigger_error('[configurationManager::__parseSubsections()] Given value is not an array!',E_USER_ERROR);
          // end else
         }

         return $concatenatedArray;

       // end function
      }


      /**
      *  @private
      *
      *  Generates sub arrays from the dot notated directices.
      *
      *  @param string $key the current configuration directive possibly containg a dot
      *  @param array|string $value value of the offset specified with $key
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      private function __generateSubArray($key,$value){

         $SubArray = array();

         if(substr_count($key,$this->__NamespaceDelimiter) > 0){

            // search for the dot
            $DelPos = strpos($key,$this->__NamespaceDelimiter);

            // extract offset
            $Offset = substr($key,0,$DelPos);

            // create remaining string
            $RemainingString = substr($key,$DelPos + strlen($this->__NamespaceDelimiter),strlen($key));

            // generate new offset recursivly
            $SubArray[$Offset] = $this->__generateSubArray($RemainingString,$value);

          // end if
         }
         else{
            $SubArray[$key] = $value;
          // end els
         }

         return $SubArray;

       // end function
      }

    // end class
   }
?>