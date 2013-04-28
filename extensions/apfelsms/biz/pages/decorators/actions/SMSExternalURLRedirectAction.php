<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\actions;

use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\extensions\apfelsms\biz\pages\decorators\SMSExternalURLPageDec;
use APF\extensions\apfelsms\biz\pages\decorators\SMSPageDec;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (02.10.12)
 *
 */
class SMSExternalURLRedirectAction extends AbstractFrontcontrollerAction {


   /**
    * @var string
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;


   /**
    * @const string
    */
   const DECORATOR_TYPE = 'externalURL';


   /**
    * @public
    *
    * @desc Checks if current page is decorated with externalURL pageDec and redirects to external URL if applicable.
    */
   public function run() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      $currentPage = $SMSM->getSite()->getCurrentPage();

      if ($currentPage instanceof SMSPageDec) {
         /** @var $currentPage SMSPageDec */

         $decoratorTypes = $currentPage->getDecoratorTypes();
         if (in_array(self::DECORATOR_TYPE, $decoratorTypes)) {

            /**
             * @var $currentPage SMSExternalUrlPageDec
             * (included in pageDecs)
             */
            $externalURLString = $currentPage->getExternalURL();

            $externalURLObject = Url::fromString($externalURLString);
            $currentURLObject = Url::fromCurrent();

            // protection against infinite redirection loops
            if (
               $externalURLObject->getHost() != $currentURLObject->getHost()
               ||
               $externalURLObject->getPath() != $currentURLObject->getPath()
            ) {
               header('Location: ' . $externalURLString, true, 301); // HTTP status code 301: moved permanently
               exit;
            }
         }
      }
   }

}
