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
 * Defines the scheme, a APF configuration provider must have. A configuration
 * provider represents a configuration format (e.g. ini, xml, ...) and can be
 * added to the ConfigurationManager to support multiple formats at the same time.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2010<br />
 */
interface ConfigurationProvider {

   /**
    * Returns the configuration specified by the given params.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $context The current application's context.
    * @param string $language The current application's language.
    * @param string $environment The environment, the applications runs on.
    * @param string $name The name of the configuration to load including it's extension.
    *
    * @return Configuration The desired configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   public function loadConfiguration($namespace, $context, $language, $environment, $name);

   /**
    * Saves the configuration applied as an argument to the file specified by the given params.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $context The current application's context.
    * @param string $language The current application's language.
    * @param string $environment The environment, the applications runs on.
    * @param string $name The name of the configuration to load including it's extension.
    * @param Configuration $config The configuration to save.
    *
    * @throws ConfigurationException In case the file cannot be saved.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config);

   /**
    * Injects the file extension, the provider is registered with.
    *
    * @param string $extension The extension, the provider is registered with.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2010<br />
    */
   public function setExtension($extension);

   /**
    * Deletes the configuration specified by the given params.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $context The current application's context.
    * @param string $language The current application's language.
    * @param string $environment The environment, the applications runs on.
    * @param string $name The name of the configuration to delete including it's extension.
    *
    * @throws ConfigurationException In case the file cannot be deleted.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 27.07.2011<br />
    */
   public function deleteConfiguration($namespace, $context, $language, $environment, $name);

}
