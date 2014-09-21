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
use APF\extensions\apfelsms\biz\pages\decorators\SMSPageDec;
use APF\extensions\apfelsms\biz\pages\decorators\SMSRedirectPageDec;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\tools\link\Url;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (02.10.12)
 *           v02. (09.03.13) Changed HTTP status code on redirect to 301 (moved permanently)
 *
 */
class SMSRedirectAction extends AbstractFrontcontrollerAction {


   /**
    * @var string $type
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;


   /**
    * @var string DECORATOR_TYPE
    */
   const DECORATOR_TYPE = 'redirect';


   /**
    * Checks if current page is decorated with redirect pageDec and redirects if applicable.
    */
   public function run() {


      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      $currentPage = $SMSM->getSite()->getCurrentPage();

      if ($currentPage instanceof SMSPageDec) {
         /** @var $currentPage SMSPageDec */

         $decoratorTypes = $currentPage->getDecoratorTypes();
         if (in_array(self::DECORATOR_TYPE, $decoratorTypes)) {

            /** @var $currentPage SMSRedirectPageDec
             *  (included in pageDecs)
             */

            /** @var $referencedPage SMSPage */
            $referencedPage = $currentPage->getReferencedPage();

            if ($currentPage->getId() != $referencedPage->getId()) {

               $referencedPageURL = $referencedPage->getLink(Url::fromCurrent(true));

               header('Location: ' . $referencedPageURL, true, 301); // HTTP status code 301: moved permanently
               exit;
            }

         }
      }
   }
}
