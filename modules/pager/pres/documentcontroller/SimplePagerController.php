<?php
namespace APF\modules\pager\pres\documentcontroller;

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
use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\pager\biz\PageItem;

/**
 * Implements a simple pager representation.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 06.08.2006<br />
 */
class SimplePagerController extends BaseDocumentController {

   public function transformContent() {

      $buffer = (string) '';

      $document = $this->getDocument();

      /* @var $pages PageItem[] */
      $pages = $document->getAttribute('Pages');
      $count = count($pages);
      for ($i = 0; $i < $count; $i++) {

         if ($pages[$i]->isSelected() == true) {
            $tmplPage = & $this->getTemplate('Page_Selected_' . $this->getLanguage());
         } else {
            $tmplPage = & $this->getTemplate('Page_Normal_' . $this->getLanguage());
         }

         $anchorName = $document->getAttribute('AnchorName');
         if (empty($anchorName)) {
            $tmplPage->setPlaceHolder('Link', $pages[$i]->getLink());
         } else {
            $tmplPage->setPlaceHolder('Link', $pages[$i]->getLink() . '#' . $anchorName);
         }

         $tmplPage->setPlaceHolder('Page', $pages[$i]->getPage());

         $buffer .= $tmplPage->transformTemplate();

      }

      $tmplPage = & $this->getTemplate('Page_' . $this->getLanguage());
      $this->setPlaceHolder('Page', $tmplPage->transformTemplate());
      $this->setPlaceHolder('Content', $buffer);
   }

}
