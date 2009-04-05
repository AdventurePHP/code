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
   *  @namespace modules::genericormapper::data
   *  @class GenericORMapperFactory
   *
   *  Implements the factory for the GenericORRelationMapper. Please do only unse the factory in
   *  singleton mode. To achieve this, use the coreObject's __getServiceObject() method.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.06.2008<br />
   */
   class GenericORMapperFactory extends coreObject
   {

      /**
      *  @protected
      *  Stores the or mapper instances.
      */
      protected $__ORMapperCache = array();


      function GenericORMapperFactory(){
      }


      /**
      *  @public
      *
      *  Factory for the generic or mapper module. Returns a reference on an instance of the GenericORRelationMapper.
      *
      *  @param string $ConfigNamespace namespace, where the desired mapper configuration is located
      *  @param string $ConfigNameAffix name affix of the object and relation definition files
      *  @param string $ConnectionName name of the connection, that the mapper should use to acces the database
      *  @param string $Type indicates, if the mapper should be created singleton or session singleton
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.06.2008<br />
      *  Version 0.2, 25.10.2008 (Bugfix: added the $Type parameter to the service manager call)<br />
      *  Version 0.3, 26.10.2008 (Bugfix: cache key now recognizes the creation type)<br />
      */
      function &getGenericORMapper($ConfigNamespace,$ConfigNameAffix,$ConnectionName,$Type = 'SINGLETON'){

         // calculate cache key
         $CacheKey = md5($ConfigNamespace.$ConfigNameAffix.$ConnectionName.$Type);

         // create and initaialize a mapper instance
         if(!isset($this->__ORMapperCache[$CacheKey])){
            $this->__ORMapperCache[$CacheKey] = &$this->__getAndInitServiceObject('modules::genericormapper::data','GenericORRelationMapper',array('ConfigNamespace' => $ConfigNamespace,'ConfigNameAffix' => $ConfigNameAffix,'ConnectionName' => $ConnectionName),$Type);
          // end if
         }

         // return mapper instance
         return $this->__ORMapperCache[$CacheKey];

       // end function
      }

    // end class
   }
?>