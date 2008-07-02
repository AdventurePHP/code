<?php
   /**
   *  @package core::registry
   *  @class RegistryFactory
   *
   *  Implements a factory for registry objects.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1,19.06.2008<br />
   */
   class RegistryFactory
   {

      /**
      *  @private
      *  Instance of the one and only registry.
      */
      var $__Registry = null;


      function RegistryFactory(){
      }


      /**
      *  @public
      *
      *  Returns a reference on the configured instance of the registry for the current namespace.<br />
      *
      *  @param string $Namespace; current namespace
      *  @return Registry $Registry; preconfigured instance of the registry
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function &getRegistry($Namespace){

         // create and cache registry
         if($this->__Registry === null){
            $this->__Registry = &Singleton::getInstance('Registry');
          // end if
         }

         // configure registry
         $this->__Registry->setCurrentNamespace($Namespace);

         // return registry
         return $this->__Registry;

       // end function
      }

    // end class
   }


   /**
   *  @package core::registry
   *  @class Registry
   *
   *  Implements a factory for registry objects.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1,19.06.2008<br />
   */
   class Registry
   {

      /**
      *  @private
      *  Stores the current namespace, that is used do modularize the registry storage.
      */
      var $__CurrentNamespace = 'global';


      /**
      *  @private
      *  Stores the registry content.
      */
      var $__RegistryStore = array();


      function Registry(){
      }


      /**
      *  @public
      *
      *  Sets the current namespace.<br />
      *
      *  @param string $Namespace; current namespace
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function setCurrentNamespace($Namespace){
         $this->__CurrentNamespace = $Namespace;
       // end function
      }


      /**
      *  @public
      *
      *  Adds a registry value to the registry.<br />
      *
      *  @param string $Name; name of the entry
      *  @param string $Value; value of the entry
      *  @param bool $ReadOnly; true (value is read only) | false (value can be changed)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function register($Name,$Value,$ReadOnly = false){

         if(!isset($this->__RegistryStore[$this->__CurrentNamespace][$Name]['readonly']) && $this->__RegistryStore[$this->__CurrentNamespace][$Name]['readonly'] === true){
            trigger_error('[Registry::setEntry()] The entry with name "'.$Name.'" already exists and is read only! Please choose another entry name.',E_USER_WARNING);
          // end if
         }
         else{
            $this->__RegistryStore[$this->__CurrentNamespace][$Name]['value'] = $Value;
            $this->__RegistryStore[$this->__CurrentNamespace][$Name]['readonly'] = $ReadOnly;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Retrieves a registry value from the registry.<br />
      *
      *  @param string $Name; name of the entry
      *  @return void $Value; the desired value or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function retrieve($Name){

         if(isset($this->__RegistryStore[$this->__CurrentNamespace][$Name]['value'])){
            return $this->__RegistryStore[$this->__CurrentNamespace][$Name]['value'];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

    // end class
   }
?>