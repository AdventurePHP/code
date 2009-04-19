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

   /**
   *  @namespace core::service
   *  @class serviceManager
   *
   *  Instanziiert ServiceObjekte mit dem jeweils richtigen Context.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 07.03.2007<br />
   *  Version 0.2, 22.04.2007 (Um Sprache erweitert)<br />
   *  Version 0.3, 24.02.2008 (Um SessionSingleton erweitert)<br />
   */
   final class serviceManager
   {

      /**
      *  @private
      *  Context.
      */
      private $__Context;


      /**
      *  @private
      *  @since 0.2
      *  Current language.
      */
      private $__Language;


      function serviceManager(){
      }


      /**
      *  @public
      *
      *  Returns a service object according to the current application context.
      *
      *  @param string $namespace Namespace of the service object (currently ignored).
      *  @param string $serviceName Name of the service object (=class name).
      *  @param string $type The initializing type (see service manager for details).
      *  @return coreObject $serviceObject The desired service object.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 17.03.2007 (Adjusted error messages)<br />
      *  Version 0.3, 22.04.2007 (Language is now injected)<br />
      *  Version 0.4, 24.02.2008 (Added SessionSingleton service type)<br />
      *  Version 0.5, 25.02.2008 (Added performance optimization for the SessionSingleton objects)<br />
      */
      function &getServiceObject($namespace,$serviceName,$type = 'SINGLETON'){

         if($type == 'SINGLETON'){

            $serviceObject = &Singleton::getInstance($serviceName);

            if(is_subclass_of($serviceObject,'coreObject')){
               $serviceObject->set('Context',$this->__Context);
               $serviceObject->set('Language',$this->__Language);
               $serviceObject->set('ServiceType','SINGLETON');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$serviceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end if
         }
         elseif($type == 'SESSIONSINGLETON'){

            // Klasse einbinden, falls noch nicht vorhanden
            if(!class_exists('SessionSingleton')){
               import('core::singleton','SessionSingleton');
             // end if
            }

            $serviceObject = &SessionSingleton::getInstance($serviceName);

            if(is_subclass_of($serviceObject,'coreObject')){
               $serviceObject->set('Context',$this->__Context);
               $serviceObject->set('Language',$this->__Language);
               $serviceObject->set('ServiceType','SESSIONSINGLETON');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$serviceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end elseif
         }
         elseif($type == 'NORMAL'){

            // "normally" create the object
            $serviceObject = new $serviceName();

            if(is_subclass_of($serviceObject,'coreObject')){
               $serviceObject->set('Context',$this->__Context);
               $serviceObject->set('Language',$this->__Language);
               $serviceObject->set('ServiceType','NORMAL');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$serviceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end elseif
         }
         else{
            trigger_error('[serviceManager->getServiceObject()] The given type ('.$type.') is not supported. Please provide one out of "SINGLETON", "SESSIONSINGLETON" or "NORMAL"',E_USER_WARNING);
            $serviceObject = null;
          // end else
         }

         return $serviceObject;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt ein ServiceObject, das bereits mit einem Initialisierungs-Parameter initialisiert<br />
      *  wurde zurück. Der Context wurde bereits durch den ServiceManager gesetzt.<br />
      *
      *  @param string $namespace Namespace of the service object (currently ignored).
      *  @param string $serviceName Name of the service object (=class name).
      *  @param string $InitParam The initialization param for the service object.
      *  @param string $type The initializing type (see service manager for details).
      *  @return coreObject $serviceObject The desired service object.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function &getAndInitServiceObject($namespace,$serviceName,$initParam,$type = 'SINGLETON'){

         $serviceObject = &$this->getServiceObject($namespace,$serviceName,$type);

         if(in_array('init',get_class_methods($serviceObject))){
            $serviceObject->init($initParam);
          // end if
         }
         else{
            trigger_error('[serviceManager->getAndInitServiceObject()] The service object ('.$serviceName.') doesn\'t support initialization!',E_USER_WARNING);
          // end else
         }

         return $serviceObject;

       // end function
      }


      /**
      *  @public
      *
      *  Sets the context.
      *
      *  @param string $context The context of the service manager.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      */
      function setContext($context){
         $this->__Context = $context;
       // end function
      }

      /**
      *  @public
      *  @since 0.2
      *
      *  Sets the language.
      *
      *  @param string $language Language of the service manager.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.04.2007<br />
      */
      function setLanguage($language){
         $this->__Language = $language;
       // end function
      }

    // end class
   }
?>