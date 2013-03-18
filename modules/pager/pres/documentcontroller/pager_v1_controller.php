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
use APF\tools\request\RequestHandler;

/**
 * @package modules::schwarzesbrett::pres::documentcontroller::pager
 * @class pager_v1_controller
 *
 * Implements a simple pager representation.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 06.08.2006<br />
 */
class pager_v1_controller extends BaseDocumentController {

   public function transformContent() {

      $buffer = (string)'';

      $count = count($this->attributes['Pages']);
      for ($i = 0; $i < $count; $i++) {

         if ($this->attributes['Pages'][$i]->isSelected() == true) {
            $tmplPage = &$this->getTemplate('Page_Selected');
         } else {
            $tmplPage = &$this->getTemplate('Page_Normal');
         }

         if (isset($this->attributes['AnchorName'])) {
            $tmplPage->setPlaceHolder('Link', $this->attributes['Pages'][$i]->getLink() . '#' . $this->attributes['AnchorName']);
         } else {
            $tmplPage->setPlaceHolder('Link', $this->attributes['Pages'][$i]->getLink());
         }
         $tmplPage->setPlaceHolder('Seite', $this->attributes['Pages'][$i]->getPage());

         $buffer .= $tmplPage->transformTemplate();

      }

      $tmplPage = &$this->getTemplate('Page_' . $this->language);
      $this->setPlaceHolder('Page', $tmplPage->transformTemplate());
      $this->setPlaceHolder('Content', $buffer);
   }

}
