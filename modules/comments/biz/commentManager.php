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
import('modules::comments::data', 'commentMapper');
import('tools::link', 'LinkGenerator');
import('tools::string', 'StringAssistant');
import('core::session', 'SessionManager');
import('tools::http', 'HeaderManager');

/**
 * @package modules::comments::biz
 * @class commentManager
 *
 *  Implements the business component of the comment module.
 *
 * @author Christian Achatz
 * @version
 *  Version 0.1, 22.08.2007<br />
 *  Version 0.2, 28.12.2007 (Added CAPTCHA support)<br />
 */
class commentManager extends APFObject {

   /**
    * @protected
    *  Schl�ssel f�r die auszuliefernde Kategorie.
    */
   protected $categoryKey;

   /**
    * @public
    *
    *  Implementierung der abstrakte "init()"-Methode.<br />
    *
    * @param string $initParam Kategorie-Schl�ssel
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 13.04.2007<br />
    *  Version 0.2, 28.12.2007 (Captcha-Unterst�tzung hinzugef�gt)<br />
    */
   public function init($initParam) {
      $this->categoryKey = $initParam;
   }

   /**
    * @public
    *
    *  L�d eine Liste von Kommentaren.<br />
    *
    * @return Array $Entries Liste von ArticleComment-Objekten
    *
    * @author Christian Sch�fer
    * @version
    *  Version 0.1, 21.08.2007<br />
    *  Version 0.2, 01.09.2007 (Verwendung des PagerManagers auf loadEntriesByAppDataComponent() umgestellt)<br />
    */
   public function loadEntries() {

      $pMF = &$this->getServiceObject('modules::pager::biz', 'PagerManagerFabric');
      $pM = &$pMF->getPagerManager('ArticleComments');

      $M = &$this->getServiceObject('modules::comments::data', 'commentMapper');
      return $pM->loadEntriesByAppDataComponent($M, 'loadArticleCommentByID', array('CategoryKey' => $this->categoryKey));
   }

   /**
    * @public
    *
    *  Gibt die HTML-Ausgabe des Pagers zur�ck.<br />
    *
    * @param string $anchorName the desired anchor name (optional)
    * @return string $pagerOutput the HTML code of the pager
    *
    * @author Christian Sch�fer
    * @version
    *  Version 0.1, 21.08.2007<br />
    *  Version 0.2, 29.08.2007 (Added the anchor name)<br />
    *  Version 0.3, 24.01.2009 (Introduced the $anchorName parameter)<br />
    */
   public function getPager($anchorName = null) {
      $pMF = &$this->getServiceObject('modules::pager::biz', 'PagerManagerFabric');
      $pM = &$pMF->getPagerManager('ArticleComments');
      $pM->setAnchorName($anchorName);
      return $pM->getPager(array('CategoryKey' => $this->categoryKey));
   }

   /**
    * @public
    *
    *  Gibt die URL-Parameter des Pagers zur�ck.<br />
    *
    * @return array $URLParameter Pager-URL-Parameter
    *
    * @author Christian Sch�fer
    * @version
    *  Version 0.1, 21.08.2007<br />
    */
   public function getURLParameter() {
      $pMF = &$this->getServiceObject('modules::pager::biz', 'PagerManagerFabric');
      $pM = &$pMF->getPagerManager('ArticleComments');
      return $pM->getPagerURLParameters();
   }

   /**
    * @public
    *
    *  Speichert einen Kommentar-Eintrag.<br />
    *
    * @param ArticleComment $articleComment ArticleComment-Objekt
    *
    * @author Christian Schäfer
    * @version
    *  Version 0.1, 21.08.2007<br />
    *  Version 0.2, 28.12.2007<br />
    *  Version 0.3, 02.02.2008<br />
    */
   public function saveEntry($articleComment) {

      $M = &$this->getServiceObject('modules::comments::data', 'commentMapper');

      $articleComment->setCategoryKey($this->categoryKey);
      $M->saveArticleComment($articleComment);

      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('coview' => 'listing')));
      HeaderManager::forward($link . '#comments');
   }

}
