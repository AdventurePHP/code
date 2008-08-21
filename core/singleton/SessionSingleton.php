<?php
   import('core::session','sessionManager');

   register_shutdown_function('saveSessionSingletonObjects');

   /**
   *  @package core::singleton
   *
   *  Shutdown Function um alle SessionSingleton gecachten Objekte in die Session<br />
   *  zu persistieren.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 24.02.2008<br />
   *  Version 0.2, 26.02.2008 (Include des sessionManagers wurde falsch notiert)<br />
   */
   function saveSessionSingletonObjects(){

      // Cachenamen erzeugen
      $CacheContainer = SessionSingleton::showCacheContainerOffset();

      if(isset($GLOBALS[$CacheContainer])){

         // Anzahl der Objekte zählen
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
   *  @package core::singleton
   *  @class SessionSingleton
   *  @static
   *
   *  Abstrakte Implementierung des SessionSingleton-Patterns. Die Objekte werden über die Session<br />
   *  hinweg gecached. Als lokaler Cache während der Ausführung der Applikation wird der Offset<br />
   *  'SESSION_SINGLETON_CACHE' im $GLOBALS-Array verwendet.<br />
   *  <br />
   *  Verwendung:<br />
   *  $oObject = &SessionSingleton::getInstance('<ClassName>');<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 24.02.2008<br />
   */
   class SessionSingleton extends Singleton
   {

      function SessionSingleton(){
      }


      /**
      *  @public
      *  @static
      *
      *  Implementierung der Methode getInstance() für SessionSingleton.<br />
      *
      *  @param string $className; Name der zu instanziierenden Klasse
      *  @return object $SessionSingletonObject; Objekt, das SessionSingelton instanziiert wurde
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function &getInstance($className){

         // Cachenamen erzeugen
         $CacheContainer = SessionSingleton::showCacheContainerOffset();
         $CacheObjectName = SessionSingleton::createCacheObjectName($className);

         // Prüfen, ob Instanz des Objekt bereits im lokalen Cache existiert
         if(!SessionSingleton::isInSingletonCache($className)){

            // Prüfen, ob Instanz bereits im Session Cache existiert
            $sessMgr = new sessionManager(SessionSingleton::showSessionNamespace());
            $CachedObject = $sessMgr->loadSessionData($CacheObjectName);

            if($CachedObject !== false){
               $GLOBALS[$CacheContainer][$CacheObjectName] = unserialize($CachedObject);
             // end if
            }
            else{

               // Prüfen, ob Klasse vorhanden
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

         // Gibt Instanz aus Singleton-Cache zurück
         return $GLOBALS[$CacheContainer][$CacheObjectName];

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Löscht die Instanz eines übergebenen Objekts aus dem Singleton-Cache.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function clearInstance($className){
         unset($GLOBALS[SessionSingleton::showCacheContainerOffset()][SessionSingleton::createCacheObjectName($className)]);
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Setzt den Singleton-Cache für alle Objekte zurück.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function clearAll(){
         $GLOBALS[SessionSingleton::showCacheContainerOffset()] = array();
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Prüft, ob ein Objekt bereits im Singleton-Cache vorhanden ist.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function isInSingletonCache($className){

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
      *  Gibt den Offset des $GLOBALS-Array zurück, in dem der SessionSingleton-Cache<br />
      *  gehalten wird.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function showCacheContainerOffset(){
         return (string)'SESSION_SINGLETON_CACHE';
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt den Namespace zurück, in dem die Objekte in der Session gecached werden sollen.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function showSessionNamespace(){
         return (string)'core::session';
       // end function
      }

    // end class
   }
?>