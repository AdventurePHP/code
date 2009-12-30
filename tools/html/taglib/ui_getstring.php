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
    * @package tools::html::taglib
    * @class ui_getstring
    * @abstract
    *
    * Implements a base class for the taglibs &lt;html:getstring /&gt; and
    * &lt;template:getstring /&gt;. This lib fetches the desired configuration value and
    * returns it on transformation time. The configuration files must be strcutured as follows:
    * <p/>
    * <pre>
    * [de]
    * key = "german value"
    *
    * [en]
    * key = "englisch value"
    *
    * ...
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2006<br />
    * Version 0.2, 17.09.2009 (Refactored due to form taglib changes)<br />
    */
   abstract class ui_getstring extends Document {

      public function ui_getstring(){
      }

      /**
       * @public
       *
       * Implements the functionality to retrieve a language dependent value form a
       * configuration file. Checks the attributes needed for displaying data.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.04.2006<br />
       * Version 0.2, 17.10.2008 (Enhanced error messages)<br />
       */
      function transform(){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $id = '('.get_class($this).') '.$this->__ObjectID.'::transform()';
         $t->start($id);

         // check for attribute "namespace"
         $namespace = $this->getAttribute('namespace');
         if($namespace === null){
            trigger_error('['.get_class($this).'->transform()] No attribute "namespace" given in tag definition!');
            $t->stop($id);
            return (string)'';
          // end if
         }

         // check for attribute "config"
         $configName = $this->getAttribute('config');
         if($configName === null){
            trigger_error('['.get_class($this).'->transform()] No attribute "config" given in tag definition!');
            $t->stop($id);
            return (string)'';
          // end if
         }

         // check for attribute "entry"
         $entry = $this->getAttribute('entry');
         if($entry === null){
            trigger_error('['.get_class($this).'->transform()] No attribute "entry" given in tag definition!');
            $t->stop($id);
            return (string)'';
          // end if
         }

         // get configuration
         $config = &$this->__getConfiguration($namespace,$configName);
         if($config == null){
            $t->stop($id);
            return (string)'';
          // end if
         }
         else{

            // get configuration values
            $value = $config->getValue($this->__Language,$entry);

            if($value == null){

               // get environment variable from registry to display nice error message
               $reg = &Singleton::getInstance('Registry');
               $env = $reg->retrieve('apf::core','Environment');

               trigger_error('['.get_class($this).'->transform()] Given entry "'.$entry
                  .'" is not defined in section "'.$this->__Language.'" in configuration "'
                  .$env.'_'.$configName.'.ini" in namespace "'.$namespace.'" and context "'
                  .$this->__Context.'"!');
               $t->stop($id);
               return (string)'';

             // end if
            }
            else{
               $t->stop($id);
               return $value;
             // end if
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>