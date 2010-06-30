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

   /**
    * @namespace modules::genericormapper::data
    * @class GenericORMapperDIConfiguration
    *
    * Represents a configuration service to be able to initialize the generic or mapper with
    * the <em>DIServiceManager</em>. In order to do so, a service section must be created
    * for this configuration that looks as follows:
    * <pre>
    * [GORM-CONFIG]
    * servicetype = "SINGLETON"
    * namespace = "modules::genericormapper::data"
    * class = "GenericORMapperDIConfiguration"
    * conf.namespace.method = "setConfigNamespace"
    * conf.namespace.value = "..."
    * conf.affix.method = "setConfigAffix"
    * conf.affix.value = "..."
    * conf.conn.method = "setConnectionName"
    * conf.conn.value = "..."
    * conf.debug.method = "setDebugMode"
    * conf.debug.value = "true|false"
    * </pre>
    * The definition of the debug mode is optional and set to false by default. It is strongly
    * recommended to not enable the debug mode on production environments due to heavy log file
    * traffic due to statement logging!
    * <p/>
    * To setup a GORM instance add the following to your service definition configuration:
    * <pre>
    * [GORM]
    * servicetype = "SESSIONSINGLETON"
    * namespace = "modules::genericormapper::data"
    * class = "GenericORRelationMapper"
    * init.configure.method = "initDI"
    * init.configure.namespace = "..."
    * init.configure.name =  "GORM-CONFIG"
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.06.2010<br />
    */
   final class GenericORMapperDIConfiguration extends APFObject {

      /**
       * @var string The configuration namespace of the main GORM configuration.
       */
      private $configNamespace;

      /**
       * @var string The configuration affix of the main GORM configuration.
       */
      private $configAffix;

      /**
       * @var string The database connection name.
       */
      private $connectionName;

      /**
       * @var boolean True, in case the database connection debug mode should be switched on, false otherwise.
       */
      private $debugMode = false;

      public function getConfigNamespace() {
         return $this->configNamespace;
      }

      public function setConfigNamespace($configNamespace) {
         $this->configNamespace = $configNamespace;
      }

      public function getConfigAffix() {
         return $this->configAffix;
      }

      public function setConfigAffix($configAffix) {
         $this->configAffix = $configAffix;
      }

      public function getConnectionName() {
         return $this->connectionName;
      }

      public function setConnectionName($connectionName) {
         $this->connectionName = $connectionName;
      }

      public function getDebugMode() {
         return $this->debugMode;
      }

      public function setDebugMode($debugMode) {
         if($debugMode == 'true'){
            $this->debugMode = true;
         }
      }

   }
?>