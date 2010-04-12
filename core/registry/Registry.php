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

   /**
    * @package core::registry
    * @class Registry
    *
    * Implements the registry pattern. You can register and retrieve namespace dependent values. The
    * Registry must be a singleton! Please use $Reg = &Singleton::getInstance('Registry'); to abtain
    * a reference on it.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1,19.06.2008<br />
    */
   final class Registry {

      /**
       * @private
       * @var string[] Stores the registry content.
       */
      private $__RegistryStore = array();

      public function Registry(){
      }

      /**
       * @public
       *
       * Adds a registry value to the registry. If write protection is enabled a warning is displayed.
       *
       * @param string $namespace namespace of the entry
       * @param string $name name of the entry
       * @param string $value value of the entry
       * @param bool $readOnly true (value is read only) | false (value can be changed)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1,19.06.2008<br />
       */
      function register($namespace,$name,$value,$readOnly = false){

         if(isset($this->__RegistryStore[$namespace][$name]['readonly']) && $this->__RegistryStore[$namespace][$name]['readonly'] === true){
            throw new InvalidArgumentException('[Registry::register()] The entry with name "'
                    .$name.'" already exists in namespace "'.$namespace.'" and is read only! '
                    .'Please choose another name.',E_USER_WARNING);
          // end if
         }
         else{
            $this->__RegistryStore[$namespace][$name]['value'] = $value;
            $this->__RegistryStore[$namespace][$name]['readonly'] = $readOnly;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Retrieves a registry value from the registry.<br />
       *
       * @param string $namespace namespace of the entry
       * @param string $name name of the entry
       *
       * @author Christian Achatz
       * @version
       * Version 0.1,19.06.2008<br />
       */
      function retrieve($namespace,$name){

         if(isset($this->__RegistryStore[$namespace][$name]['value'])){
            return $this->__RegistryStore[$namespace][$name]['value'];
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