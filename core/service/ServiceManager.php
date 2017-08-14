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
namespace APF\core\service;

use APF\core\singleton\ApplicationSingleton;
use APF\core\singleton\SessionSingleton;
use APF\core\singleton\Singleton;
use InvalidArgumentException;
use ReflectionClass;

/**
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
    * Returns a service object according to the current application context.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param string $context The application context, the service object belongs to.
    * @param string $language The language, the service object has.
    * @param array $arguments A list of constructor arguments to create the service instance with.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    *
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
    * Version 0.9, 23.07.2013 (Added "APPLICATIONSINGLETON" object creation mechanism.)<br />
    * Version 1.0, 14.08.2017 (ID#317: prevent empty context to avoid scoping issues for instances of the same type but different configuration/application contexts)<br />
    */
   public static function &getServiceObject($class, $context, $language, array $arguments = [], $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {

      // ID#317:
      // (Re-)using objects/services with multiple configurations (defined by multiple contexts) within one application
      // will fail when not passing context information. This is because the resulting object/service instances will be
      // identical instead of associated to the relevant use-case.
      // To avoid such context clashes, objects/services must be created with proper context information!
      if (empty($context)) {
         throw new InvalidArgumentException('[ServiceManager::getServiceObject()] Instance/service creation of type "'
               . $class . '" failed due to missing/empty context information (' . $context
               . ')! Please provide appropriate context definition to avoid context clashes.');
      }

      // Introduce generated instance key to create services with respect to the context.
      // In 1.15, creating instances of the same service implementation within different contexts
      // resulted in equal instances instead of different ones.
      // In 2.0 language has been removed from the instance id since within multi-language applications
      // you want to re-use the instance throughout different languages!
      if ($instanceId === null) {
         $instanceId = $class . '|' . $context;
      }

      $service = null;
      switch ($type) {
         case APFService::SERVICE_TYPE_SINGLETON:
            $service = Singleton::getInstance($class, $arguments, $instanceId);
            break;
         case APFService::SERVICE_TYPE_SESSION_SINGLETON:
            $service = SessionSingleton::getInstance($class, $arguments, $instanceId);
            break;
         case APFService::SERVICE_TYPE_APPLICATION_SINGLETON:
            $service = ApplicationSingleton::getInstance($class, $arguments, $instanceId);
            break;
         case APFService::SERVICE_TYPE_NORMAL:
            if (count($arguments) > 0) {
               $service = (new ReflectionClass($class))->newInstanceArgs($arguments);
            } else {
               $service = new $class;
            }
            break;
         default:
            throw new InvalidArgumentException('[ServiceManager::getServiceObject()] The given type ('
                  . $type . ') is not supported. Please provide one out of "' . APFService::SERVICE_TYPE_SINGLETON
                  . '", "' . APFService::SERVICE_TYPE_SESSION_SINGLETON . '" or "' . APFService::SERVICE_TYPE_NORMAL
                  . '"', E_USER_WARNING);
      }

      // inject the basic set of information to the APF style service
      if ($service instanceof APFService) {
         $service->setContext($context);
         $service->setLanguage($language);
         $service->setServiceType($type);
      } else {
         throw new InvalidArgumentException('[ServiceManager::getServiceObject()] The precisely '
               . 'now created object (' . $class . ') does not implement the APFService interface! '
               . 'So context, language and service type cannot be set correctly!', E_USER_WARNING);
      }

      return $service;
   }

}
