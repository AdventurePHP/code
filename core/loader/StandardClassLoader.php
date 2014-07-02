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
namespace APF\core\loader;

/**
 * @package APF\core\loader
 * @class StandardClassLoader
 *
 * Implements the standard APF class loader.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.03.2013<br />
 */
class StandardClassLoader implements ClassLoader {

   /**
    * @var string The vendor name this class loader is registered for.
    */
   private $vendorName;

   /**
    * @var string The root path to load classes/templates from.
    */
   private $rootPath;

   /**
    * @var string The root path to load configurations from.
    */
   private $configRootPath;

   /**
    * @public
    *
    * Constructs the standard class loader.
    *
    * @param string $vendorName The vendor name this class loader is registered for.
    * @param string $rootPath The root path to load classes/templates from.
    * @param string $configRootPath The configuration root path to load configurations from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    * Version 0.2, 19.05.2013 (Introduced config root path mechanism to allow separation of APF source and config files)<br />
    */
   public function __construct($vendorName, $rootPath, $configRootPath = null) {
      $this->vendorName = $vendorName;
      $this->rootPath = $rootPath;

      // By default the configuration files are located under the classes root path.
      // If desired, you can re-map the path to whatever you intend to structure your
      // project folder structure.
      if ($configRootPath === null) {
         $configRootPath = $rootPath;
      }

      $this->configRootPath = $configRootPath;
   }

   public function load($class) {

      if (strpos($class, $this->vendorName . '\\') !== false) {

         // create the complete and absolute file name
         $strippedClass = str_replace($this->vendorName . '\\', '', $class);
         $file = $this->rootPath . '/' . str_replace('\\', '/', $strippedClass) . '.php';

         // do a file_exists() instead of @include() because fatal errors within class loader lead to weired behaviour!
         // ID#165: don't throw an exception here in case the class/file is not found to allow other class loaders
         // to load the class (e.g. with different vendor)!
         if (file_exists($file)) {
            include($file);
         }
      }
   }

   public function getVendorName() {
      return $this->vendorName;
   }

   public function getRootPath() {
      return $this->rootPath;
   }

   public function getConfigurationRootPath() {
      return $this->configRootPath;
   }

   /**
    * @public
    *
    * Let's you define the vendor name this class loader is registered for.
    *
    * @param string $name The vendor name.
    *
    * @return StandardClassLoader This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function setVendorName($name) {
      $this->vendorName = $name;

      return $this;
   }

   /**
    * @public
    *
    * Let's you define the root path for load classes/configurations/templates.
    *
    * @param string $rootPath The root path to load classes/configurations/templates from.
    *
    * @return StandardClassLoader This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function setRootPath($rootPath) {
      $this->rootPath = $rootPath;

      return $this;
   }

}
