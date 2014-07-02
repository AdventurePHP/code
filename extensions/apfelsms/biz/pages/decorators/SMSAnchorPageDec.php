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
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\tools\link\Url;

/**
 * Adds an anchor to page URL
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (22.11.2013)
 */
class SMSAnchorPageDec extends SMSAbstractPageDec {


   /**
    * The anchor
    *
    * @var string $anchor
    */
   protected $anchor = '';


   public static $mapVars = array(
         'anchor' => ''
   );


   /**
    * @param Url $url
    *
    * @return string
    */
   public function getLink(Url $url) {


      $url->setAnchor($this->anchor);

      return $this->SMSPage->getLink($url);

   }


   /**
    * @param string $anchor
    */
   public function setAnchor($anchor) {

      $this->anchor = $anchor;
   }


   /**
    * @return string
    */
   public function getAnchor() {

      return $this->anchor;
   }

}
