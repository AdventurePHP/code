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
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\pagecontroller\IncludeException;
use APF\extensions\apfelsms\biz\SMSException;
use APF\extensions\apfelsms\biz\SMSManager;

/**
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (11.08.12)
 */
class SMSImportDesignTag extends ImportTemplateTag {


   public function onParseTime() {


      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');
      $SMSS = $SMSM->getSite();


      ////
      // fetch not found template name

      $notFoundTemplate = $this->getAttribute('notFoundTemplate');

      if(empty($notFoundTemplate)) {
         $_404page = $SMSS->get404Page();

         if($_404page !== null) {
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

         // check if template is protected and fall back on notAllowedTemplate if necessary
         if($page->isAccessProtected()) {
            $notAllowedTemplate = $this->getAttribute('notAllowedTemplate');

            if(empty($notAllowedTemplate)) {
               $_403page = $SMSS->get403Page();

               if($_403page !== null) {
                  $notAllowedTemplate = $_403page->getTemplateName();
               }
            }
            $template = $notAllowedTemplate;
         }

      }
      catch (SMSException $e) {

         // on exception (e.g. given page id is not existent) fall back on notFoundTemplate
         $template = $notFoundTemplate;
      }


      // check if template name is set
      if(empty($template)) {
         throw new SMSException('[SMSImportDesignTag::onParseTime()] No template found.');
      }

      // inject template name
      $this->setAttribute('template', $template);


      ////
      // template inclusion


      try {
         parent::onParseTime();
      }
      catch (IncludeException $ie) {

         ////
         // fall back on notFoundTemplate if missing template was reason of exception

         $message = $ie->getMessage();
         $strPosDesign = strpos($message, 'Template'); // Changed from "Design" to "Template" since APF v2.0
         $strPosExists = strpos($message, 'not existent');

         if($strPosDesign === false || $strPosExists === false) {
            throw $ie; // other reason, abort and throw exception
         }

         $this->setAttribute('template', $notFoundTemplate);

         parent::onParseTime();

      }

   }

}
