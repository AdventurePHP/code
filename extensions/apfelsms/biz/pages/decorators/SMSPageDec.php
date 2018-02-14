<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 *          : v0.2 (20.08.13) SMSPageDec now extends SMSPage, (re)moved mapData() method
 *
 */
interface SMSPageDec extends SMSPage {


   /**
    * @return SMSPage
    */
   public function getPage();


   /**
    * @param SMSPage $page
    */
   public function setPage(SMSPage $page);


   /**
    * @return string
    */
   public function getDecType();


   /**
    * @param string $type
    */
   public function setDecType($type);


   /**
    * @param array $giveThrough
    *
    * @return array
    */
   public function getDecoratorTypes(array $giveThrough = []);


   /**
    * @param array $giveThrough
    *
    * @return array
    */
   public function getAllDecorators(array $giveThrough = []);


   /**
    * @param $name
    *
    * @return boolean
    */
   public function providesDecMethod($name);


   /**
    * @return SMSPage
    */
   public function getPageWithoutDecorators();

}
