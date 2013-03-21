<?php
namespace APF\core\loader;

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
use APF\core\pagecontroller\IncludeException;

/**
 * @package APF\core\loader
 * @class ClassLoader
 *
 * Defines an APF class loader that is used to load classes, templates and config files.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.03.2013<br />
 */
interface ClassLoader {

   /**
    * @public
    *
    * Decision on what to do with none-vendor classes can be done by the ClassLoader itself!
    *
    * @param string $class The class to load.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function load($class);

   /**
    * @public
    *
    * Returns the vendor name the class loader represents.
    *
    * @return string The name of the vendor the class loader is attending to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function getVendorName();

   /**
    * @public
    *
    * Returns the root path this class loader instance uses to load PHP classes.
    * <p/>
    * Further, tha root path is used to load templates and configuration files as well.
    * This is because the APF uses one addressing scheme for all elements. Please note,
    * that templates and configuration files naturally do not have namespaces but the
    * APF introduces them with this mechanism for convenience and consistency reasons.
    *
    * @return string The root path of the class loader.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function getRootPath();

}

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
    * @var string The root path to load classes/configurations/templates from.
    */
   private $rootPath;

   /**
    * @public
    *
    * Constructs the standard class loader.
    *
    * @param string $vendorName The vendor name this class loader is registered for.
    * @param string $rootPath The root path to load classes/configurations/templates from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function __construct($vendorName, $rootPath) {
      $this->vendorName = $vendorName;
      $this->rootPath = $rootPath;
   }

   public function load($class) {

      if (strpos($class, $this->vendorName . '\\') !== false) {

         // create the complete and absolute file name
         $strippedClass = str_replace($this->vendorName . '\\', '', $class);
         $file = $this->rootPath . '/' . str_replace('\\', '/', $strippedClass) . '.php';

         // do a file_exists() instead of @include() because fatal errors must not be caught here (e.g. class not found)!
         if (file_exists($file)) {
            include($file);
         } else {
            throw new IncludeException('[StandardClassLoader::load()] Loading class "'
                  . $class . '" filed since file "' . $file . '" cannot be loaded!', E_USER_ERROR);
         }
      }
   }

   public function getVendorName() {
      return $this->vendorName;
   }

   public function getRootPath() {
      return $this->rootPath;
   }

   /**
    * @public
    *
    * Let's you define the vendor name this class loader is registered for.
    *
    * @param string $name The vendor name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function setVendorName($name) {
      $this->vendorName = $name;
   }

   /**
    * @public
    *
    * Let's you define the root path for load classes/configurations/templates.
    *
    * @param string $rootPath The root path to load classes/configurations/templates from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function setRootPath($rootPath) {
      $this->rootPath = $rootPath;
   }

}

/**
 * @package APF\core\loader
 * @class RootClassLoader
 *
 * This is the root class loader of the APF. To add further class loaders, please use
 * <em>addLoader()</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.03.2013<br />
 */
class RootClassLoader {

   /**
    * @var ClassLoader[] The registered class loaders.
    */
   private static $loaders = array();

   /**
    * @param ClassLoader $loader A class loader to add to the list.
    */
   public static function addLoader(ClassLoader $loader) {
      self::$loaders[$loader->getVendorName()] = $loader;
   }

   /**
    * @param string $class The fully-qualified class name to load.
    */
   public static function load($class) {
      foreach (self::$loaders as $loader) {
         $loader->load($class);
      }
   }

   /**
    * @public
    *
    * Returns a class loader by the applied vendor name.
    *
    * @param string $vendorName The name of the desired class loader to get.
    * @return ClassLoader The desired class loader.
    * @throws \Exception In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function getLoaderByVendor($vendorName) {
      if (isset(self::$loaders[$vendorName])) {
         return self::$loaders[$vendorName];
      }
      throw new \Exception('No class loader with vendor "' . $vendorName . '" registered!');
   }

   /**
    * @public
    *
    * Returns a class loader by the applied namespace.
    *
    * @param string $namespace The namespace of the desired class loader to get.
    * @return ClassLoader The desired class loader.
    * @throws \Exception In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function getLoaderByNamespace($namespace) {
      return self::getLoaderByVendor(substr($namespace, 0, strpos($namespace, '\\')));
   }

}
