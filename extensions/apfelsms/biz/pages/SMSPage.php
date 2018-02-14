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
namespace APF\extensions\apfelsms\biz\pages;

use APF\tools\link\Url;

/**
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.2 (18.06.12)
 *             v0.3 (20.06.12) Added method isReference()
 */
interface SMSPage {


   /**
    * @return string|integer
    */
   public function getId();


   /**
    * @param string|integer $id
    */
   public function setId($id);


   /**
    * @return integer
    */
   public function getLevel();


   /**
    * @param int $lvl
    */
   public function setLevel($lvl);


   /**
    * @return string
    */
   public function getTitle();


   /**
    * @param string $title
    */
   public function setTitle($title);


   /**
    * @return string
    */
   public function getNavTitle();


   /**
    * @param string $navTitle
    */
   public function setNavTitle($navTitle);


   /**
    * @return array
    */
   public function getCSS();


   /**
    * @param string $css
    * @param string|null $media
    */
   public function addCSS($css, $media = null);


   /**
    * @return array
    */
   public function getJS();


   /**
    * @param string $js
    */
   public function addJS($js);


   /**
    * @param Url $url
    *
    * @return string
    */
   public function getLink(Url $url);


   /**
    * @param Url $url
    *
    * @return Url
    */
   public function setPageRequestParamInURL(Url $url);


   /**
    * @return string
    */
   public function getTemplateName();


   /**
    * @return boolean
    */
   public function isHidden();


   /**
    * @return boolean
    */
   public function isAccessProtected();


   /**
    * @return boolean
    */
   public function isReference();


   /**
    * @return boolean
    */
   public function isCurrentPage();


   /**
    * @return boolean
    */
   public function isActive();


   /**
    * @return SMSPage
    */
   public function getParent();


   /**
    * @param boolean $includeMe
    *
    * @return SMSPage[]
    */
   public function getSiblings($includeMe = false);


   /**
    * @return SMSPage[]
    */
   public function getChildren();


   /**
    * @return SMSPage
    */
   public function getOuterPage();


   /**
    * @param array $data
    */
   public function mapData(array $data);

}
