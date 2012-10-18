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
 * @package modules::genericormapper::data
 * @class GenericORMapperDIMappingConfiguration
 *
 * Represents a configuration service to be able to add a further mapping configuration to
 * the generic or mapper with the <em>DIServiceManager</em>. In order to do so, a service
 * section must be created for this configuration that looks as follows:
 * <pre>
 * [GORM-CONFIG-ADDITIONAL-MAPPING]
 * servicetype = "SINGLETON"
 * namespace = "modules::genericormapper::data"
 * class = "GenericORMapperDIMappingConfiguration"
 * conf.namespace.method = "setConfigNamespace"
 * conf.namespace.value = "..."
 * conf.affix.method = "setConfigAffix"
 * conf.affix.value = "..."
 * </pre>
 * To enhance a GORM instance add the following to your service definition configuration:
 * <pre>
 * [GORM]
 * servicetype = "..."
 * namespace = "modules::genericormapper::data"
 * class = "GenericORRelationMapper"
 * ...
 * init.additionalrelation.method = "addDIMappingConfiguration"
 * init.additionalrelation.namespace = "..."
 * init.additionalrelation.name =  "GORM-CONFIG-ADDITIONAL-MAPPING"
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.06.2010<br />
 */
final class GenericORMapperDIMappingConfiguration extends APFObject {

   /**
    * @var string The configuration namespace of the additional GORM mapping configuration.
    */
   private $configNamespace;

   /**
    * @var string The configuration affix of the additional GORM mapping configuration.
    */
   private $configAffix;

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

}
