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
namespace APF\extensions\apfelsms\biz\pages\stores;

use APF\core\pagecontroller\APFObject;
use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (04.08.12)
 *
 */
class SMSStdPageStore extends APFObject implements SMSPageStore {


   /**
    * @var SMSPage[]
    */
   protected $pages = array();


   /**
    * @param string|integer $id
    * @return SMSPage
    */
   public function getPage($id) {


      if (!isset($this->pages[$id])) {
         return null;
      }

      return $this->pages[$id];

   }


   /**
    * @param string|integer $id
    * @return boolean
    */
   public function isPageSet($id) {


      if (isset($this->pages[$id])) {
         return true;
      }

      return false;

   }


   /**
    * @param string|integer $id
    * @param SMSPage $page
    */
   public function setPage($id, SMSPage $page) {


      $this->pages[$id] = $page;
   }

}
