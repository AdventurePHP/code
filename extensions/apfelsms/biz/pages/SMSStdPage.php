<?php

import('extensions::apfelsms::biz::pages', 'SMSPageInterface');

/**
 *
 * @package APFelSMS
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.1 (06.06.12)
 *             v0.2 (20.06.12) Added method isReference() (implementing the SMSPage interface)
 *             v0.3 (23.09.12) Removed bug in function laodParent(): No parent leads to an uncaught exception
 *
 */
class SMSStdPage extends APFObject implements SMSPage {


   /**
    * @var string
    */
   protected $id;


   /**
    * @var integer
    */
   protected $level;


   /**
    * @var string
    */
   protected $title = '';


   /**
    * @var string|null
    */
   protected $navTitle = null;


   /**
    * @var array
    */
   protected $js = array();


   /**
    * @var array
    */
   protected $css = array();


   /**
    * @var SMSPage|null
    */
   protected $__Parent = null;


   /**
    * @var SMSPage[]
    */
   protected $__Children = array();


   /**
    * @var array
    */
   public static $mapVars = array(
      'title' => '',
      'navTitle' => '',
      'js' => array(),
      'css' => array()
   );


   /**
    * @return string
    */
   public function getId() {

      return $this->id;
   }


   /**
    * @param $id
    */
   public function setId($id) {

      $this->id = $id;
   }


   /**
    * @return integer
    */
   public function getLevel() {

      return $this->level;
   }


   /**
    * @param $level
    */
   public function setLevel($level) {

      $this->level = $level;
   }


   /**
    * @return array
    */
   public function getCSS() {

      return $this->css;
   }


   /**
    * @param string $css
    * @param string|null $media
    */
   function addCSS($css, $media = null) {

      if (!empty($media)) {
         $this->css[$media] = $css;
         return;
      }

      $this->css[] = $css;

   }


   /**
    * @return array
    */
   public function getJS() {

      return $this->js;
   }


   /**
    * @param string $js
    */
   function addJS($js) {

      $this->js[] = $js;
   }


   /**
    * @return string
    */
   public function getTitle() {

      return $this->title;
   }


   /**
    * @param string $title
    */
   function setTitle($title) {

      $this->title = $title;
   }


   /**
    * @return string
    */
   public function getNavTitle() {

      if (empty($this->navTitle)) {
         return $this->title;
      }

      return $this->navTitle;

   }


   /**
    * @param string $navTitle
    */
   public function setNavTitle($navTitle) {

      $this->navTitle = $navTitle;
   }


   /**
    * @param Url $url
    * @return string
    */
   public function getLink(Url $url) {

      $url = $this->setPageRequestParamInURL($url);

      return LinkGenerator::generateUrl($url);
   }


   /**
    * @param Url $url
    * @return Url
    */
   public function setPageRequestParamInURL(Url $url) {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $pageRequestParam = $SMSM->getPageRequestParamName();
      $url->setQueryParameter($pageRequestParam, $this->getId());

      return $url;
   }


   /**
    * @return string
    */
   public function getTemplateName() {
      return $this->getId();
   }


   /**
    * @return SMSPage[]
    */
   public function getChildren() {

      if (empty($this->__Children)) {
         $this->loadChildren();
      }

      return $this->__Children;

   }


   /**
    *
    */
   protected function loadChildren() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $ids = $SMSM->getMapper()->getChildrenIds($this);

      if (count($ids) < 1) {
         return;
      }

      foreach ($ids AS $id) {
         $this->__Children[] = $SMSM->getPage($id);
      }

   }


   /**
    * @param boolean $includeMe Include this page in returned array
    * @return null|SMSPage[]
    */
   public function getSiblings($includeMe = false) {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $siblingIds = $SMSM->getMapper()->getSiblingAndOwnIds($this);

      if (count($siblingIds) < 1) {
         return null;
      }

      $siblings = array();
      foreach ($siblingIds AS $siblingId) {

         // skip this id, if not required
         if (!$includeMe && $siblingId == $this->getId()) {
            continue;
         }

         $siblings[] = $SMSM->getPage($siblingId);

      }

      return $siblings;

   }


   /**
    * @return SMSPage
    */
   public function getParent() {

      if (empty($this->__Parent)) {
         $this->loadParent();
      }

      return $this->__Parent;

   }


   /**
    *
    */
   protected function loadParent() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $id = $SMSM->getMapper()->getParentId($this);

      if ($id === null) {
         return;
      }

      $this->__Parent = $SMSM->getPage($id);

   }


   /**
    * @return SMSPage
    */
   public function getOuterPage() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      return $SMSM->getPage($this->getId());

   }


   /**
    * @return bool
    */
   public function isHidden() {

      return false;
   }


   /**
    * @return bool
    */
   public function isAccessProtected() {

      return false;
   }


   /**
    * @return bool
    */
   public function isReference() {

      return false;
   }


   /**
    * @return bool
    */
   public function isCurrentSite() {

      /* @var $SMSS SMSSite */
      $SMSS = $this->getDIServiceObject('extensions::apfelsms', 'Site');

      $currentSiteId = $SMSS->getCurrentPageId();
      $thisId = $this->getId();

      if ($thisId == $currentSiteId) {
         return true;
      }


      return false;

   }


   /**
    * @return boolean
    */
   public function isActive() {

      if ($this->isCurrentSite()) {
         return true;
      }

      $children = $this->getChildren();

      if (count($children) < 1) {
         return false;
      }

      foreach ($children AS $child) {
         if ($child->isActive()) {
            return true;
         }
      }

      return false;

   }


   /**
    * @param array $data
    *
    * @throws SMSException
    */
   public function mapData(array $data) {

      foreach ($data AS $prop => $val) {

         if (property_exists($this, $prop)) { // check if property is applicable
            $this->$prop = $val;
            // end if
         } elseif (is_array($val) && property_exists($this, $prop . 's')) { // try plural form, e.g. an XMl element name may be "requestParam" and belong to property "requestParams"
            $pluralProp = $prop . 's';
            $this->$pluralProp = $val;
         } else {
            throw new SMSException('[' . get_class($this) . '::mapData()] Mapper delivers data that is not applicable to ' . get_class($this) . ' object');
            // end else
         }

         // end foreach
      }
   }


}
