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
namespace APF\core\loader;

use InvalidArgumentException;

/**
 * Defines an APF class loader that is used to load classes, templates and config files.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.03.2013<br />
 */
interface ClassLoader {

   /**
    * Decision on what to do with none-vendor classes can be done by the ClassLoader itself!
    *
    * @param string $class The class to load.
    *
    * @throws InvalidArgumentException In case the class cannot be loaded.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.03.2013<br />
    */
   public function load(string $class);

   /**
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
