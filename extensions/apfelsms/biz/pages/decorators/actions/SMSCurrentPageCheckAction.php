<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\actions;

use APF\core\session\SessionManager;
use APF\tools\link\LinkGenerator;

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (03.10.12)
 *
 */
class SMSCurrentPageCheckAction extends AbstractFrontcontrollerAction {


   /**
    * @const string
    */
   const SESSION_NAMESPACE = 'extensions::apfelsms::actions';


   /**
    * @const string
    */
   const SESSION_LOOPCOUNT_NAME = 'redirectLoopCount';


   /**
    * @const integer
    */
   const MAX_LOOPS = 10;


   /**
    * @var string
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;


   /**
    * @public
    *
    * @desc Checks for 404 and 403 errors and redirects to error pages.
    *       If error page is current page, status code is adjusted.
    */
   public function run() {


      // check loop counter (to protect against infinite redirect loops)
      if (!$this->checkLoopsOK()) {
         header('X-APFelSMS: Infinite redirection loop detected', true, 500); // HTTP status code 500: Server error
      }


      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');
      $SMSS = $SMSM->getSite();


      // first, check for exception caused by errors/invalid page id
      try {
         $currentPage = $SMSM->getPage($SMSS->getCurrentPageId());
      } catch (SMSWrongParameterException $e) {

         // invalid page id

         $this->incrementLoopCounter();

         $error404URL = $SMSS->get404Page()->getLink(Url::fromCurrent(true));
         header('Location: ' . $error404URL, true, 307); // HTTP status code 307: Temporary Redirect
         exit;

      }


      // check access protection
      if ($currentPage->isAccessProtected()) {

         // page is access protected

         $this->incrementLoopCounter();

         $error403URL = $SMSS->get403Page()->getLink(Url::fromCurrent(true));
         header('Location: ' . $error403URL, true, 307); // HTTP status code 307: Temporary Redirect
         exit;

      }

      // check if 404 error page
      if ($SMSS->currentIs404Page()) {
         header('X-APFelSMS: Invalid page id', true, 404);
      }

      //check if 403 error page
      if ($SMSS->currentIs403Page()) {
         header('X-APFelSMS: Access protected page', true, 403);
      }


      // clear loop counter (no redirection done during action run)
      $this->resetLoopCounter();

   }


   /**
    *
    */
   protected function incrementLoopCounter() {
      $sessM = new SessionManager(self::SESSION_NAMESPACE);
      $sessM->saveSessionData(
         self::SESSION_LOOPCOUNT_NAME,
            $sessM->loadSessionData(self::SESSION_LOOPCOUNT_NAME, 0) + 1
      );
   }


   /**
    * @return bool
    */
   protected function checkLoopsOK() {
      $sessM = new SessionManager(self::SESSION_NAMESPACE);
      return $sessM->loadSessionData(self::SESSION_LOOPCOUNT_NAME, 0) <= self::MAX_LOOPS;
   }


   /**
    *
    */
   protected function resetLoopCounter() {
      $sessM = new SessionManager(self::SESSION_NAMESPACE);
      $sessM->deleteSessionData(self::SESSION_LOOPCOUNT_NAME);
   }
}
