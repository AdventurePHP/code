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
 * @package modules::schwarzesbrett::pres::documentcontroller::pager
 * @class SimplePagerController
 *
 * Implements a simple pager representation.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 06.08.2006<br />
 */
class SimplePagerController extends BaseDocumentController {

   public function transformContent() {

      $buffer = (string)'';

      /* @var $pages PageItem[] */
      $pages = $this->attributes['Pages'];
      $count = count($pages);
      for ($i = 0; $i < $count; $i++) {

         if ($pages[$i]->isSelected() == true) {
            $tmplPage = & $this->getTemplate('Page_Selected');
         } else {
            $tmplPage = & $this->getTemplate('Page_Normal');
         }

         if (isset($this->attributes['AnchorName'])) {
            $tmplPage->setPlaceHolder('Link', $pages[$i]->getLink() . '#' . $this->attributes['AnchorName']);
         } else {
            $tmplPage->setPlaceHolder('Link', $pages[$i]->getLink());
         }
         $tmplPage->setPlaceHolder('Seite', $pages[$i]->getPage());

         $buffer .= $tmplPage->transformTemplate();

      }

      $tmplPage = & $this->getTemplate('Page_' . $this->language);
      $this->setPlaceHolder('Page', $tmplPage->transformTemplate());
      $this->setPlaceHolder('Content', $buffer);
   }

}
