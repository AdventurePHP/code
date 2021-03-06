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
namespace APF\core\configuration\provider\xml;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\BaseConfigurationProvider;
use SimpleXMLElement;

/**
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
 * <li>
 *    Disable environment: in case $omitEnvironment is set to true, the environment is not
 *    used as sub part of the file name.
 * </li>
 * </ul>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2010<br />
 * Version 0.2, 27.02.2012 (Throw an exception if xml isn't well-formed - Tobias Lückel [Megger])
 */
class XmlConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   public function loadConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      if (file_exists($fileName)) {

         $xml = simplexml_load_file($fileName);

         if ($xml === false) {
            throw new ConfigurationException('[XmlConfigurationProvider::loadConfiguration()] '
                  . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
                  . ' language "' . $language . '", environment "' . $environment . '", and name '
                  . '"' . $name . '" isn\'t well-formed (file name: ' . $fileName . ')!', E_USER_ERROR);
         }

         $config = new XmlConfiguration();

         foreach ($xml->xpath('section') as $section) {
            $this->parseSection($config, $section);
         }

         return $config;
      }

      if ($this->activateEnvironmentFallback && $environment !== 'DEFAULT') {
         return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
      }

      throw new ConfigurationException('[XmlConfigurationProvider::loadConfiguration()] '
            . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
            . ' language "' . $language . '", environment "' . $environment . '", and name '
            . '"' . $name . '" cannot be loaded (file name: ' . $fileName . ')!', E_USER_ERROR);
   }

   public function saveConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name, Configuration $config) {

      $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><configuration></configuration>');

      foreach ($config->getSectionNames() as $sectionName) {
         $this->processSection($xml, $config->getSection($sectionName), $sectionName);
      }

      // directly save file to gain performance and decrease memory usage
      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      // create file path if necessary to avoid "No such file or directory" errors
      $this->createFilePath($fileName);

      if ($xml->asXML($fileName) === false) {
         throw new ConfigurationException('[XmlConfigurationProvider::saveConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be saved! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

   /**
    * Transforms a given config section into the applied xml structure.
    *
    * @param SimpleXMLElement $xml The parent XML node.
    * @param Configuration $config The current section to translate.
    * @param string $name The name of the section to add.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   private function processSection(SimpleXMLElement &$xml, Configuration $config, string $name) {

      // create current section and append it to the parent node structure.
      $section = $xml->addChild('section');
      $section->addAttribute('name', $name);

      // add values
      foreach ($config->getValueNames() as $valueName) {
         $property = $section->addChild('property', $config->getValue($valueName));
         $property->addAttribute('name', $valueName);
      }

      // add sections recursively
      foreach ($config->getSectionNames() as $sectionName) {
         $this->processSection($section, $config->getSection($sectionName), $sectionName);
      }
   }

   /**
    * @param Configuration $config
    * @param SimpleXMLElement $section
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2010<br />
    */
   private function parseSection(Configuration $config, SimpleXMLElement $section) {
      $config->setSection((string)$section->attributes()->name, $this->parseXmlElement($section));
   }

   /**
    * @param SimpleXMLElement $element The current XML node.
    *
    * @return XmlConfiguration The configuration representing the current XML node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2010<br />
    */
   private function parseXmlElement(SimpleXMLElement $element) {

      $config = new XmlConfiguration();

      // parse properties
      foreach ($element->xpath('property') as $property) {
         $config->setValue((string)$property->attributes()->name, (string)$property);
      }

      // parse sections
      foreach ($element->xpath('section') as $section) {
         $this->parseSection($config, $section);
      }

      return $config;
   }

   public function deleteConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);
      if (unlink($fileName) === false) {
         throw new ConfigurationException('[XmlConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be deleted! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

}
