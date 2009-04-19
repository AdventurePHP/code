<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::service','serviceManager');

   /**
    * The DIServiceManager provides a dependency injection container for
    * creating preconfigured service objects similar to the {@link ServiceManager}.
    * It provides only method injection, because the APF objects are already injected
    * the current language and context. Introducing constructor injection would
    * lead to the situation, that the service objects could not be created
    * in SINGLETON or SESSIONSINGLETON mode with the generic implementations.
    * Please note, that a service object must derive from the {@link coreObject} class,
    * to be able to inject the context and language of the current instance of
    * the page or front controller. For convenience, the {@link coreObject} contains the
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
   final class DIServiceManager extends coreObject
   {

      /**
       * @private
       * Injection call cache to avoid circular injections.
       */
      private $__InjectionCallCache = array();

      /**
       * @private
       * Defines the name of the service object configuration file.
       */
      private $__ConfigFileName = 'serviceobjects';

      public function DIServiceManager(){
      }

      /**
       * @public
       * 
       * Returns the initialized service object.
       *
       * @param string $configNamespace The namespace of the service object.
       * @param string $name The name of the desired service object.
       * @return coreObject The preconfigured service object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 19.04.2009<br />
       */
      public function &getServiceObject($configNamespace,$sectionName){

         // invoke benchmarker
         $t = &Singleton::getInstance('benchmarkTimer');
         $benchId = 'DIServiceManager::getServiceObject('.$configNamespace.','.$sectionName.')';
         $t->start($benchId);

         // get config to determine, which object to create. Parse subsections, to be able to
         // easily separate the init/conf subsections.
         $config = &$this->__getConfiguration($configNamespace,$this->__ConfigFileName,true);

         // check, whether the section contains the basic directives
         $serviceType = $config->getValue($sectionName,'servicetype');
         $namespace = $config->getValue($sectionName,'namespace');
         $class = $config->getValue($sectionName,'class');
         
         if($serviceType !== null && $namespace !== null && $class !== null){

            // include the class, that it can be created
            if(!class_exists($class)){
               import($namespace,$class);   
            }

            // create the service object with use of the "normal" service manager
            // perhaps, this is not possible, because we have to ensure, that the
            // singleton objects are only treated once by the injection
            // mechanism!!!
            // But: if we constitute, that the injected service objects are often
            // also singletons, this is no problem, because the injected instance
            // is then only one time constructed initialized.
            $serviceObject = &$this->__getServiceObject($namespace,$class,$serviceType);

            // do param injection (static configuration)
            $cfTasks = $config->getSubSection($sectionName,'conf');
            if($cfTasks !== null){

               foreach($cfTasks as $initKey => $directive){

                  // be aware of the params needed for injection
                  if(isset($directive['method']) && isset($directive['value'])){

                     // check, if method exists to avoid fatals
                     if(method_exists($serviceObject,$directive['method'])){
                        $serviceObject->{$directive['method']}($directive['value']);
                      // end if
                     }
                     else{

                        trigger_error('[DIServiceManager::getServiceObject()] Injection of configuration value "'.$directive['value'].
                           '" cannot be accomplished to service object "'.$class.'" from namespace "'.$namespace.'"! Method '.
                           $directive['method'].'() is not implemented!',
                           E_USER_ERROR);
                        exit();

                      // end else
                     }



                   // end if
                  }
                  else{

                     trigger_error('[DIServiceManager::getServiceObject()] Initialization of the service object "'.
                        $sectionName.'" cannot be accomplished, due to incorrect configuration! Please revise the "'.$initKey.
                        '" sub section and consult the manual!',
                        E_USER_ERROR);
                     exit();

                   // end else
                  }

                // end foreach
               }

             // end if
            }

            // do service object injection
            $miTasks = $config->getSubSection($sectionName,'init');
            if($miTasks !== null){

               foreach($miTasks as $initKey => $directive){

                  // be aware of the params needed for injection
                  if(isset($directive['method']) && isset($directive['namespace']) && isset($directive['name'])){

                     // check for circular injection
                     $injectionKey = $namespace.'::'.$class.'['.$serviceType.']'.' injected with '.
                        $directive['method'].'('.$directive['namespace'].'::'.$directive['name'].')';

                     if(!isset($this->__InjectionCallCache[$injectionKey])){

                        // check, if method exists to avoid fatals
                        if(method_exists($serviceObject,$directive['method'])){

                           $this->__InjectionCallCache[$injectionKey] = true;
                           $miObject = &$this->getServiceObject($directive['namespace'],$directive['name']);
                           $serviceObject->{$directive['method']}($miObject);

                         // end if
                        }
                        else{

                           trigger_error('[DIServiceManager::getServiceObject()] Injection of service object "'.$directive['name'].
                              '" from namespace "'.$directive['namespace'].'" cannot be accomplished to service object "'.
                              $class.'" from namespace "'.$namespace.'"! Method '.$directive['method'].'() is not implemented!',
                              E_USER_ERROR);
                           exit();

                         // end else
                        }

                      // end if
                     }
                     else{

                        // print note with shortend information
                        trigger_error('[DIServiceManager::getServiceObject()] Detected circular injection: '.
                           'class "'.$class.'" from namespace "'.$namespace.'" with service type "'.$serviceType.
                           '" was already configured with service object "'.$directive['name'].'" from namespace "'.
                           $directive['namespace'].'"! Full stack trace can be taken from the logfile!', E_USER_ERROR);

                        // append error to log to provide debugging information
                        $log = &Singleton::getInstance('Logger');
                        $instructions = (string)'';
                        foreach ($this->__InjectionCallCache as $injectionInstruction => $DUMMY){
                           $instructions .= PHP_EOL.$injectionInstruction;
                        }
                        $log->logEntry('php','[DIServiceManager::getServiceObject()] Injection stack trace: '.$instructions,'TRACE');
                        exit();

                      // end else
                     }

                  }
                  else{

                     trigger_error('[DIServiceManager::getServiceObject()] Initialization of the service object "'.
                        $sectionName.'" cannot be accomplished, due to incorrect configuration! Please revise the "'.$initKey.
                        '" sub section and consult the manual!',
                        E_USER_ERROR);
                     exit();
                     
                   // end else
                  }

                // end foreach
               }

             // end if
            }

          // end if
         }
         else{

            $reg = &Singleton::getInstance('Registry');
            $env = $reg->retrieve('apf::core','Environment');
            trigger_error('[DIServiceManager::getServiceObject()] No valid object definition section found for service object "'.$sectionName.'"! Please check the configuration file "'.$env.'_serviceobjects.ini" in namespace "'.$configNamespace.'" for context "'.$this->__Context.'".',E_USER_ERROR);
            exit();
            
          // end else
         }

         $t->stop($benchId);
         return $serviceObject;

       // end function
      }

    // end class
   }
?>