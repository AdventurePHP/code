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

use APF\core\benchmark\BenchmarkTimer;
use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationManager;
use APF\core\logging\entry\SimpleLogEntry;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use InvalidArgumentException;

/**
 * The DIServiceManager provides a dependency injection container for
 * creating pre-configured service objects similar to the {@link ServiceManager}.
 * It provides constructor injection and method injection.
 * <p/>
 * PLEASE NOTE: with constructor injection, context and language are not yet available,
 * so you cannot load configurations or the like that are based on context and/or language.
 * <p/>
 * Services must derive from the {@link APFObject} class to be able to inject the context
 * and language of the current instance of the page or front controller. For convenience,
 * the {@link APFObject} contains the getDIServiceObject() method. Usage:
 * <br />
 * <pre>$initializedServiceObject =
 *             &$this->getDIServiceObject(
 *                        'VENDOR\namespace\of\the\configuration',
 *                        'ServiceName'
 *             );</pre>
 * <br />
 * Further, the DIServiceManager includes additional config param injection.
 * This means, that the desired service cannot only be configured using other
 * service objects but also by plain parameters.
 * <p/>
 * The configuration is done by a file located under the desired namespace provided
 * as the first argument to the getServiceObject() method. It is named after the
 * APF configuration file naming convention and the file-body must be "serviceobjects".
 * The scheme of the configuration file is as follows:
 * <pre>[&lt;ServiceObjectName&gt;]
 * servicetype = "SINGLETON|SESSIONSINGLETON|NORMAL|CACHED"
 * class = "VENDOR\namespace\to\the\service\object\class\NameOfTheClass"
 *
 * constructor.{construct-key-one}.value = "constructor value"
 * constructor.{construct-key-two}.namespace = "VENDOR\namespace\of\the\service\object\to\inject"
 * constructor.{construct-key-two}.name = "bar-service"
 *
 * init.{init-key}.method = "nameOfTheFirstInjectionMethod"
 * init.{init-key}.namespace = "VENDOR\namespace\of\the\service\object\to\inject"
 * init.{init-key}.name = "NameOfTheServiceToInject"
 *
 * conf.{conf-key}.method = "nameOfTheConfigParamInjectionMethod"
 * conf.{conf-key}.value = "config value"</pre>
 * <p/>
 * The name of the injected object references a configuration section within
 * the configuration file located under the defined namespace. "{init-key}"
 * indicates the key of the injected object. This makes possible multiple
 * injections. Further, "{conf-key}" indicates one section of plain configuration
 * param injection.
 * <p/>
 * Using the constructor injection option you are able to inject any number of
 * constructor arguments. Each injection - either scalar values using the
 * <em>value</em> keyword or other services using <em>namespace</em> and <em>name</em>
 * as reference - must be defined with a unique key indicated with "{construct-key-one}"
 * and "{construct-key-two}" above. The order of the items must comply with the
 * order of arguments the service expects within the __construct() method.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 18.04.2009<br />
 * Version 0.2, 19.04.2009 (Finished implementation)<br />
 * Version 0.3, 07.03.2011 (Refactored to static component to increase performance and accessibility)<br />
 * Version 0.4, 10.07.2012 Jan Wiese (Added configuration and service type cache)<br />
 * Version 0.5, 10.04.2015 (ID#249: introduced constructor injection for object creation)<br />
 */
final class DIServiceManager {

   /**
    * Defines the configuration extension and thus the file type to be used for DI service configurations.
    *
    * @var string $configurationExtension
    */
   public static $configurationExtension = 'ini';
   /**
    * Injection call cache to avoid circular injections.
    *
    * @var array $INJECTION_CALL_CACHE
    */
   private static $INJECTION_CALL_CACHE = [];
   /**
    * Contains the service objects, that were already configured.
    *
    * @var array $SERVICE_OBJECT_CACHE
    */
   private static $SERVICE_OBJECT_CACHE = [];
   /**
    * Contains the configuration of already delivered services
    *
    * @var array $SERVICE_CONFIG_CACHE
    */
   private static $SERVICE_CONFIG_CACHE = [];
   /**
    * Contains the cached service types
    *
    * @var array $SERVICE_TYPE_CACHE
    */
   private static $SERVICE_TYPE_CACHE = [];

