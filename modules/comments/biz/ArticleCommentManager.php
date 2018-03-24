<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\modules\comments\biz;

use APF\core\http\mixins\GetRequestResponse;
use APF\core\pagecontroller\APFObject;
use APF\core\service\APFDIService;
use APF\modules\comments\data\ArticleCommentMapper;
use APF\modules\pager\biz\PagerManager;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 *  Implements the business component of the comment module.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.08.2007<br />
 * Version 0.2, 28.12.2007 (Added CAPTCHA support)<br />
 */
class ArticleCommentManager extends APFObject {

   use GetRequestResponse;

   /**
    * Category key.
    *
    * @var string $categoryKey
    */
   protected $categoryKey;

   /**
    * @param string $categoryKey The category to load the comments with.
    */
   public function __construct($categoryKey) {
      $this->categoryKey = $categoryKey;
   }

   /**
    * Loads a list of comment entries.
    *
    * @return ArticleComment[] The list of desired entries.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.08.2007<br />
    * Version 0.2, 01.09.2007 (Switched to PagerManager::loadEntriesByAppDataComponent())<br />
    */
   public function loadEntries() {
      $pager = $this->getPagerManager();
      /* @var $m APFObject */
      $m = $this->getServiceObject(ArticleCommentMapper::class);

      return $pager->loadEntriesByAppDataComponent($m, 'loadArticleCommentByID', ['CategoryKey' => $this->categoryKey]);
   }

   /**
    * @return PagerManager|APFDIService
    */
   private function getPagerManager() {
      return $this->getDIServiceObject('APF\modules\comments', 'CommentsPager');
   }

   /**
    * Returns the HTML representation of the pager.
    *
    * @param string $anchorName The desired anchor name (optional).
    *
    * @return string The HTML code of the pager.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.08.2007<br />
    * Version 0.2, 29.08.2007 (Added the anchor name)<br />
    * Version 0.3, 24.01.2009 (Introduced the $anchorName parameter)<br />
    */
   public function getPager($anchorName = null) {
      $pager = $this->getPagerManager();
      $pager->setAnchorName($anchorName);

      return $pager->getPager(['CategoryKey' => $this->categoryKey]);
   }

   /**
    * Returns the url parameters the pager used.
    *
    * @return string[] Pager URL parameters.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.08.2007<br />
    */
   public function getURLParameter() {
      return $this->getPagerManager()->getPagerURLParameters();
   }

   /**
    * Saves a comment.
    *
    * @param ArticleComment $articleComment The entry to save.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.08.2007<br />
    * Version 0.2, 28.12.2007<br />
    * Version 0.3, 02.02.2008<br />
    */
   public function saveEntry(ArticleComment $articleComment) {

      /* @var $M ArticleCommentMapper */
      $M = $this->getServiceObject(ArticleCommentMapper::class);

      $articleComment->setCategoryKey($this->categoryKey);
      $M->saveArticleComment($articleComment);

      $link = LinkGenerator::generateUrl(
            Url::fromCurrent()
                  ->mergeQuery(['coview' => 'listing'])
                  ->setAnchor('comments')
      );
      $this->getResponse()->forward($link);
   }

}
