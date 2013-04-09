<?php
namespace APF\extensions\arraypager\biz;

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
use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\extensions\arraypager\biz\ArrayPagerManager;

/**
 * @package APF\extensions\arraypager\biz
 * @class ArrayPagerManagerFabric
 *
 * Implements the factory of the array-pager manager. Initializes concrete ArrayPagerManager
 * instances and caches them for further usage.
 * Application sample:
 * <pre>$aPMF = $this->getServiceObject ('extensions::arraypager::biz','ArrayPagerManagerFabric');
 * $aPM = $aPMF->getArrayPagerManager ('{ConfigSection}');</pre>
 *
 * @author Lutz Mahlstedt
 * @version
 * Version 0.1, 21.12.2009<br />
 */
final class ArrayPagerManagerFabric extends APFObject {

   /**
    * @private
    * @var ArrayPagerManager[] Cache list if concrete pager manager instances.
    */
   private $pagers = array();

   /**
    * @public
    *
    * Returns a reference on the desired pager manager. Initializes newly created ones.
    *
    * @param string $config The configuration/initialization string (configuration section name).
    * @return ArrayPagerManager Reference on the desired PagerManager instance.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function &getArrayPagerManager($config) {

      // create cache key
      $cacheKey = md5($config);

      // initialize desired pager lazily
      if (isset($this->pagers[$cacheKey]) === false) {
         $this->pagers[$cacheKey] = $this->getServiceObject(
            'APF\extensions\arraypager\biz\ArrayPagerManager',
            APFService::SERVICE_TYPE_NORMAL
         );
         $this->pagers[$cacheKey]->init($config);
      }

      return $this->pagers[$cacheKey];
   }

}
