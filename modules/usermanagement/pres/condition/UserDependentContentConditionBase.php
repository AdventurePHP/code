<?php
namespace APF\modules\usermanagement\pres\condition;

use APF\core\pagecontroller\APFObject;

/**
 * @package APF\modules\usermanagement\pres\condition
 * @class UserDependentContentConditionBase
 *
 * Implements basic functionality for content conditions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
abstract class UserDependentContentConditionBase extends APFObject {

   private $options;

   public function setOptions(array $options) {
      $this->options = $options;
   }

   /**
    * @return array The list of options.
    */
   public function getOptions() {
      return $this->options;
   }

}