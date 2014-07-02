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
namespace APF\extensions\apfelsms\biz\pages;

use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.2 (18.06.12)
 *             v0.3 (20.06.12) Added method isReference()
 */
interface SMSPage {


   /**
    * @abstract
    * @return string|integer
    */
   public function getId();


   /**
    * @abstract
    * @param $id string|integer
    */
   public function setId($id);


   /**
    * @abstract
    * @return integer
    */
   public function getLevel();


   /**
    * @abstract
    * @param integer $lvl
    */
   public function setLevel($lvl);


   /**
    * @abstract
    * @return string
    */
   public function getTitle();


   /**
    * @abstract
    * @param string $title
    */
   public function setTitle($title);


   /**
    * @abstract
    * @return string
    */
   public function getNavTitle();


   /**
    * @abstract
    * @param string $navTitle
    */
   public function setNavTitle($navTitle);


   /**
    * @abstract
    * @return array
    */
   public function getCSS();


   /**
    * @abstract
    * @param string $css
    * @param string|null $media
    */
   public function addCSS($css, $media = null);


   /**
    * @abstract
    * @return array
    */
   public function getJS();


   /**
    * @abstract
    * @param string $js
    */
   public function addJS($js);


   /**
    * @abstract
    * @param Url $url
    * @return string
    */
   public function getLink(Url $url);


   /**
    * @abstract
    * @param Url $url
    * @return Url
    */
   public function setPageRequestParamInURL(Url $url);


   /**
    * @abstract
    * @return string
    */
   public function getTemplateName();


   /**
    * @abstract
    * @return boolean
    */
   public function isHidden();


   /**
    * @abstract
    * @return boolean
    */
   public function isAccessProtected();


   /**
    * @abstract
    * @return boolean
    */
   public function isReference();


   /**
    * @abstract
    * @return boolean
    */
   public function isCurrentPage();


   /**
    * @abstract
    * @return boolean
    */
   public function isActive();


   /**
    * @abstract
    * @return SMSPage
    */
   public function getParent();


   /**
    * @abstract
    * @param boolean $includeMe
    * @return SMSPage[]
    */
   public function getSiblings($includeMe = false);


   /**
    * @abstract
    * @return SMSPage[]
    */
   public function getChildren();


   /**
    * @abstract
    * @return SMSPage
    */
   public function getOuterPage();


   /**
    * @abstract
    * @param array $data
    */
   public function mapData(array $data);

}
