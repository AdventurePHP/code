<?php
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
   class GenericORMapperFactory extends coreObject
   {

      /**
      *  @private
      *  Stores the or mapper instances.
      */
      var $__ORMapperCache = array();


      function GenericORMapperFactory(){
      }


      /**
      *  @public
      *
      *  Factory for the GenericORMapper. Returns a reference on an instance of the GenericORRelationMapper.
      *
      *  @param string $ConfigNamespace; namespace, where the desired mapper configuration is located
      *  @param string $ConfigNameAffix; name affix of the object and relation definition files
      *  @param string $ConnectionName; name of the connection, that the mapper should use to acces the database
      *  @param string $Type; indicates, if the mapper should be created singleton or session singleton
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.06.2008<br />
      */
      function &getGenericORMapper($ConfigNamespace,$ConfigNameAffix,$ConnectionName,$Type = 'SINGLETON'){

         // calculate cache key
         $CacheKey = md5($ConfigNamespace.$ConfigNameAffix.$ConnectionName);

         // create and initaialize a mapper instance
         if(!isset($this->__ORMapperCache[$CacheKey])){
            $this->__ORMapperCache[$CacheKey] = &$this->__getAndInitServiceObject('modules::genericormapper::data','GenericORRelationMapper',array('ConfigNamespace' => $ConfigNamespace,'ConfigNameAffix' => $ConfigNameAffix,'ConnectionName' => $ConnectionName));
          // end if
         }

         // return mapper instance
         return $this->__ORMapperCache[$CacheKey];

       // end function
      }

    // end class
   }
?>