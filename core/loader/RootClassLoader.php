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
    * @throws \InvalidArgumentException In case the class cannot be loaded.
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
    * Further, the root path is used to load templates files. This is because the APF
    * uses one addressing scheme for all elements. Please note, that template files
    * naturally do not have namespaces but the APF introduces them with this mechanism
    * for convenience and consistency reasons.
    *
    * @return string The root path of the class loader.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function getRootPath();

   /**
    * @public
    *
    * Returns the root path this class loader instance advices the ConfigurationProvider
    * to load the config files from.
    * <p/>
    * Please note that the APF uses one addressing scheme for all elements since configuration
    * files naturally do not have namespaces. Namespaces have been introduced for convenience
    * and consistency reasons.
    *
    * @return string The configuration root path of the class loader.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.05.2013<br />
    */
   public function getConfigurationRootPath();

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
    * @public
    * @static
    *
    * Main entry for all class loading activities. This method is registered with the
    * <em>spl_autoload_register()</em> function.
    *
    * @param string $class The fully-qualified class name to load.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function load($class) {
      foreach (self::$loaders as $loader) {
         $loader->load($class);
      }
   }

   /**
    * @public
    * @static
    *
    * Returns a class loader by the applied vendor name.
    *
    * @param string $vendorName The name of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws \InvalidArgumentException In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function getLoaderByVendor($vendorName) {
      if (isset(self::$loaders[$vendorName])) {
         return self::$loaders[$vendorName];
      }
      throw new \InvalidArgumentException('No class loader with vendor "' . $vendorName . '" registered!');
   }

   /**
    * @public
    * @static
    *
    * Returns a class loader by the applied namespace.
    *
    * @param string $namespace The namespace of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws \Exception In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function getLoaderByNamespace($namespace) {
      // we can use getVendor() here, because only the first section is of our interest!
      return self::getLoaderByVendor(self::getVendor($namespace));
   }

   /**
    * @public
    * @static
    *
    * Returns a class loader by the applied namespace.
    *
    * @param string $class The fully-qualified class of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws \Exception In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.04.2013<br />
    */
   public static function getLoaderByClass($class) {
      $namespace = self::getNamespace($class);
      if (self::isVendorOnlyNamespace($namespace)) {
         return self::getLoaderByVendor($namespace);
      } else {
         return self::getLoaderByNamespace($namespace);
      }
   }

   /**
    * @public
    * @static
    *
    * Determines the class name of a fully-qualified class for you.
    *
    * @param string $class Fully-qualified class name (e.g. <em>APF\core\loader\StandardClassLoader</em>).
    *
    * @return string The class name of the given class (e.g. <em>StandardClassLoader</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.04.2013<br />
    */
   public static function getClassName($class) {
      return substr($class, strrpos($class, '\\') + 1);
   }

   /**
    * @public
    * @static
    *
    * Determines the namespace of a fully-qualified class for you.
    *
    * @param string $class Fully-qualified class name (e.g. <em>APF\core\loader\StandardClassLoader</em>).
    *
    * @return string The class name of the given class (e.g. <em>APF\core\loader</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.04.2013<br />
    */
   public static function getNamespace($class) {
      return substr($class, 0, strrpos($class, '\\'));
   }

   /**
    * @public
    * @static
    *
    * Determines the namespace without the leading vendor of a fully-qualified class for you.
    *
    * @param string $class Fully-qualified class name (e.g. <em>APF\core\loader\StandardClassLoader</em>).
    *
    * @return string The class name without vendor of the given class (e.g. <em>core\loader</em>).
    *
    * @author Jan Wiese
    * @version
    * Version 0.1, 28.05.2013<br />
    */
   public static function getNamespaceWithoutVendor($class) {

      $start = strpos($class, '\\');
      $end = strrpos($class, '\\');

      return substr($class, ($start + 1), ($end - $start - 1)); // plus/minus one to strip leading and trailing slashes
   }

   /**
    * @public
    * @static
    *
    * Determines the vendor of a fully-qualified class for you.
    *
    * @param string $class Fully-qualified class name (e.g. <em>APF\core\loader\StandardClassLoader</em>).
    *
    * @return string The class name of the given class (e.g. <em>APF</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.04.2013<br />
    */
   public static function getVendor($class) {
      return substr($class, 0, strpos($class, '\\'));
   }

   /**
    * @public
    * @static
    *
    * Determines whether the given namespace only consists of a vendor.
    *
    * @param string $namespace A fully-qualified namespace (e.g. <em>APF\core\loader</em> or <em>APF</em>).
    *
    * @return bool <em>True</em> in case the namespace contains only the vendor declaration, <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.02.2014<br />
    */
   public static function isVendorOnlyNamespace($namespace) {
      return strpos($namespace, '\\') === false;
   }

}
