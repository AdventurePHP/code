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

   import('modules::genericormapper::data','GenericORRelationMapper');

   /**
   *  @package modules::genericormapper::data
   *  @class GenericORMapperFactory
   *
   *  Implements the factory for the GenericORRelationMapper. Please do only unse the factory in
   *  singleton mode. To achieve this, use the coreObject's __getServiceObject() method.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.06.2008<br />
   */
   class GenericORMapperFactory extends coreObject {

      /**
      *  @private
      *  Stores the or mapper instances.
      */
      private $__ORMapperCache = array();


      function GenericORMapperFactory(){
      }


      /**
       * @public
       *
       * Factory for the generic or mapper module. Returns a reference on an instance of the
       * GenericORRelationMapper. Do not use the $debugMode option set to true in production
       * environment!
       *
       * @param string $configNamespace namespace, where the desired mapper configuration is located
       * @param string $configNameAffix name affix of the object and relation definition files
       * @param string $connectionName name of the connection, that the mapper should use to acces the database
       * @param string $type indicates, if the mapper should be created singleton or session singleton
       * @param boolean $debugMode Inicates, if the generated statements should be logged to a file. Do not use this option set to true in production environment!
       * @return GenericORRelationMapper The desired instance of the generic OR mapper.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2008<br />
       * Version 0.2, 25.10.2008 (Bugfix: added the $Type parameter to the service manager call)<br />
       * Version 0.3, 26.10.2008 (Bugfix: cache key now recognizes the creation type)<br />
       * Version 0.4, 03.05.2009 (Added the $debugMode param)<br />
       */
      function &getGenericORMapper($configNamespace,$configNameAffix,$connectionName,$type = 'SINGLETON',$debugMode = false){

         // calculate cache key
         $cacheKey = md5($configNamespace.$configNameAffix.$connectionName.$type);

         // create and initaialize a mapper instance
         if(!isset($this->__ORMapperCache[$cacheKey])){
            $this->__ORMapperCache[$cacheKey] =
               &$this->__getAndInitServiceObject(
                  'modules::genericormapper::data',
                  'GenericORRelationMapper',
                  array('ConfigNamespace' => $configNamespace,
                     'ConfigNameAffix' => $configNameAffix,
                     'ConnectionName' => $connectionName,
                     'LogStatements' => $debugMode
                  ),
                  $type);
          // end if
         }

         return $this->__ORMapperCache[$cacheKey];

       // end function
      }

    // end class
   }
?>