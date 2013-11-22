<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\SMSManager;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @desc    : An alias page decorator uses the template name (and title/navTitle if own are not defined) of a referenced page
 *            There is no impact on the link (url returned by getLink()) or parents and children
 * @version :  v0.1 (06.06.12)
 *             v0.2 (15.03.13) fixed bug in method getNavTitle(), which returned title instead of navTitle
 */
class SMSAliasPageDec extends SMSAbstractPageDec {


   /**
    * @var SMSPage
    */
   protected $referencedPage;


   /**
    * @var string
    */
   protected $referencedPageId;


   public static $mapVars = array(
      'referencedPageId' => null
   );


   /**
    * @return SMSPage
    */
   public function getReferencedPage() {


      if (!($this->referencedPage instanceof SMSPage)) {

         /** @var SMSManager $SMSM */
         $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

         $this->referencedPage = $SMSM->getPage($this->referencedPageId);

      }

      return $this->referencedPage;

   }


   /**
    * @return string
    */
   public function getTemplateName() {


      return $this->getReferencedPage()->getTemplateName();
   }


   /**
    * @return string
    */
   public function getTitle() {


      // alias pages may have their own title (or must, but thats a question of data storage layout)
      $title = $this->SMSPage->getTitle();

      if (empty($title)) {
         return $this->getReferencedPage()->getTitle();
      }

      return $title;

   }


   /**
    * @return string
    */
   public function getNavTitle() {


      $navTitle = $this->SMSPage->getNavTitle();

      if (empty($navTitle)) {
         return $this->getReferencedPage()->getNavTitle();
      }

      return $navTitle;
   }


   /**
    * @return bool
    */
   public function isAccessProtected() {


      return $this->getReferencedPage()->isAccessProtected();
   }


   /**
    * @return bool
    */
   public function isReference() {


      return true;
   }

}
