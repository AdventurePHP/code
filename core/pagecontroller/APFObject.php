<?php
namespace APF\core\pagecontroller;

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
use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationManager;
use APF\core\registry\Registry;
use APF\core\service\APFDIService;
use APF\core\service\APFService;
use APF\core\service\DIServiceManager;
use APF\core\service\ServiceManager;

/**
 * @package APF\core\pagecontroller
 * @class APFObject
 * @abstract
 *
 * Represents the base objects of (nearly) all APF classes. Especially all GUI classes derive
 * from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 11.02.2007 (Added language and context)<br />
 * Version 0.3, 28.10.2008 (Added the serviceType member to indicate the service manager creation type)<br />
 * Version 0.4, 03.11.2008 (Added initializing values to some of the class members)<br />
 */
abstract class APFObject implements APFDIService {

   /**
    * @protected
    * @var string The context of the current object within the application.
    */
   protected $context = null;

   /**
    * @protected
    * @var string The language of the current object within the application.
    */
   protected $language = 'de';

   /**
    * @since 0.3
    * @protected
    * @var string Contains the service type, if the object was created with the ServiceManager.
    */
   protected $serviceType = null;

   /**
    * @since 1.15
    * @protected
    * @var bool Stores the internal initialization status of the present APFDIService.
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

   public function init($initParam) {
   }

   public function setContext($context) {
      $this->context = $context;
   }

   public function getContext() {
      return $this->context;
   }

   public function setLanguage($lang) {
      $this->language = $lang;
   }

   public function getLanguage() {
      return $this->language;
   }

   public function setServiceType($serviceType) {
      $this->serviceType = $serviceType;
   }

   public function getServiceType() {
      return $this->serviceType;
   }

   /**
    * @public
    *
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
      return '2.2-GIT';
   }

   /**
    * @protected
    *
    * Returns a service object, that is initialized by dependency injection.
    * For details see {@link DIServiceManager}.
    *
    * @param string $namespace The namespace of the service object definition.
    * @param string $name The name of the service object.
    *
    * @return APFObject The pre-configured service object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.04.2009<br />
    */
   protected function &getDIServiceObject($namespace, $name) {
      return DIServiceManager::getServiceObject(
            $namespace, $name, $this->getContext(), $this->getLanguage());
   }

   /**
    * @protected
    *
    * Returns a service object according to the current application context.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFObject The desired service object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.03.2007<br />
    * Version 0.2, 08.03.2007 (Context is now taken from the current object)<br />
    * Version 0.3, 10.03.2007 (Method now is considered protected)<br />
    * Version 0.4, 22.04.2007 (Added language initialization of the service manager)<br />
    * Version 0.5, 24.02.2008 (Added the service type param)<br />
    * Version 0.6  21.11.2012 Jens Prangenberg <jprangenberg@mywebhd.com> (Added the instanceid param)<br />
    */
   protected function &getServiceObject($class, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      return ServiceManager::getServiceObject($class, $this->getContext(), $this->getLanguage(), $type, $instanceId);
   }

   /**
    * @protected
    *
    * Returns a initialized service object according to the current application context.
    *
    * @deprecated Please use getServiceObject() applying the init param to dedicated methods or the DIServiceManager instead.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param string $initParam The initialization parameter.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFObject The desired service object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 22.04.2007 (Added language initialization of the service manager)<br />
    * Version 0.3, 24.02.2008 (Added the service type param)<br />
    * Version 0.4  21.11.2012 Jens Prangenberg <jprangenberg@mywebhd.com> (Added the instanceid param)<br />
    */
   protected function &getAndInitServiceObject($class, $initParam, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      return ServiceManager::getAndInitServiceObject($class, $this->getContext(), $this->getLanguage(), $initParam, $type, $instanceId);
   }

   /**
    * @protected
    *
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
    * @protected
    *
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
    * @protected
    *
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

   /**
    * @protected
    *
    * Creates a string representation of the given attributes list, using a
    * white list to especially include attributes.
    *
    * @param array $attributes The list of attributes to convert to an xml string.
    * @param array $whiteList The list of attributes, the string may contain.
    *
    * @return string The xml attributes string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010 (Replaced old implementation with the white list feature.)<br />
    * Version 0.2, 27.11.2013 (Added default data-* attribute support to ease white list maintenance)<br />
    */
   protected function getAttributesAsString(array $attributes, array $whiteList = array()) {

      $attributeParts = array();

      // process white list entries only, when attribute is given
      // code duplication is done here due to performance reasons!!!
      $charset = Registry::retrieve('APF\core', 'Charset');
      if (count($whiteList) > 0) {
         foreach ($attributes as $name => $value) {
            // allow "data-*" attributes by default to not deal with complicated white list configuration
            if (strpos($name, 'data-') !== false || in_array($name, $whiteList)) {
               $attributeParts[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
            }
         }
      } else {
         foreach ($attributes as $name => $value) {
            $attributeParts[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
         }
      }

      return implode(' ', $attributeParts);
   }

}