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
namespace APF\modules\pager\biz;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;

/**
 * Implements the factory of the pager manager. Initializes concrete PagerManager
 * instances and caches them for further usage.
 * Application sample:
 * <pre>$pMF = &$this->getServiceObject(PagerManagerFabric::class);
 * $pM = &$pMF->getPagerManager('{ConfigSection}', {AdditionalParamArray});</pre>
 *
 * @deprecated Please use initialization of the PagerManager via the DIServiceManager instead.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.04.2007<br />
 */
final class PagerManagerFabric extends APFObject {

   /**
    * @var $pager PagerManager[] Cache list if concrete pager manager instances.
    */
   private $pager = array();

   /**
    * Returns a reference on the desired pager manager. Initializes newly created ones.
    *
    * @deprecated Please use initialization of the PagerManager via the DIServiceManager instead.
    *
    * @param string $configString The configuration/initialization string (configuration section name).
    *
    * @return PagerManager Reference on the desired PagerManager instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.04.2007<br />
    * Version 0.2, 24.01.2009 (Moved the additional params to the loadEntries() method. Refactored the method.)<br />
    */
   public function &getPagerManager($configString) {

      // create cache key
      $pagerHash = md5($configString);

      // initialize desired pager lazily
      if (!isset($this->pager[$pagerHash])) {
         $this->pager[$pagerHash] = $this->getServiceObject(PagerManager::class, [],
               APFService::SERVICE_TYPE_NORMAL);
         $this->pager[$pagerHash]->init($configString);
      }

      return $this->pager[$pagerHash];

   }

}
