<?php
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\Document;
use APF\extensions\apfelsms\biz\SMSException;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\tools\link\Url;
use APF\tools\string\StringAssistant;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.1 (26.08.12)
 *             v0.2 (22.09.12) Added magic page ids __current, __referer and __start
 *                  (23.09.12) Added magic page ids __parent, __next and __prev
 *             v0.3 (30.10.12) Added support for title attribute
 *             v0.4 (30.09.12) Added URL XSS protection for magic id "__referer" (host check)
 *
 */
class SMSPageLinkTag extends Document {


   public static $template = '<a href="{LINK}" title="{TITLE}">{TEXT}</a>';


   public function transform() {

      $pageId = $this->getAttribute('id');

      if (empty($pageId)) {
         throw new \InvalidArgumentException('No page id defined', E_USER_ERROR);
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');


      ////
      // evaluate magic page ids

      $magicPageIds = array('__current', '__referer', '__start', '__parent', '__next', '__prev');
      if (in_array($pageId, $magicPageIds)) {

         switch ($pageId) {
            case '__current': // current page

               $pageId = $SMSM->getSite()->getCurrentPageId();

               break;
            case '__referer': // previous visited page

               // get http referer

               if (!isset($_SERVER['HTTP_REFERER'])) {
                  // fallback on current page
                  $pageId = $SMSM->getSite()->getCurrentPageId();
                  break;
               }

               $pageRequestParamName = $SMSM->getPageRequestParamName();

               $refererUrl = Url::fromReferer(true);
               $currentUrl = Url::fromCurrent(true);

               // protection against url xss
               if ($refererUrl->getHost() == $currentUrl->getHost()) {
                  $pageId = $refererUrl->getQueryParameter($pageRequestParamName);
               }

               if (empty($pageId)) {
                  // safety fallback on current page 
                  $pageId = $SMSM->getSite()->getCurrentPageId();
               }

               // test for valid page id
               try {
                  $SMSM->getPage($pageId);
               } catch (SMSException $e) {
                  $pageId = $SMSM->getSite()->getCurrentPageId();
               }

               break;
            case '__start':

               $pageId = $SMSM->getSite()->getStartPageId();

               break;
            case '__parent':

               $currentPage = $SMSM->getSite()->getCurrentPage();


               $parentPage = $currentPage->getParent();

               if ($parentPage === null) {
                  $pageId = $SMSM->getSite()->getStartPageId();
               } else {
                  $pageId = $parentPage->getId();
               }

               break;
            case '__next':
            case '__prev':

               $currentPage = $SMSM->getSite()->getCurrentPage();

               $siblings = $currentPage->getSiblings(true);

               if (empty($siblings)) {
                  return '';
               }
               
               /** @var $visibleSiblings \APF\extensions\apfelsms\biz\pages\SMSPage[] */
               $visibleSiblings = array();
               foreach ($siblings AS $sibling) {
                  if ($sibling->isHidden()) {
                     continue;
                  }
                  $visibleSiblings[] = $sibling;
               }

               $currentIndex = null;
               $currentId = $currentPage->getId();
               foreach ($visibleSiblings AS $index => $sibling) {
                  if ($sibling->getId() == $currentId) {
                     $currentIndex = $index;
                     break;
                  }
               }

               if ($pageId == '__next') {
                  $summand = 1;
               } else {
                  $summand = -1;
               }

               $index = $currentIndex + $summand;

               if (!isset($visibleSiblings[$index])) {
                  return '';
               }

               $pageId = $visibleSiblings[$index]->getId();

               break;
         }

      }


      // fetch page object
      try {
         $page = $SMSM->getPage($pageId);
      } catch (SMSException $e) {
         // fallback on 404 error page (not found)
         $page = $SMSM->getSite()->get404Page();
      }


      $content = $this->getContent();

      if (empty($content)) {
         $content = StringAssistant::escapeSpecialCharacters($page->getNavTitle());
      }

      $title = StringAssistant::escapeSpecialCharacters($this->getAttribute('title', $page->getTitle()));

      $link = StringAssistant::escapeSpecialCharacters($page->getLink(Url::fromCurrent()->resetQuery()));

      return str_replace(
         array('{LINK}', '{TITLE}', '{TEXT}'),
         array($link, $title, $content),
         self::$template
      );
   }

}
