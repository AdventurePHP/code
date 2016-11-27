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
namespace APF\core\pagecontroller;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationManager;
use APF\core\registry\Registry;
use APF\core\service\APFDIService;
use APF\core\service\APFService;
use APF\core\service\DIServiceManager;
use APF\core\service\ServiceManager;

/**
 * Represents the base objects of (nearly) all APF classes. Especially all GUI classes derive
 * from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 11.02.2007 (Added language and context)<br />
 * Version 0.3, 28.10.2008 (Added the serviceType member to indicate the service manager creation type)<br />
 * Version 0.4, 03.11.2008 (Added initializing values to some of the class members)<br />
 * Version 0.5, 10.04.2015 (ID#249: introduced constructor injection for object creation)<br />
 */
abstract class APFObject implements APFDIService {

   /**
    * The context of the current object within the application.
    *
    * @var string $context
    */
   protected $context = null;

   /**
    * The language of the current object within the application.
    *
    * @var string $language
    */
   protected $language = null;

   /**
    * Contains the service type, if the object was created with the ServiceManager.
    *
    * @var string $serviceType
    *
    * @since 0.3
    */
   protected $serviceType = null;

   /**
    * Stores the internal initialization status of the present APFDIService.
    *
    * @var bool $isInitialized
    *
    * @since 1.15
    */
   protected $isInitialized = false;

   public function markAsInitialized() {
      $this->isInitialized = true;
   }

   public function markAsPending() {
      $this->isInitialized = false;
   }

   public function isInitialized() {
      return $this->isInitialized;
   }

   public function getServiceType() {
      return $this->serviceType;
   }

   public function setServiceType($serviceType) {
      $this->serviceType = $serviceType;
   }

   /**
    * This method returns the current version of the present APF distribution. Please
    * note that this revision is no warranty that all files within your current
    * installation are subjected to the returned version number since the APF team
    * cannot guarantee consistency throughout manual patching or manual GIT updates.
    *
    * @return string The current APF version.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.02.2011<br />
    */
   public function getVersion() {
      return '3.3-GIT';
   }

   /**
    * Returns a service object, that is initialized by dependency injection.
    * For details see {@link DIServiceManager}.
    *
    * @param string $namespace The namespace of the service object definition.
    * @param string $name The name of the service object.
    *
    * @return APFDIService The pre-configured service object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.04.2009<br />
    */
   protected function &getDIServiceObject($namespace, $name) {
      return DIServiceManager::getServiceObject(
            $namespace, $name, $this->getContext(), $this->getLanguage());
   }

   public function getContext() {
      return $this->context;
   }

   public function setContext($context) {
      $this->context = $context;
   }

   public function getLanguage() {
      return $this->language;
   }

   public function setLanguage($lang) {
      $this->language = $lang;
   }

   /**
    * Returns a service object according to the current application context.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param array $arguments A list of constructor arguments to create the service instance with.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFService The desired service object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.03.2007<br />
    * Version 0.2, 08.03.2007 (Context is now taken from the current object)<br />
    * Version 0.3, 10.03.2007 (Method now is considered protected)<br />
    * Version 0.4, 22.04.2007 (Added language initialization of the service manager)<br />
    * Version 0.5, 24.02.2008 (Added the service type param)<br />
    * Version 0.6  21.11.2012 Jens Prangenberg <jprangenberg@mywebhd.com> (Added the instance id param)<br />
    */
   protected function &getServiceObject($class, array $arguments = [], $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      return ServiceManager::getServiceObject($class, $this->getContext(), $this->getLanguage(), $arguments, $type, $instanceId);
   }

   /**
    * Convenience method for loading a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    *
    * @return Configuration The desired configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.09.2010<br />
    */
   protected function getConfiguration($namespace, $name) {
      return ConfigurationManager::loadConfiguration(
            $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name);
   }

   /**
    * Convenience method for saving a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    * @param Configuration $config The configuration to save.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   protected function saveConfiguration($namespace, $name, Configuration $config) {
      ConfigurationManager::saveConfiguration(
            $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name, $config);
   }

   /**
    * Convenience method for deleting a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 27.07.2011<br />
    */
   protected function deleteConfiguration($namespace, $name) {
      ConfigurationManager::deleteConfiguration(
            $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name);
   }

}
