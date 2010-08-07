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
    * @package tools::cache
    * @class CacheManagerFabric
    *
    * Fabric for the cache manager. Must be created singleton using the service manager.
    * Returns a cache manager instance by providing the desired config section.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.11.2008<br />
    */
   final class CacheManagerFabric extends APFObject {

      /**
       * @private
       * @var CacheManager[] Contains the cache manager instances.
       */
      private $cacheManagerCache = array();

      /**
       * @public
       *
       * Returns the cache manager instance by the desired config section.
       *
       * @param string $configSection the config section.
       * @return CacheManager The desired cache manager instance.
       * @throws InvalidArgumentException In case the given config section cannot be resolved.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.11.2008<br />
       * Version 0.2, 04.08.2010 (Bugfix: initializing two cache managers failed due to wrong service mode)<br />
       */
      public function &getCacheManager($configSection){

         if(!isset($this->cacheManagerCache[$configSection])){

            // load config
            $config = &$this->__getConfiguration('tools::cache','cacheconfig');
            $section = $config->getSection($configSection);

            if($section === null){
               $env = Registry::retrieve('apf::core','Environment');
               throw new InvalidArgumentException('[CacheManagerFabric::getCacheManager()] The desired config section "'
                  .$configSection.'" does not exist within the cache configuration. Please check '
                  .'your cache configuration ("'.$env.'_cacheconfig.ini") for namespace '
                  .'"tools::cache" and context "'.$this->__Context.'"!',E_USER_ERROR);
            }

            // create cache manager
            $this->cacheManagerCache[$configSection] =
               $this->__getAndInitServiceObject('tools::cache','CacheManager',$section,'NORMAL');

          // end if
         }

         return $this->cacheManagerCache[$configSection];

       // end function
      }

    // end class
   }
?>