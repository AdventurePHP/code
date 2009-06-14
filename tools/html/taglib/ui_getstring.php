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

   /**
   *  @namespace tools::html::taglib
   *  @class ui_getstring
   *  @abstract
   *
   *  Implements a base class for the taglibs "<html:getstring />" and "<template:getstring />".
   *  This lib fetches the desired configuration value and returns it on transformation time.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 21.04.2006<br />
   */
   abstract class ui_getstring extends Document
   {

      function ui_getstring(){
      }


      /**
      *  @public
      *
      *  Implements an abstract method to return a value from a specific configuration section.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.04.2006<br />
      *  Version 0.2, 17.10.2008 (Enhanced error messages)<br />
      */
      function transform(){

         // start timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $ID = '('.get_class($this).') '.$this->__ObjectID.'::transform()';
         $T->start($ID);

         // check for attribute "namespace"
         if(!isset($this->__Attributes['namespace']) || empty($this->__Attributes['namespace'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "namespace" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $Namespace = $this->__Attributes['namespace'];
          // end else
         }

         // check for attribute "config"
         if(!isset($this->__Attributes['config']) || empty($this->__Attributes['config'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "config" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $ConfigName = $this->__Attributes['config'];
          // end else
         }

         // check for attribute "entry"
         if(!isset($this->__Attributes['entry']) || empty($this->__Attributes['entry'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "entry" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $Entry = $this->__Attributes['entry'];
          // end else
         }

         // get configuration
         $Config = &$this->__getConfiguration($Namespace,$ConfigName);

         if($Config == null){
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{

            // get configuration values
            $Value = $Config->getValue($this->__Language,$Entry);

            if($Value == null){

               // get some environment variables from the registry
               $Reg = &Singleton::getInstance('Registry');
               $Env = $Reg->retrieve('apf::core','Environment');

               // trigger error
               trigger_error('['.get_class($this).'->transform()] Given entry "'.$Entry.'" is not defined in section "'.$this->__Language.'" in configuration "'.$Env.'_'.$ConfigName.'.ini" in namespace "'.$Namespace.'" and context "'.$this->__Context.'"!');
               $T->stop($ID);
               return (string)'';

             // end if
            }
            else{
               $T->stop($ID);
               return $Value;
             // end if
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>