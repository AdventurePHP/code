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

   import('core::session','sessionManager');
   register_shutdown_function('saveSessionSingletonObjects');


   /**
   *  @namespace core::singleton
   *
   *  Shutdown Function um alle SessionSingleton gecachten Objekte in die Session<br />
   *  zu persistieren.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 24.02.2008<br />
   *  Version 0.2, 26.02.2008 (Include des sessionManagers wurde falsch notiert)<br />
   */
   function saveSessionSingletonObjects(){

      // Cachenamen erzeugen
      $CacheContainer = SessionSingleton::showCacheContainerOffset();

      if(isset($GLOBALS[$CacheContainer])){

         // Anzahl der Objekte z�hlen
         $CacheCount = count($GLOBALS[$CacheContainer]);

         if($CacheCount > 0){

            // sessionManager erzeugen
            $sessMgr = new sessionManager(SessionSingleton::showSessionNamespace());

            foreach($GLOBALS[$CacheContainer] as $Key => $DUMMY){
               $sessMgr->saveSessionData($Key,serialize($GLOBALS[$CacheContainer][$Key]));
             // end for
            }

          // end if
         }

       // end if
      }

    // end function
   }


   /**
   *  @namespace core::singleton
   *  @class SessionSingleton
   *  @static
   *
   *  Abstrakte Implementierung des SessionSingleton-Patterns. Die Objekte werden �ber die Session<br />
   *  hinweg gecached. Als lokaler Cache w�hrend der Ausf�hrung der Applikation wird der Offset<br />
   *  'SESSION_SINGLETON_CACHE' im $GLOBALS-Array verwendet.<br />
   *  <br />
   *  Verwendung:<br />
   *  $oObject = &SessionSingleton::getInstance('<ClassName>');<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 24.02.2008<br />
   */
   class SessionSingleton extends Singleton
   {

      private function SessionSingleton(){
      }


      /**
      *  @public
      *  @static
      *
      *  Implementierung der Methode getInstance() f�r SessionSingleton.<br />
      *
      *  @param string $className; Name der zu instanziierenden Klasse
      *  @return object $SessionSingletonObject; Objekt, das SessionSingelton instanziiert wurde
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function &getInstance($className){

         // Cachenamen erzeugen
         $CacheContainer = SessionSingleton::showCacheContainerOffset();
         $CacheObjectName = SessionSingleton::createCacheObjectName($className);

         // Pr�fen, ob Instanz des Objekt bereits im lokalen Cache existiert
         if(!SessionSingleton::isInSingletonCache($className)){

            // Pr�fen, ob Instanz bereits im Session Cache existiert
            $sessMgr = new sessionManager(SessionSingleton::showSessionNamespace());
            $CachedObject = $sessMgr->loadSessionData($CacheObjectName);

            if($CachedObject !== false){
               $GLOBALS[$CacheContainer][$CacheObjectName] = unserialize($CachedObject);
             // end if
            }
            else{

               // Pr�fen, ob Klasse vorhanden
               if(!class_exists($className)){
                  trigger_error('[SessionSingleton::getInstance()] Class "'.$className.'" cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
                  exit(1);
                // end if
               }

               // Erzeugt Klasse $className singleton
               $GLOBALS[$CacheContainer][$CacheObjectName] = new $className;

             // end else
            }

          // end if
         }

         // Gibt Instanz aus Singleton-Cache zur�ck
         return $GLOBALS[$CacheContainer][$CacheObjectName];

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  L�scht die Instanz eines �bergebenen Objekts aus dem Singleton-Cache.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function clearInstance($className){
         unset($GLOBALS[SessionSingleton::showCacheContainerOffset()][SessionSingleton::createCacheObjectName($className)]);
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Setzt den Singleton-Cache f�r alle Objekte zur�ck.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function clearAll(){
         $GLOBALS[SessionSingleton::showCacheContainerOffset()] = array();
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Pr�ft, ob ein Objekt bereits im Singleton-Cache vorhanden ist.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function isInSingletonCache($className){

         if(isset($GLOBALS[SessionSingleton::showCacheContainerOffset()][SessionSingleton::createCacheObjectName($className)])){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt den Offset des $GLOBALS-Array zur�ck, in dem der SessionSingleton-Cache<br />
      *  gehalten wird.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function showCacheContainerOffset(){
         return (string)'SESSION_SINGLETON_CACHE';
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt den Namespace zur�ck, in dem die Objekte in der Session gecached werden sollen.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      static function showSessionNamespace(){
         return (string)'core::session';
       // end function
      }

    // end class
   }
?>