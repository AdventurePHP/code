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
   *  @namespace core::registry
   *  @class Registry
   *
   *  Implements the registry pattern. You can register and retrieve namespace dependent values. The
   *  Registry must be a singleton! Please use $Reg = &Singleton::getInstance('Registry'); to abtain
   *  a reference on it.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1,19.06.2008<br />
   */
   class Registry
   {

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
      *  Adds a registry value to the registry. If write protection is enabled a warning is displayed.
      *
      *  @param string $Namespace; namespace of the entry
      *  @param string $Name; name of the entry
      *  @param string $Value; value of the entry
      *  @param bool $ReadOnly; true (value is read only) | false (value can be changed)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function register($Namespace,$Name,$Value,$ReadOnly = false){

         if(isset($this->__RegistryStore[$Namespace][$Name]['readonly']) && $this->__RegistryStore[$Namespace][$Name]['readonly'] === true){
            trigger_error('[Registry::register()] The entry with name "'.$Name.'" already exists in namespace "'.$Namespace.'" and is read only! Please choose another name.',E_USER_WARNING);
          // end if
         }
         else{
            $this->__RegistryStore[$Namespace][$Name]['value'] = $Value;
            $this->__RegistryStore[$Namespace][$Name]['readonly'] = $ReadOnly;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Retrieves a registry value from the registry.<br />
      *
      *  @param string $Namespace; namespace of the entry
      *  @param string $Name; name of the entry
      *  @return void $Value; the desired value or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1,19.06.2008<br />
      */
      function retrieve($Namespace,$Name){

         if(isset($this->__RegistryStore[$Namespace][$Name]['value'])){
            return $this->__RegistryStore[$Namespace][$Name]['value'];
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