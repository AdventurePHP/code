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
namespace APF\core\configuration;

/**
 * Defines the scheme, a APF configuration object must have. Each configuration
 * provider can define it's own configuration instance based on this interface.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2010<br />
 * Version 0.2, 12.03.2015 (ID#224: introduced path expression capability for configuration values)<br />
 */
interface Configuration {

   /**
    * @const string Defines the separation character to define a multi-level value expression.
    */
   const SECTION_PATH_SEPARATOR = '.';

   /**
    * Returns the desired configuration key's value.
    * <p/>
    * Supports accessing values with a path expression (e.g. <em>Section.SubSection.AttributeName</em>).
    * Here, <em>AttributeName</em> is the name of the attribute to return but located in section
    * <em>SubSection</em> that in turn is defined withing section <em>Section</em>.
    *
    * @param string $name The name of the attribute.
    * @param string $defaultValue The default value.
    *
    * @return string The desired configuration value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getValue($name, $defaultValue = null);

   /**
    * Returns the desired configuration section.
    * <p/>
    * Supports accessing sections with a path expression (e.g. <em>Section.SubSection</em>).
    * Here, <em>SubSection</em> is the name of the section to return but located in section
    * <em>Section</em>.
    *
    * @param string $name The name of the attribute.
    *
    * @return Configuration The desired configuration section.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getSection($name);

   /**
    * Let's you check whether the current configuration object has
    * a section with the given name defined.
    * <p/>
    * Supports path expressions that are also applicable with the <em>getSection()</em> method.
    *
    * @param string $name The name of the section.
    *
    * @return bool <em>True</em> in case the section is defined and <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.03.2015<br />
    */
   public function hasSection($name);

   public function setValue($name, $value);

   public function setSection($name, Configuration $section);

   /**
    * Let's you check whether the current configuration object has
    * a value with the given name defined.
    * <p/>
    * Supports path expressions that are also applicable with the <em>getValue()</em> method.
    *
    * @param string $name The name of the attribute.
    *
    * @return bool <em>True</em> in case the attribute is defined and <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.03.2015<br />
    */
   public function hasValue($name);

   /**
    * Enumerates the names of the CURRENT instance' configuration keys.
    *
    * @return array The names of the config keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getValueNames();

   /**
    * Enumerates the names of the CURRENT instance' configuration sections.
    *
    * @return array The names of the section keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getSectionNames();

   /**
    * Removes the section with the given name from the configuration.
    * This can be used to manipulate configuration files for saving.
    *
    * @param string $name The name of the section to remove.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function removeSection($name);

   /**
    * Removes the value with the given name from the configuration.
    * This can be used to manipulate configuration files for saving.
    *
    * @param string $name The name of the value to remove.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function removeValue($name);

}
