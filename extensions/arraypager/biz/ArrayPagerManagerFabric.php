<?php
namespace APF\extensions\arraypager\biz;

use APF\extensions\arraypager\biz\ArrayPagerManager;

/**
 * @package extensions::arraypager::biz
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
    * Cache list if concrete pager manager instances.
    */
   private $pagers = array();

   /**
    * @public
    *
    * Returns a reference on the desired pager manager. Initializes newly created ones.
    *
    * @param string $stringConfig The configuration/initialization string (configuration section name).
    * @return ArrayPagerManager Reference on the desired PagerManager instance.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function &getArrayPagerManager($stringConfig) {

      // create cache key
      $stringPagerHash = md5($stringConfig);

      // initialize desired pager lazily
      if (isset($this->pagers[$stringPagerHash]) === FALSE) {
         $this->pagers[$stringPagerHash] = $this->getAndInitServiceObject('extensions::arraypager::biz',
            'ArrayPagerManager',
            $stringConfig,
            APFService::SERVICE_TYPE_NORMAL
         );
      }

      return $this->pagers[$stringPagerHash];
   }

}
