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
namespace APF\extensions\arraypager\data;

use APF\core\pagecontroller\APFObject;
use APF\core\session\Session;

/**
 * @package APF\extensions\arraypager\data
 * @class ArrayPagerMapper
 *
 * Represents the data layer of the array-pager.
 *
 * @author Lutz Mahlstedt
 * @version
 * Version 0.1, 21.12.2009<br />
 */
final class ArrayPagerMapper extends APFObject {

   /**
    * @protected
    *
    *  Returns the session key.
    *
    * @param string $stringPager name of the pager
    * @return string $stringSessionKey the desired session key
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   protected function getSessionKey($stringPager) {
      return 'ArrayPagerMapper_' . md5($stringPager);
   }

   /**
    * @public
    *
    *  Returns the number of entries of the desired object.
    *
    * @param string $stringPager name of the pager
    * @return integer $integerEntriesCount the number of entries
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function getEntriesCount($stringPager) {
      $integerEntriesCount = count($this->loadEntries($stringPager));

      return $integerEntriesCount;
   }

   /**
    * @public
    *
    *  Returns a list of the objects, that should be loaded for the current page.
    *
    * @param string $stringPager name of the pager
    * @return array $arrayEntries a list of entries
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function loadEntries($stringPager) {
      $objectSession = $this->getSessionManager();
      $stringSessionKey = $this->getSessionKey($stringPager);
      $arrayEntries = $objectSession->load($stringSessionKey);

      return $arrayEntries;
   }

   /**
    * @return Session
    */
   protected function getSessionManager() {
      return new Session('APF\extensions\arraypager\biz');
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    * @param array $arrayData a list of entries
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function registerEntries($stringPager, $arrayData) {
      $objectSession = $this->getSessionManager();
      $stringSessionKey = $this->getSessionKey($stringPager);
      $objectSession->save($stringSessionKey,
         $arrayData
      );
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function unregisterEntries($stringPager) {
      $objectSession = $this->getSessionManager();
      $stringSessionKey = $this->getSessionKey($stringPager);
      $objectSession->delete($stringSessionKey);
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    * @return boolean $mixedData whether pager exists or not
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function checkPager($stringPager) {
      $objectSession = $this->getSessionManager();
      $stringSessionKey = $this->getSessionKey($stringPager);
      $mixedData = $objectSession->load($stringSessionKey);

      $booleanExists = FALSE;

      if ($mixedData !== NULL) {
         $booleanExists = TRUE;
      }

      return $booleanExists;
   }
}
