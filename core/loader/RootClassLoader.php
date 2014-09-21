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

use Exception;
use InvalidArgumentException;

/**
 * This is the root class loader of the APF. To add further class loaders, please use
 * <em>addLoader()</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.03.2013<br />
 */
class RootClassLoader {

   /**
    * The registered class loaders.
    *
    * @var ClassLoader[] $loaders
    */
   private static $loaders = array();

   /**
    * @param ClassLoader $loader A class loader to add to the list.
    */
   public static function addLoader(ClassLoader $loader) {
      self::$loaders[$loader->getVendorName()] = $loader;
   }

   /**
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
    * Returns a class loader by the applied vendor name.
    *
    * @param string $vendorName The name of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws InvalidArgumentException In case no class loader is found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public static function getLoaderByVendor($vendorName) {
      if (isset(self::$loaders[$vendorName])) {
         return self::$loaders[$vendorName];
      }
      throw new InvalidArgumentException('No class loader with vendor "' . $vendorName . '" registered!');
   }

   /**
    * Returns a class loader by the applied namespace.
    *
    * @param string $namespace The namespace of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws Exception In case no class loader is found.
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
    * Returns a class loader by the applied namespace.
    *
    * @param string $class The fully-qualified class of the desired class loader to get.
    *
    * @return ClassLoader The desired class loader.
    * @throws Exception In case no class loader is found.
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
