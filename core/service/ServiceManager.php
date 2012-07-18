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
import('core::singleton', 'SessionSingleton');
import('core::service', 'APFService');

/**
 * @package core::service
 * @class ServiceManager
 *
 * Provides a simple dependency injection container for objects created during application flow.
 * It initializes the service objects with the current context and language to be able to access
 * context and environment sensitive configuration at any place of your application. For details
 * see the service object configuration.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 07.03.2007<br />
 * Version 0.2, 22.04.2007 (Added language attribute)<br />
 * Version 0.3, 24.02.2008 (Added SESSIONSINGLETON feature)<br />
 * Version 0.4, 04.03.2011 (Refactored to static class to not have performance overhead.)<br />
 */
final class ServiceManager {

   /**
    * @public
    *
    * Returns a service object according to the current application context.
    *
    * @param string $namespace Namespace of the service object (currently ignored).
    * @param string $serviceName Name of the service object (=class name).
    * @param string $context The application context, the service object belongs to.
    * @param string $language The language, the service object has.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    * @return APFService The desired service object.
    * @throws InvalidArgumentException In case of invalid ServiceType or if requested service does not implement the APFService interface.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.03.2007<br />
    * Version 0.2, 17.03.2007 (Adjusted error messages)<br />
    * Version 0.3, 22.04.2007 (Language is now injected)<br />
    * Version 0.4, 24.02.2008 (Added SessionSingleton service type)<br />
    * Version 0.5, 25.02.2008 (Added performance optimization for the SessionSingleton objects)<br />
    * Version 0.6, 10.08.2009 (Added lazy import, so that the developer must not care about the inclusion of the component.)<br />
    * Version 0.7, 04.03.2011 (Refactored to static method; enhanced code)<br />
    * Version 0.8, 07.07.2012 Jan Wiese <jw-lighting@ewetel.net> (Corrected service retrieval to respect context and language each time.)<br />
    */
   public static function &getServiceObject($namespace, $serviceName, $context, $language, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {

      // include service object for convenience
      import($namespace, $serviceName);

      // Introduce generated instance key to create services with respect to context and language.
      // In 1.15, creating instances of the same service implementation within different contexts
      // resulted in equal instances instead of different ones.
      if ($instanceId === null) {
         $instanceId = $namespace . '::' . $serviceName . '|' . $context . '_' . $language;
      }

      $serviceObject = null;
      if ($type == APFService::SERVICE_TYPE_SINGLETON) {
         $serviceObject = &Singleton::getInstance($serviceName, $instanceId);
      } elseif ($type == APFService::SERVICE_TYPE_SESSION_SINGLETON) {
         $serviceObject = &SessionSingleton::getInstance($serviceName, $instanceId);
      } elseif ($type == APFService::SERVICE_TYPE_NORMAL) {
         $serviceObject = new $serviceName();
      } else {
         throw new InvalidArgumentException('[ServiceManager::getServiceObject()] The given type ('
               . $type . ') is not supported. Please provide one out of "' . APFService::SERVICE_TYPE_SINGLETON
               . '", "' . APFService::SERVICE_TYPE_SESSION_SINGLETON . '" or "' . APFService::SERVICE_TYPE_NORMAL
               . '"', E_USER_WARNING);
      }

      // inject the basic set of information to the APF style service
      if ($serviceObject instanceof APFService) {
         $serviceObject->setContext($context);
         $serviceObject->setLanguage($language);
         $serviceObject->setServiceType($type);
      } else {
         throw new InvalidArgumentException('[ServiceManager::getServiceObject()] The precisely '
               . 'now created object (' . $serviceName . ') does not implement the APFService interface! '
               . 'So the context, language and service type cannot be set correctly!', E_USER_WARNING);
      }

      return $serviceObject;
   }

   /**
    * @public
    *
    * Returns a service object, that is initialized with the given init param. The param itself
    * can be a primitive data type, an array or an object. Context and language are injected
    * as well.
    *
    * @param string $namespace The namespace of the service object's class (currently ignored).
    * @param string $serviceName Name of the service object (=class name).
    * @param string $context The application context, the service object belongs to.
    * @param string $language The language, the service object has.
    * @param string $initParam The initialization param for the service object.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    * @return APFService The desired service object.
    * @throws InvalidArgumentException In case the service object does not implement the APFService interface.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 16.05.2009 (Added check for non existing service object returned by getServiceObject()))<br />
    * Version 0.3, 04.03.2011 (Refactored to static method)<br />
    */
   public static function &getAndInitServiceObject($namespace, $serviceName, $context, $language, $initParam, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      $serviceObject = &self::getServiceObject($namespace, $serviceName, $context, $language, $type, $instanceId);
      $serviceObject->init($initParam);
      return $serviceObject;
   }

}
