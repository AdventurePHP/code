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
    * The DIServiceManager provides a dependency injection container for
    * creating preconfigured service objects similar to the {@link ServiceManager}.
    * It provides only method injection, because the APF objects are already injected
    * the current language and context. Introducing constructor injection would
    * lead to the situation, that the service objects could not be created
    * in SINGLETON or SESSIONSINGLETON mode with the generic implementations.
    * Please note, that a service object must derive from the {@link APFObject} class,
    * to be able to inject the context and language of the current instance of
    * the page or front controller. For convenience, the {@link APFObject} contains the
    * __getDIServiceObject() method. Usage:
    * <br />
    * <pre>$initializedServiceObject =
    *             &$this->__getDIServiceObject(
    *                        'namespace::of::the::configuration',
    *                        'ServiceObjectName'
    *             );</pre>
    * <br />
    * Further, the DIServiceManager includes additional config param injection.
    * This means, that the desired service cannot only be configured using other
    * service objects but also by plain parameters.
    * <br />
    * <br />
    * Configuration:
    * The configuration is done by a ini file located under the desired
    * namespace provided as the first argument to the getServiceObject()
    * method. It is named after the APF configuration file naming convention
    * and the filebody must be "serviceobjects". The scheme of the configuration
    * file looks as follows:
    * <pre>[&lt;ServiceObjectName&gt;]
    * servicetype = "SINGLETON|SESSIONSINGLETON|NORMAL"
    * namespace = "namespace::to::the::service::object::class"
    * class = "BothNameOfTheClassAndTheFile"
    *
    * init.&lt;foo&gt;.method = "nameOfTheFirstInjectionMethod"
    * init.&lt;foo&gt;.namespace = "namespace::of::the::service::object::to::inject"
    * init.&lt;foo&gt;.name = "NameOfTheServiceObjectToInject"
    * ...
    * conf.&lt;baz&gt;.method = "nameOfTheConfigParamInjectionMethod"
    * conf.&lt;baz&gt;.value = "config value"</pre>
    *
    * The name of the injected object references a configuration section within
    * the configuration file located under the defined namespace. "&lt;foo&gt;"
    * indicates the key of the injected object. This makes possible multiple
    * injections. Too, "&lt;baz&gt;" indicates one section of plain configuration
    * param injection.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.04.2009<br />
    * Version 0.2, 19.04.2009 (Finished implementation)<br />
    */
   final class DIServiceManager extends APFObject {

      /**
       * @private
       * Injection call cache to avoid circular injections.
       */
      private $injectionCallCache = array();

      /**
       * @private
       * Contains the service objects, that were already configured.
       */
      private $serviceObjectCache = array();

      /**
       * @public
       * 
       * Returns the initialized service object.
       *
       * @param string $configNamespace The namespace of the service object.
       * @param string $name The name of the desired service object.
       * @return APFObject The preconfigured service object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 19.04.2009<br />
       */
      public function &getServiceObject($configNamespace,$sectionName){

         // Check, whether service object was created before. If yes, deliver it from cache.
         $cacheKey = $configNamespace.'__'.$sectionName;
         if(isset($this->serviceObjectCache[$cacheKey])){
            return $this->serviceObjectCache[$cacheKey];
         }

         // Invoke benchmarker. Suppress warning for already started timers with circular calls!
         // Suppressing is here done by a dirty '@', because we will run into an error anyway.
         $t = &Singleton::getInstance('BenchmarkTimer');
         $benchId = 'DIServiceManager::getServiceObject('.$configNamespace.','.$sectionName.')';
         @$t->start($benchId);

         // Get config to determine, which object to create. Parse subsections, to be able to
         // easily separate the init/conf subsections.
         $section = $this->getServiceConfiguration($configNamespace, $sectionName);
         
         // check, whether the section contains the basic directives
         $serviceType = $section->getValue('servicetype');
         $namespace = $section->getValue('namespace');
         $class = $section->getValue('class');

         // Check if configuration section was complete. If not throw an error.
         if($serviceType !== null && $namespace !== null && $class !== null){

            // include the class representing the service object
            if(!class_exists($class)){
               import($namespace,$class);
            }

            // Create the service object with use of the "normal" service manager. Perhaps, this
            // may run into problems, because we have to ensure, that the singleton objects are
            // only treated once by the injection mechanism!
            // But: if we constitute, that the injected service objects are often also singletons
            // and the DIServiceManager caches the created service objects within a singleton cache,
            // this is no problem. Hence, the injected instance is then only one time constructed
            // initialized.
            $serviceObject = &$this->__getServiceObject($namespace,$class,$serviceType);

            // do param injection (static configuration)
            $cfTasks = $section->getSection('conf');
            if($cfTasks !== null){

               foreach($cfTasks->getSectionNames() as $initKey){

                  $directive = $cfTasks->getSection($initKey);

                  // be aware of the params needed for injection
                  $method = $directive->getValue('method');
                  $value = $directive->getValue('value');
                  if($method !== null && $value !== null){

                     // check, if method exists to avoid fatals
                     if(method_exists($serviceObject,$method)){
                        $serviceObject->{$method}($value);
                     } else{

                        throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Injection of'
                           .' configuration value "'.$directive->getValue('value'). '" cannot be accomplished'
                           .' to service object "'.$class.'" from namespace "'.$namespace.'"! Method '
                           .$method.'() is not implemented!', E_USER_ERROR);

                     }

                   // end if
                  }
                  else{

                     throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the'
                        .' service object "'.$sectionName.'" cannot be accomplished, due to'
                        .' incorrect configuration! Please revise the "'.$initKey.'" sub section and'
                        .' consult the manual!',E_USER_ERROR);

                  }

                // end foreach
               }

             // end if
            }

            // do service object injection
            $miTasks = $section->getSection('init');
            if($miTasks !== null){

               foreach($miTasks->getSectionNames() as $initKey){

                  $directive = $miTasks->getSection($initKey);

                  // be aware of the params needed for injection
                  $method = $directive->getValue('method');
                  $namespace = $directive->getValue('namespace');
                  $name = $directive->getValue('name');
                  if($method !== null && $namespace !== null && $name !== null){

                     // check for circular injection
                     $injectionKey = $namespace.'::'.$class.'['.$serviceType.']'.' injected with '.
                        $method.'('.$namespace.'::'.$name.')';

                     if(!isset($this->injectionCallCache[$injectionKey])){

                        // add the current run to the recursion detection array
                        $this->injectionCallCache[$injectionKey] = true;

                        // get the dependent service object
                        $miObject = &$this->getServiceObject($namespace,$name);

                        // inject the current service object with the created one
                        if(method_exists($serviceObject,$method)){
                           $serviceObject->{$method}($miObject);
                         // end if
                        }
                        else{
                           
                           throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Injection of service object "'.$name.
                              '" from namespace "'.$namespace.'" cannot be accomplished to service object "'.
                              $class.'" from namespace "'.$namespace.'"! Method '.$method.'() is not implemented!',
                              E_USER_ERROR);

                        }

                      // end if
                     }
                     else{

                        // append error to log to provide debugging information
                        $log = &Singleton::getInstance('Logger');
                        $instructions = (string)'';
                        foreach ($this->injectionCallCache as $injectionInstruction => $DUMMY){
                           $instructions .= PHP_EOL.$injectionInstruction;
                        }
                        $log->logEntry('php','[DIServiceManager::getServiceObject()] Injection stack trace: '.$instructions,'TRACE');

                        // print note with shortend information
                        throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Detected circular injection! '.
                           'Class "'.$class.'" from namespace "'.$namespace.'" with service type "'.$serviceType.
                           '" was already configured with service object "'.$name.'" from namespace "'.
                           $namespace.'"! Full stack trace can be taken from the logfile!', E_USER_ERROR);

                     }

                   // end if
                  }
                  else{

                     throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the service object "'.
                        $sectionName.'" cannot be accomplished, due to incorrect configuration! Please revise the "'.$initKey.
                        '" sub section and consult the manual!',
                        E_USER_ERROR);

                  }

                // end foreach
               }

             // end if
            }

          // end if
         }
         else{

            throw new InvalidArgumentException('[DIServiceManager::getServiceObject()] Initialization of the service object "'.
               $sectionName.'" from namespace "'.$configNamespace.'" cannot be accomplished, due to missing
               or incorrect configuration! Please revise the configuration file and consult the manual!',
               E_USER_ERROR);

         }

         $t->stop($benchId);

         // add service object to cache and return it
         $this->serviceObjectCache[$cacheKey] = $serviceObject;
         return $this->serviceObjectCache[$cacheKey];

       // end function
      }

      /**
       * @private
       *
       * Loads the service configuration.
       *
       * @param string $configNamespace The namespace of the service (a.k.a. config namespace).
       * @param string $sectionName The name of the service (a.k.a. section name).
       * @return IniConfiguration The appropriate configuration.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.10.2010<br />
       */
      private function getServiceConfiguration($configNamespace, $sectionName) {

         $provider = ConfigurationManager::retrieveProvider('ini');
         /* @var $provider IniConfigurationProvider */

         // enable the parse sub section feature
         $currentSetting = $provider->getParseSubSections();
         $provider->setParseSubSections(true);

         $config = $this->getConfiguration($configNamespace,'serviceobjects.ini');

         // reset the parse sub section feature to the previous value
         $provider->setParseSubSections($currentSetting);

         return $config->getSection($sectionName);
         
      }

    // end class
   }
?>