   /**
    * Returns the initialized service object.
    *
    * @param string $configNamespace The namespace of the service object.
    * @param string $sectionName The name of the desired service object.
    * @param string $context The context of the current application.
    * @param string $language The language of the current application.
    *
    * @return APFDIService The pre-configured service object.
    * @throws ConfigurationException In case the requested service is not existent.
    * @throws InvalidArgumentException In case of injection issues.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.04.2009<br />
    * Version 0.2, 24.08.2011 (Added "setupmethod" functionality)<br/>
    * Version 0.3, 07.07.2012 Jan Wiese (Corrected service retrieval to respect context and language each time.<br />
    *                         Introduced CACHED service type to retrieve a NORMAL instance from the cache and thus gain performance for none-singletons.)<br />
    * Version 0.4, 10.07.2012 Jan Wiese (Introduced configuration cache to gain performance)<br />
    * Version 0.5, 10.07.2012 Jan Wiese (Improvements in code quality and removed bugs from v0.3/v0.4)<br />
    */
   public static function &getServiceObject($configNamespace, $sectionName, $context, $language) {

      // build cache key. because configuration-file path includes context, include context (and language) in cache key
      // In 2.0 language has been removed from the instance id since within multi-language applications
      // you want to re-use the instance throughout different languages!
      $cacheKey = $configNamespace . '|' . $sectionName . '|' . $context;

      // Check, whether service object was created before. If yes, deliver it from cache for all services types except NORMAL.
      // Do not cache ServiceType 'NORMAL' because we want to have different instances!
      if (isset(self::$SERVICE_OBJECT_CACHE[$cacheKey]) && self::$SERVICE_TYPE_CACHE[$cacheKey] != APFService::SERVICE_TYPE_NORMAL) {
         return self::$SERVICE_OBJECT_CACHE[$cacheKey];
      }

      // Invoke benchmarker. Suppress warning for already started timers with circular calls!
      // Suppressing is here done by a dirty '@', because we will run into an error anyway.
      $t = &Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $benchId = 'DIServiceManager::getServiceObject(' . $configNamespace . ',' . $sectionName . ',' . $context . ',' . $language . ')';
      @$t->start($benchId);

      // Get config to determine, which object to create.
      $config = self::getServiceConfiguration($configNamespace, $context, $language, $cacheKey);

      if (!$config->hasSection($sectionName)) {
         throw new ConfigurationException('[DIServiceManager::getServiceObject()] Service object configuration with '
               . 'name "' . $sectionName . '" cannot be found within namespace "'
               . $configNamespace . '"! Please double-check your setup.', E_USER_ERROR);
      }

      $section = $config->getSection($sectionName);

      // check, whether the section contains the basic directives and read/write service type cache
      if (isset(self::$SERVICE_TYPE_CACHE[$cacheKey])) {
         $serviceType = self::$SERVICE_TYPE_CACHE[$cacheKey];
      } else {
         $serviceType = $section->getValue('servicetype');
         self::$SERVICE_TYPE_CACHE[$cacheKey] = $serviceType;
      }

      $class = $section->getValue('class');

      // The behaviour of service types CACHED and NORMAL is equal in the following, thus remapping it.
      if ($serviceType == APFService::SERVICE_TYPE_CACHED) {
         $serviceType = APFService::SERVICE_TYPE_NORMAL;
      }

      // Check if configuration section was complete. If not throw an exception to fail fast.
      if ($serviceType === null || $class === null) {
         throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the service object "' .
               $sectionName . '" from namespace "' . $configNamespace . '" cannot be accomplished, due to missing
               or incorrect configuration! Please revise the configuration file and consult the manual!',
               E_USER_ERROR);
      }

      // Create the service object with use of the "normal" service manager. Perhaps, this
      // may run into problems, because we have to ensure, that the singleton objects are
      // only treated once by the injection mechanism!
      // But: if we constitute, that the injected service objects are often also singletons
      // and the DIServiceManager caches the created service objects within a singleton cache,
      // this is no problem. Hence, the injected instance is then only one time constructed.

