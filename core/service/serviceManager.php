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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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
   class serviceManager
   {

      /**
      *  @private
      *  Context.
      */
      var $__Context;


      /**
      *  @private
      *  @since 0.2
      *  Sprache.
      */
      var $__Language;


      function serviceManager(){
      }


      /**
      *  @public
      *
      *  Gibt ein ServiceObject zurück, dessen Context bereits durch den ServiceManager gesetzt wurde.<br />
      *
      *  @param string $Namespace; Namespace des zu ladenden Moduls
      *  @param string $ServiceName; Name des zu ladenden Moduls
      *  @param string $Type; Art der Instanziierung des ServiceObjekts. Standard: SINGLETON
      *  @return object $ServiceObject; Das Service-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 17.03.2007 (Fehlermeldung angepasst)<br />
      *  Version 0.3, 22.04.2007 (Sprache wird mit übergeben)<br />
      *  Version 0.4, 24.02.2008 (Um SessionSingleton erweitert)<br />
      *  Version 0.5, 25.02.2008 (Performance-Optimierung für SessionSingleton eingefügt)<br />
      */
      function &getServiceObject($Namespace,$ServiceName,$Type = 'SINGLETON'){

         //
         //  Es sollte überlegt werden, ob nicht zusätzlich Interfaces für
         //  ServiceObjects eingefügt werden. Somit müsste beim Wechsel auf
         //  einen anderen Service die Applikation nicht getauscht werden,
         //  sondern nur die Implementierung des Interfaces. So können
         //
         //  - Namespace
         //  - Art (Singleton, ... )
         //  - Modul
         //
         //  nochmals abstrahiert werden.
         //
         //  -> Feature für V 0.4 / später
         //

         if($Type == 'SINGLETON'){

            // ServiceObject instanziieren
            $ServiceObject = &Singleton::getInstance($ServiceName);

            // Falls Klasse von coreObject geerbt hat den Context setzen
            if(is_subclass_of($ServiceObject,'coreObject')){
               $ServiceObject->set('Context',$this->__Context);
               $ServiceObject->set('Language',$this->__Language);
               $ServiceObject->set('ServiceType','SINGLETON');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$ServiceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end if
         }
         elseif($Type == 'SESSIONSINGLETON'){

            // Klasse einbinden, falls noch nicht vorhanden
            if(!class_exists('SessionSingleton')){
               import('core::singleton','SessionSingleton');
             // end if
            }

            // SerciceObject instanziieren
            $ServiceObject = &SessionSingleton::getInstance($ServiceName);

            // Falls Klasse von coreObject geerbt hat den Context setzen
            if(is_subclass_of($ServiceObject,'coreObject')){
               $ServiceObject->set('Context',$this->__Context);
               $ServiceObject->set('Language',$this->__Language);
               $ServiceObject->set('ServiceType','SESSIONSINGLETON');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$ServiceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end elseif
         }
         elseif($Type == 'NORMAL'){

            // ServiceObject instanziieren
            $ServiceObject = new $ServiceName();

            // Falls Klasse von coreObject geerbt hat den Context setzen
            if(is_subclass_of($ServiceObject,'coreObject')){
               $ServiceObject->set('Context',$this->__Context);
               $ServiceObject->set('Language',$this->__Language);
               $ServiceObject->set('ServiceType','NORMAL');
             // end if
            }
            else{
               trigger_error('[serviceManager->getServiceObject()] The precisely now created object ('.$ServiceName.') inherits not from superclass coreObject! So the context cannot be set correctly!',E_USER_WARNING);
             // end else
            }

          // end elseif
         }
         else{
            trigger_error('[serviceManager->getServiceObject()] The given type ('.$Type.') is not supported. Please provide one out of "SINGLETON", "SESSIONSINGLETON" or "NORMAL"',E_USER_WARNING);
            $ServiceObject = null;
          // end else
         }

         // ServiceObject zurückgeben
         return $ServiceObject;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt ein ServiceObject, das bereits mit einem Initialisierungs-Parameter initialisiert<br />
      *  wurde zurück. Der Context wurde bereits durch den ServiceManager gesetzt.<br />
      *
      *  @param string $Namespace; Namespace des zu ladenden Moduls
      *  @param string $ServiceName; Name des zu ladenden Moduls
      *  @param string $InitParam; Initialisierungs-Parameter
      *  @param string $Type; Art der Instanziierung des ServiceObjekts. Standard: Singleton
      *  @return object $ServiceObject; Das Service-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function &getAndInitServiceObject($Namespace,$ServiceName,$InitParam,$Type = 'SINGLETON'){

         // ServiceObject holen
         $ServiceObject = &$this->getServiceObject($Namespace,$ServiceName,$Type);

         // ServiceObject initialisieren
         if(in_array('init',get_class_methods($ServiceObject))){
            $ServiceObject->init($InitParam);
          // end if
         }
         else{
            trigger_error('[serviceManager->getAndInitServiceObject()] The service object ('.$ServiceName.') doesn\'t support initialization!',E_USER_WARNING);
          // end else
         }

         // ServiceObject zurückgeben
         return $ServiceObject;

       // end function
      }


      /**
      *  @public
      *
      *  Setzt den Context des ServiceManager.
      *
      *  @param string $Context; Context des ServiceManagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      */
      function setContext($Context){
         $this->__Context = $Context;
       // end function
      }


      /**
      *  @public
      *
      *  Gibt den Context des ServiceManager zurück.
      *
      *  @return string $Context; Context des ServiceManagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      */
      function getContext(){
         return $this->__Context;
       // end function
      }


      /**
      *  @public
      *  @since 0.2
      *
      *  Setzt die Sprache des ServiceManager.
      *
      *  @param string $Language; Sprache des ServiceManagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.04.2007<br />
      */
      function setLanguage($Language){
         $this->__Language = $Language;
       // end function
      }


      /**
      *  @public
      *  @since 0.2
      *
      *  Gibt die Sprache des ServiceManager zurück.
      *
      *  @return string $Language; Sprache des ServiceManagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.04.2007<br />
      */
      function getLanguage(){
         return $this->__Language;
       // end function
      }

    // end class
   }
?>