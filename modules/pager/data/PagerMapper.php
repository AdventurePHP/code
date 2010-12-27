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

   import('core::session','SessionManager');

   /**
    * @package modules::pager::data
    * @class PagerMapper
    *
    * Represents the data layer of the pager.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2006<br />
    * Version 0.2, 19.02.2009 (Added the connection key handling)<br />
    * Version 0.3, 24.01.2009 (Added session caching to gain performance)<br />
    */
   final class PagerMapper extends APFObject {

      /**
       *  @protected
       *  Defines the database connection key. Must be filled within the init() method.
       */
      protected $__ConnectionKey = null;

      /**
      *  @public
      *
      *  Initializes the connection key of the mapper.
      *
      *  @param string $initParam the database connection key
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 19.01.2009<br />
      */
      public function init($initParam){
         $this->__ConnectionKey = $initParam;
      }

      /**
      *  @private
      *
      *  Returns the session key for the current statement and params.
      *
      *  @param string $namespace namespace of the statement
      *  @param string $statement name of the statement file
      *  @param array $params statement params
      *  @return string $sessionKey the desired session key
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.01.2009<br />
      */
      protected function __getSessionKey($namespace,$statement,$params){
         return 'PagerMapper_'.md5($namespace.$statement.implode('',$params));
      }

      /**
      *  @public
      *
      *  Returns the number of entries of the current object.
      *
      *  @param string $namespace the namespace of the statement
      *  @param string $statement the name of the statement file
      *  @param array $params additional params for the statement
      *  @param bool $cache decides if caching is active or not (true = yes, false = no)
      *  @return string $entriesCount the number of entries
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 16.08.2006 (Added an argument for further statement params)<br />
      *  Version 0.3, 19.01.2009 (Added the connection key handling)<br />
      *  Version 0.4, 24.01.2009 (Added session caching)<br />
      *  Version 0.5, 25.01.2009 (Removed nullpointer bug due to session object definition)<br />
      */
      public function getEntriesCount($namespace,$statement,$params = array(),$cache = true){

         // start benchmarker
         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('PagerMapper::getEntriesCount()');

         // try to load the entries count from the session
         if($cache === true){
            $session = new SessionManager('modules::pager::biz');
            $sessionKey = $this->__getSessionKey($namespace,$statement,$params).'_EntriesCount';
            $entriesCount = $session->loadSessionData($sessionKey);
         } else {
            $entriesCount = false;
         }

         // load from database if not in session
         if($entriesCount === false){
            $cM = &$this->__getServiceObject('core::database','ConnectionManager');
            $SQL = &$cM->getConnection($this->__ConnectionKey);
            $result = $SQL->executeStatement($namespace,$statement,$params);
            $data = $SQL->fetchData($result);
            $entriesCount = $data['EntriesCount'];

            // only save to session, when cache is enabled
            if($cache === true){
               $session->saveSessionData($sessionKey,$entriesCount);
            }

         }

         $t->stop('PagerMapper::getEntriesCount()');
         return $entriesCount;

      }


      /**
       * @public
       *
       * Returns a list of the object ids, that should be loaded for the current page.
       *
       * @param string $namespace the namespace of the statement
       * @param string $statement the name of the statement file
       * @param array $params additional params for the statement
       * @param bool $cache decides if caching is active or not (true = yes, false = no)
       * @return array $entries a list of entry ids
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.08.2006<br />
       * Version 0.2, 19.01.2009 (Added the connection key handling)<br />
       * Version 0.3, 24.01.2009 (Added session caching)<br />
       * Version 0.4, 25.01.2009 (Removed nullpointer bug due to session object definition)<br />
       * Version 0.5, 27.12.2010 (Bugfix: In case of empty results, no empty objects are returned any more.)<br />
      */
      public function loadEntries($namespace, $statement, $params = array(), $cache = true) {

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('PagerMapper::loadEntries()');

         // try to load the entries count from the session
         if($cache === true){
            $session = new SessionManager('modules::pager::biz');
            $sessionKey = $this-> __getSessionKey($namespace,$statement,$params).'_EntryIDs';
            $entryIds = $session->loadSessionData($sessionKey);
         } else {
            $entryIds = false;
         }

         // load from database if not in session
         if($entryIds === false){

            $cM = &$this->__getServiceObject('core::database','ConnectionManager');
            $SQL = &$cM->getConnection($this->__ConnectionKey);
            $result = $SQL->executeStatement($namespace,$statement,$params);

            // map empty results to empty array
            if ($result === false) {
               return array();
            }

            $entryIds = array();
            while($data = $SQL->fetchData($result)){
               $entryIds[] = $data['DB_ID'];
            }

            // only save to session, when cache is enabled
            if($cache === true){
               $session->saveSessionData($sessionKey,serialize($entryIds));
            }

         } else {
            $entryIds = unserialize($entryIds);
         }

         $t->stop('PagerMapper::loadEntries()');
         return $entryIds;

      }

   }
?>