      // ID#249: as of 3.0 you are also able to inject dependent configuration and services via constructor.
      // This replaces the former getAndInitServiceObject() method of the ServiceManager with a more elegant
      // way. Configuration allows any number of constructor arguments as string, array, or other services.
      $arguments = [];
      if ($section->hasSection('construct')) {

         $constructorArguments = $section->getSection('construct');

         foreach ($constructorArguments->getSectionNames() as $argumentKey) {

            $directive = $constructorArguments->getSection($argumentKey);

            // be aware of the params needed for injection
            $value = $directive->getValue('value');
            $namespace = $directive->getValue('namespace');
            $name = $directive->getValue('name');
            if ($value === null && ($namespace === null || $name === null)) {
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Construction of the'
                     . ' service object "' . $sectionName . '" cannot be accomplished, due to'
                     . ' incorrect constructor injection configuration! Please revise the "' . $argumentKey
                     . '" sub section and consult the manual!', E_USER_ERROR);
            }

            // Simple value argument (string)
            if ($directive->hasValue('value')) {
               $arguments[] = $value;
            } else {
               // complex injection with another service
               $arguments[] = &self::getServiceObject($namespace, $name, $context, $language);
            }
         }
      }

      /* @var $serviceObject APFDIService */
      $serviceObject = &ServiceManager::getServiceObject($class, $context, $language, $arguments, $serviceType, $cacheKey);

      // do param injection (static configuration)
      if ($section->hasSection('conf')) {

         $cfTasks = $section->getSection('conf');

         foreach ($cfTasks->getSectionNames() as $initKey) {

            $directive = $cfTasks->getSection($initKey);

            // be aware of the params needed for injection
            $method = $directive->getValue('method');
            if ($method === null) {
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the'
                     . ' service object "' . $sectionName . '" cannot be accomplished, due to'
                     . ' incorrect configuration! Please revise the "' . $initKey . '" sub section and'
                     . ' consult the manual!', E_USER_ERROR);
            }

            // check, if method exists to avoid fatal errors
            if (!method_exists($serviceObject, $method)) {
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Injection of'
                     . ' configuration value "' . $directive->getValue('value') . '" cannot be accomplished'
                     . ' to service object "' . $class . '"! Method ' . $method . '() is not implemented!',
                     E_USER_ERROR);
            }

            if (($value = $directive->getValue('value')) !== null) {
               $serviceObject->$method($value);
            } else {
               if (!$directive->hasSection('value')) {
                  throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the'
                        . ' service object "' . $sectionName . '" cannot be accomplished, due to'
                        . ' missing value(s) for method ' . $method . '()! Please revise the "' . $initKey . '" sub section and'
                        . ' consult the manual!', E_USER_ERROR);
               }

               $cfSubSection = $directive->getSection('value');

               $values = [];
               foreach ($cfSubSection->getValueNames() as $valueName) {
                  $values[] = $cfSubSection->getValue($valueName);
               }
               call_user_func_array([$serviceObject, $method], $values);
            }
         }
      }

      // do service object injection
      if ($section->hasSection('init')) {

         $miTasks = $section->getSection('init');

         foreach ($miTasks->getSectionNames() as $initKey) {

            $directive = $miTasks->getSection($initKey);

            // be aware of the params needed for injection
            $method = $directive->getValue('method');
            $namespace = $directive->getValue('namespace');
            $name = $directive->getValue('name');
            if ($method === null || $namespace === null || $name === null) {
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the service object "' .
                     $sectionName . '" cannot be accomplished, due to incorrect configuration! Please revise the "' . $initKey .
                     '" sub section and consult the manual!',
                     E_USER_ERROR);
            }

            // check for circular injection
            $injectionKey = $namespace . '::' . $class . '[' . $serviceType . ']' . ' injected with ' .
                  $method . '(' . $namespace . '::' . $name . ')';

            // TODO why do we accept loops for normal services?
            if (isset(self::$INJECTION_CALL_CACHE[$injectionKey]) && $serviceType != APFService::SERVICE_TYPE_NORMAL) {

               // append error to log to provide debugging information
               $log = &Singleton::getInstance(Logger::class);
               /* @var $log Logger */
               $instructions = '';
               foreach (self::$INJECTION_CALL_CACHE as $injectionInstruction => $DUMMY) {
                  $instructions .= PHP_EOL . $injectionInstruction;
               }

               $log->addEntry(
                     new SimpleLogEntry(
                     // use the configured log target to allow custom configuration of APF-internal log statements
                     // to be written to a custom file/location
                           Registry::retrieve('APF\core', 'InternalLogTarget'),
                           '[DIServiceManager::getServiceObject()] Injection stack trace: ' . $instructions,
                           LogEntry::SEVERITY_TRACE
                     )
               );

               // print note with shorted information
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Detected circular injection! ' .
                     'Class "' . $class . '" from namespace "' . $namespace . '" with service type "' . $serviceType .
                     '" was already configured with service object "' . $name . '" from namespace "' .
                     $namespace . '"! Full stack trace can be taken from the logfile!', E_USER_ERROR);
            } else {

               // add the current run to the recursion detection array
               self::$INJECTION_CALL_CACHE[$injectionKey] = true;

               // get the dependent service object
               $miObject = &self::getServiceObject($namespace, $name, $context, $language);

               // inject the current service object with the created one
               if (method_exists($serviceObject, $method)) {
                  $serviceObject->$method($miObject);
               } else {
                  throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Injection of service object "' . $name .
                        '" from namespace "' . $namespace . '" cannot be accomplished to service object "' .
                        $class . '" from namespace "' . $namespace . '"! Method ' . $method . '() is not implemented!',
                        E_USER_ERROR);
               }
            }
         }
      }

      // Often, there you have a services that depends on several other services (e.g. database connection). Thus,
      // you are forced to initialize your service using these components. To ease such cases, you may specify a
      // generic method within the "setupmethod" attribute. The DIServiceManager calls this method at the end of
      // the initialization process and you can initialize your service without being dependent on the user's
      // order of configuration parameters.
      // in order to not execute the setup method on every request, check the initialization status of the service
      // object before. this mechanism can be used for re-initialization on __wakeup() in case the property is
      // set to false (=reinitialization after session wake-up).

      $setupMethod = $section->getValue('setupmethod');
      if (!empty($setupMethod)) {
         if (!$serviceObject->isInitialized()) {
            if (method_exists($serviceObject, $setupMethod)) {
               $serviceObject->$setupMethod();
            } else {
               throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Custom service object setup '
                     . 'method "' . $setupMethod . '()" is not implemented for given type "'
                     . get_class($serviceObject) . '"! Please double-check your configuration '
                     . 'for service "' . $sectionName . '" from namespace "' . $configNamespace . '."',
                     E_USER_ERROR);
            }
         }
      }

      $t->stop($benchId);

      // add service object to cache and return it
      self::$SERVICE_OBJECT_CACHE[$cacheKey] = $serviceObject;

      return self::$SERVICE_OBJECT_CACHE[$cacheKey];
   }

   /**
    * Loads the service configuration.
    *
    * @param string $configNamespace The namespace of the service (a.k.a. config namespace).
    * @param string $context The context of the current application.
    * @param string $language The language of the current application.
    * @param string $cacheKey The cache key to check/find configuration in configuration cache
    *
    * @return Configuration The appropriate configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2010<br />
    * Version 0.2, 15.07.2012 Jan Wiese (Introduced configuration cache and $cacheKey parameter)<br />
    */
   private static function getServiceConfiguration($configNamespace, $context, $language, $cacheKey) {

      // return cached version as much as possible to gain performance
      if (isset(self::$SERVICE_CONFIG_CACHE[$cacheKey])) {
         return self::$SERVICE_CONFIG_CACHE[$cacheKey];
      }

      self::$SERVICE_CONFIG_CACHE[$cacheKey] = ConfigurationManager::loadConfiguration(
            $configNamespace,
            $context,
            $language,
            Registry::retrieve('APF\core', 'Environment'),
            'serviceobjects.' . self::$configurationExtension);

      return self::$SERVICE_CONFIG_CACHE[$cacheKey];
   }
}
