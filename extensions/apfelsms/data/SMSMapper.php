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
namespace APF\extensions\apfelsms\data;

use APF\extensions\apfelsms\biz\pages\decorators\SMSPageDec;
use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.2 (18.06.2012)
 *             v0.2 (28.04.2013) Added getPageType()-method to support multiple page types in one application
 */
interface SMSMapper {


   /**
    * @abstract
    * @param SMSPage $page
    * @param SMSPage
    */
   public function mapPage(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return SMSPage
    */
   public function mapPageWithoutDecorators(SMSPage $page);


   /**
    * @abstract
    * @param SMSPageDec $pageDec
    * @param string|integer $pageId
    * @return SMSPageDec
    */
   public function mapPageDec(SMSPageDec $pageDec, $pageId);


   /**
    * @abstract
    * @param string|integer $pageId
    * @return mixed
    * @since v0.3
    */
   public function getPageType($pageId);


   /**
    * @abstract
    * @param SMSPage $page
    * @return array
    */
   public function getChildrenIds(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return array
    */
   public function getSiblingAndOwnIds(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return string
    */
   public function getParentId(SMSPage $page);

}
