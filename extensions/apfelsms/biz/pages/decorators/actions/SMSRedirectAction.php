<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (02.10.12)
 *
 */
class SMSRedirectAction extends AbstractFrontcontrollerAction {


   /**
    * @var string
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;


   /**
    * @const string
    */
   const DECORATOR_TYPE = 'redirect';


   /**
    * @public
    *
    * @desc Checks if current page is decorated with redirect pageDec and redirects if applicable.
    */
   public function run() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

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

               header('Location: ' . $referencedPageURL, true, 307); // HTTP status code 303: Temporary Redirect
               exit;
            }

         }
      }
   }
}
