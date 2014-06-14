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
namespace APF\core\configuration\provider\ini;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\BaseConfigurationProvider;

/**
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
 *    Disable environment: in case $omitEnvironment is set to true, the environment is not
 *    used as sub part of the file name.
 * </li>
 * </ul>
 * In case configuration keys contain a dot (".") they are interpreted as sub-sections of the
 * current section. Providing a key named "conf.abc" and "conf.def" will generate a section
 * "conf" with the two keys "abc" and "def".
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2010<br />
 */
class IniConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   /**
    * The sub key delimiter.
    *
    * @var string $NAMESPACE_DELIMITER
    */
   private static $NAMESPACE_DELIMITER = '.';

   /**
    *
    *
    * @var int $groupDepth
    */
   protected $groupDepth = 1;

   /**
    * Use this method to to determine how many subsection should be grouped as Headers
    * when saving the configuration. Default is 1
    *
    * <code>
    * groupDepth of 1 will produce
    * [Section]
    * subsection.subsubsection.foo = "bar"
    *
    * groupDepth of 2 will produce
    * [Section]
    * [Section.subsection]
    * subsubsection.foo = "bar"
    *
    * groupDepth of 3 will produce the following configuration
    * [Section]
    * [Section.subsection.subsubsection]
    * foo = "bar"
    * ...
    * </code>
    *
    * @param int $depth
    *
    * @throws InvalidArgumentException in case the given value is less than 1
    */
   public function setGroupDepth($depth) {
      if ($depth < 1) {
         throw new \InvalidArgumentException('GroupDepth can only be set to a value greater then 0');
      }
      $this->groupDepth = $depth;
   }

   public function loadConfiguration($namespace, $context, $language, $environment, $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      if (file_exists($fileName)) {
         return $this->parseConfig(parse_ini_file($fileName, true));
      }

      if ($this->activateEnvironmentFallback && $environment !== 'DEFAULT') {
         return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
      }

      throw new ConfigurationException('[IniConfigurationProvider::loadConfiguration()] '
            . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
            . ' language "' . $language . '", environment "' . $environment . '", and name '
            . '"' . $name . '" cannot be loaded (file name: ' . $fileName . ')!', E_USER_ERROR);

   }

   /**
    * Creates the configuration representation for all sections.
    *
    * @param string[] $entries The sections of the current configuration.
    *
    * @return IniConfiguration The appropriate configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2010<br />
    */
   private function parseConfig($entries) {

      // prevent errors with foreach loop
      if ($entries === false) {
         return null;
      }
      $config = new IniConfiguration();

      foreach ($entries as $section => $subEntries) {
         if (strpos($section, self::$NAMESPACE_DELIMITER) === false) {
            $config->setSection($section, $this->parseSection($subEntries));
         } else {
            $sectionArray = explode(self::$NAMESPACE_DELIMITER, $section);
            $deepConfig = $config;
            $depth = count($sectionArray);
            for ($i = 0; $i < $depth - 1; $i++) {
               if ($deepConfig->getSection($sectionArray[$i]) === null) {
                  $deepConfig->setSection($sectionArray[$i], new IniConfiguration());
               }
               $deepConfig = $deepConfig->getSection($sectionArray[$i]);
            }
            $deepConfig->setSection($sectionArray[$i], $this->parseSection($subEntries));

         }
      }

      return $config;
   }

   /**
    * Creates the configuration representation of one single section.
    *
    * @param string[] $entries The entries of the current main section.
    *
    * @return IniConfiguration The configuration, that represents the applied entries.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2010<br />
    */
   private function parseSection(array $entries) {

      $config = new IniConfiguration();

      foreach ($entries as $entryName => $value) {
         if (strpos($entryName, self::$NAMESPACE_DELIMITER) === false) {
            $config->setValue($entryName, $value);
         } else {
            $sectionArray = explode(self::$NAMESPACE_DELIMITER, $entryName);
            $deepConfig = $config;
            $depth = count($sectionArray);
            for ($i = 0; $i < $depth - 1; $i++) {
               if ($deepConfig->getSection($sectionArray[$i]) === null) {
                  $deepConfig->setSection($sectionArray[$i], new IniConfiguration());
               }
               $deepConfig = $deepConfig->getSection($sectionArray[$i]);
            }
            $deepConfig->setValue($sectionArray[$i], $value);

         }
      }

      return $config;

   }

   public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      $buffer = '';
      foreach ($config->getSectionNames() as $sectionName) {

         $buffer .= $this->processSection($config->getSection($sectionName), $sectionName);
         $buffer .= PHP_EOL;
      }

      // create file path if necessary to avoid "No such file or directory" errors
      $this->createFilePath($fileName);

      if (file_put_contents($fileName, $buffer) === false) {
         throw new ConfigurationException('[IniConfigurationProvider::saveConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be saved! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

   private function processSection(Configuration $section, $currentName) {

      $depth = substr_count($currentName, self::$NAMESPACE_DELIMITER)+1;

      $buffer = '';

      $valueNames=$section->getValueNames();

      if(!empty($valueNames)|| $depth===1){
         $buffer .= '[' . $currentName . ']' . PHP_EOL;
      }

      foreach ($valueNames as $name) {
         $value = $section->getValue($name);

         if (is_array($value)) {
            foreach ($value as $element) {
               $buffer .= $name . '[] = "' . $element . '"' . PHP_EOL;
            }
         } else {
            $buffer .= $name . ' = "' . $value . '"' . PHP_EOL;
         }
      }


      foreach ($section->getSectionNames() as $sectionName) {
         if ($depth < $this->groupDepth) {
            $buffer .= $this->processSection($section->getSection($sectionName), $currentName . '.' . $sectionName);
         } else {
            if($depth !== 1){
               $buffer .= '[' . $currentName . ']' . PHP_EOL;
            }
            $buffer .= $this->generateComplexConfigValue($section->getSection($sectionName), $sectionName);
         }
      }

      return $buffer;
   }

   private function generateComplexConfigValue(Configuration $config, $currentName) {

      $buffer = '';

      // append simple values
      foreach ($config->getValueNames() as $name) {
         $value = $config->getValue($name);

         if (is_array($value)) {
            foreach ($value as $element) {
               $buffer .= $currentName . '.' . $name . '[] = "' . $element . '"' . PHP_EOL;
            }
         } else {
            $buffer .= $currentName . '.' . $name . ' = "' . $value . '"' . PHP_EOL;
         }
      }

      // append sections
      foreach ($config->getSectionNames() as $name) {
         $buffer .= $this->generateComplexConfigValue($config->getSection($name), $currentName . '.' . $name);
      }

      return $buffer;
   }

   public function deleteConfiguration($namespace, $context, $language, $environment, $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);
      if (unlink($fileName) === false) {
         throw new ConfigurationException('[IniConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be deleted! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

}
