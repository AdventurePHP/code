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

   import('modules::genericormapper::data','GenericORRelationMapper');

   /**
    * @package modules::genericormapper::data
    * @class GenericORMapperFactory
    *
    * Implements a factory for creating GenericORRelationMapper instances. Automatically
    * configures the desired instance using the given params.
    * <p/>
    * Please note, that the instanciation method of the factory decides the service mode of the
    * mapper. This means, that you have to create the factory in NORMAL mode, if you have decided
    * to use per-use-instanciation, take SINGLETON (default) if you like to have one instance per
    * request and choose SESSIONSINGLETON in case the mapper should initialize it's mapping and
    * relation table only once per session.
    * <p/>
    * Further, the factory must be created using the service manager. Sample:
    * <pre>$gormFact = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');
    * $gormFact = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory','NORMAL');
    * $gormFact = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory','SESSIONSINGLETON');</pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2008<br />
    */
   final class GenericORMapperFactory extends APFObject {

      /**
       * @private
       * @var GenericORRelationMapper[] Stores the or mapper instances.
       */
      private $ORMapperCache = array();

      public function GenericORMapperFactory(){
      }

      /**
       * @public
       *
       * Factory for the generic or mapper. Returns a reference on an instance of the
       * GenericORRelationMapper class. Do not use the $debugMode option set to true in production
       * environment!
       * <p/>
       * As of 1.12, this method has to be used as follows:
       * <pre>$gormFact = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');
       * $orm = &$gormFact->getGenericORMapper('my::namespace','config_sub_key','db_connection'[,true|false]);</pre>
       *
       * @param string $configNamespace namespace, where the desired mapper configuration is located
       * @param string $configNameAffix name affix of the object and relation definition files
       * @param string $connectionName name of the connection, that the mapper should use to acces the database
       * @param boolean $debugMode Inicates, if the generated statements should be logged to a file. Do not use this option set to true in production environment!
       * @return GenericORRelationMapper The desired instance of the generic OR mapper.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2008<br />
       * Version 0.2, 25.10.2008 (Bugfix: added the $Type parameter to the service manager call)<br />
       * Version 0.3, 26.10.2008 (Bugfix: cache key now recognizes the creation type)<br />
       * Version 0.4, 03.05.2009 (Added the $debugMode param)<br />
       * Version 0.5, 16.03.2010 (Bugfix 299: removed the type to prevent identical mapper objects)<br />
       */
      public function &getGenericORMapper($configNamespace,$configNameAffix,$connectionName,$debugMode = false){

         // calculate cache key
         $cacheKey = md5($configNamespace.$configNameAffix.$connectionName);

         // create and initaialize a mapper instance
         if(!isset($this->ORMapperCache[$cacheKey])){
            $this->ORMapperCache[$cacheKey] =
               &$this->__getAndInitServiceObject(
                  'modules::genericormapper::data',
                  'GenericORRelationMapper',
                  array('ConfigNamespace' => $configNamespace,
                     'ConfigNameAffix' => $configNameAffix,
                     'ConnectionName' => $connectionName,
                     'LogStatements' => $debugMode
                  ),
                  'NORMAL');
          // end if
         }

         return $this->ORMapperCache[$cacheKey];

       // end function
      }

    // end class
   }
?>