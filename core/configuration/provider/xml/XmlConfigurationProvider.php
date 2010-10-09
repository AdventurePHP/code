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

   import('core::configuration::provider', 'BaseConfigurationProvider');
   import('core::configuration::provider::xml', 'XmlConfiguration');

   /**
    * @package core::configuration::provider::ini
    * @class XmlConfigurationProvider
    *
    * Implements the configuration provider for the default APF xml format. The
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
    * </ul>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   class XmlConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

      public function loadConfiguration($namespace, $context, $language, $environment, $name) {
         
         $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

         if(file_exists($fileName)){

            $xml = simplexml_load_file($fileName);

            $config = new XmlConfiguration();

            foreach($xml->xpath('section') as $section){
               $this->parseSection($config, $section);
            }

            return $config;
            
         } else {
            if($this->activateEnvironmentFallback){
               return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
            } else {
               throw new ConfigurationException('[XmlConfigurationProvider::loadConfiguration()] '
                       .'Configuration with namepace "'.$namespace.'", context "'.$context.'", '
                       .' language "'.$language.'", environment "'.$environment.'", and name '
                       .'"'.$name.'" cannot be loaded!', E_USER_ERROR);
            }
         }

      }

      public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {
         throw new ConfigurationException('This functionality is not implemented, yet!');
      }

      /**
       * @private
       *
       * @param Configuration $config
       * @param SimpleXMLElement $section
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.10.2010<br />
       */
      private function parseSection(Configuration $config, SimpleXMLElement $section){
         $config->setSection((string)$section->attributes()->name, $this->parseXmlElement($section));
      }

      /**
       * @private
       *
       * @param SimpleXMLElement $element The current XML node.
       * @return XmlConfiguration The configuration representing the current XML node.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.10.2010<br />
       */
      private function parseXmlElement(SimpleXMLElement $element){

         $config = new XmlConfiguration();

         // parse properties
         foreach($element->xpath('property') as $property){
            $config->setValue((string)$property->attributes()->name, (string)$property);
         }

         // parse sections
         foreach($element->xpath('section') as $section){
            $this->parseSection($config, $section);
         }

         return $config;

      }

      protected function getConfigFileExtension() {
         return 'xml';
      }

   }
?>