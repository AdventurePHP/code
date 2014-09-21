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
namespace APF\extensions\apfelsms\biz\pages\decorators\actions;

use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\session\Session;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\extensions\apfelsms\biz\SMSWrongParameterException;
use APF\tools\link\Url;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (03.10.12)
 *
 */
class SMSCurrentPageCheckAction extends AbstractFrontcontrollerAction {


   /**
    * @var string SESSION_NAMESPACE
    */
   const SESSION_NAMESPACE = 'APF\extensions\apfelsms\actions';


   /**
    * @var string SESSION_LOOPCOUNT_NAME
    */
   const SESSION_LOOPCOUNT_NAME = 'redirectLoopCount';


   /**
    * @var integer MAX_LOOPS
    */
   const MAX_LOOPS = 10;


   /**
    * @var string $type
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;


   /**
    * Checks for 404 and 403 errors and redirects to error pages.
    * If error page is current page, status code is adjusted.
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


      $sessM = new Session(self::SESSION_NAMESPACE);
      $sessM->save(
            self::SESSION_LOOPCOUNT_NAME,
            intval($sessM->load(self::SESSION_LOOPCOUNT_NAME, 0)) + 1
      );
   }


   /**
    * @return bool
    */
   protected function checkLoopsOK() {


      $sessM = new Session(self::SESSION_NAMESPACE);

      return $sessM->load(self::SESSION_LOOPCOUNT_NAME, 0) <= self::MAX_LOOPS;
   }


   /**
    *
    */
   protected function resetLoopCounter() {


      $sessM = new Session(self::SESSION_NAMESPACE);
      $sessM->delete(self::SESSION_LOOPCOUNT_NAME);
   }
}
