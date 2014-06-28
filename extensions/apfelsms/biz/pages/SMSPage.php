<?php
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
    * @param $id string|integer
    */
   public function setId($id);


   /**
    * @return integer
    */
   public function getLevel();


   /**
    * @param integer $lvl
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
