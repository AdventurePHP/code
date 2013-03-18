<?php
namespace APF\core\configuration;

/**
 * @package core::configuration
 * @class Configuration
 *
 * Defines the scheme, a APF configuration object must have. Each configuration
 * provider can define it's own configuration instance based on this interface.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2010<br />
 */
interface Configuration {

   /**
    * @public
    *
    * Returns the desired configuration key's value.
    *
    * @param string $name The name of the attribute.
    * @param string $defaultValue The default value.
    * @return string The desired configuration value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getValue($name, $defaultValue = null);

   /**
    * @public
    *
    * Returns the desired configuration section.
    *
    * @param string $name The name of the attribute.
    * @return Configuration The desired configuration section.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getSection($name);

   public function setValue($name, $value);

   public function setSection($name, Configuration $section);

   /**
    * @public
    *
    * Enumerates the names of the current configuration keys.
    *
    * @return array The names of the config keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getValueNames();

   /**
    * @public
    *
    * Enumerates the names of the current configuration sections.
    *
    * @return array The names of the section keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   public function getSectionNames();

   /**
    * @public
    *
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
    * @public
    *
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
