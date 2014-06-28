<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\tools\link\Url;

/**
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (22.11.2013)
 * @desc    : Adds an anchor and uses template name of parent page
 *            This is a combination of the anchor decorator and the alias decorator with reference to the parent
 *
 */
class SMSParentAnchorPageDec extends SMSAnchorPageDec {


   /**
    * @return SMSPage
    */
   protected function getOuterParent() {


      return $this->getOuterPage()->getParent();
   }


   /**
    * @param Url $url
    *
    * @return string
    */
   public function getLink(Url $url) {


      $url->setAnchor($this->anchor);

      $parent = $this->getOuterParent();

      return $parent->getLink($url);

   }


   /**
    * @return string
    */
   public function getTemplateName() {


      $parent = $this->getOuterParent();

      return $parent->getTemplateName();

   }


   /**
    * @return string
    */
   public function getTitle() {


      $title = $this->SMSPage->getTitle();

      if (empty($title)) {
         return $this->getOuterParent()->getTitle();
      }

      return $title;
   }


   /**
    * @return string
    */
   public function getNavTitle() {


      $navTitle = $this->SMSPage->getTitle();

      if (empty($navTitle)) {
         return $this->getOuterParent()->getNavTitle();
      }

      return $navTitle;
   }


   /**
    * @return bool
    */
   public function isAccessProtected() {


      return $this->getOuterParent()->isAccessProtected();
   }


   /**
    * @return bool
    */
   public function isReference() {


      return true;
   }

}
