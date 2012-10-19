<?php
/**
 * @package APFelSMS
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (11.08.12)

 */
class SMSImportDesignTaglib extends core_taglib_importdesign {


   public function onParseTime() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');
      $SMSS = $SMSM->getSite();


      ////
      // fetch not found template name

      $notFoundTemplate = $this->getAttribute('notFoundTemplate');

      if (empty($notFoundTemplate)) {
         $_404page = $SMSS->get404Page();

         if ($_404page !== null) {
            $notFoundTemplate = $_404page->getTemplateName();
         }
      }


      ////
      // try to load template for current/given page id

      try {

         // get page id, current as default
         $currentPageId = $SMSS->getCurrentPageId();
         $pageId = $this->getAttribute('pageId', $currentPageId);

         // get template for given page
         $page = $SMSM->getPage($pageId);
         $template = $page->getTemplateName();

         // check if template is protected and fall back on notAllowedTemplate if neccessary
         if ($page->isAccessProtected()) {
            $notAllowedTemplate = $this->getAttribute('notAllowedTemplate');

            if (empty($notAllowedTemplate)) {
               $_403page = $SMSS->get403Page();

               if ($_403page !== null) {
                  $notAllowedTemplate = $_403page->getTemplateName();
               }
            }
            $template = $notAllowedTemplate;
         }

      } catch (SMSException $e) {

         // on exception (e.g. given page id is not existent) fall back on notFoundTemplate
         $template = $notFoundTemplate;
      }


      // check if template name is set
      if (empty($template)) {
         throw new SMSException('[taglib_sms:importdesign] No template found.');
      }

      // inject template name
      $this->setAttribute('template', $template);


      ////
      // template inclusion


      try {
         parent::onParseTime();
      } catch (IncludeException $ie) {

         ////
         // fall back on notFoundTemplate if missing template was reason of exception

         $message = $ie->getMessage();
         $strPosDesign = strpos($message, 'Design');
         $strPosExists = strpos($message, 'not existent');

         if ($strPosDesign === false || $strPosExists === false) {
            throw $ie; // other reason, abort and throw exception
         }

         $this->setAttribute('template', $notFoundTemplate);

         parent::onParseTime();

      }

   }

}
