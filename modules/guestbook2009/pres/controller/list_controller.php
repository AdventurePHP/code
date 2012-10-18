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
import('modules::guestbook2009::biz', 'GuestbookService');

/**
 * @package modules::guestbook2009::pres::controller
 * @class list_controller
 *
 * Implements the document controller for the guestbook's list view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2009<br />
 * Version 0.2, 06.06.2009 (Added dynamic link generation)<br />
 */
class list_controller extends base_controller {

   public function transformContent() {

      /* @var $gS GuestbookService */
      $gS = &$this->getDIServiceObject('modules::guestbook2009::biz', 'GuestbookService');
      $entryList = $gS->loadPagedEntryList();

      $tmpl_entry = &$this->getTemplate('entry');
      $buffer = (string)'';
      foreach ($entryList as $entry) {

         $editor = $entry->getEditor();
         $tmpl_entry->setPlaceHolder('name', $editor->getName());
         $tmpl_entry->setPlaceHolder('website', $editor->getWebsite());

         $tmpl_entry->setPlaceHolder('title', $entry->getTitle());
         $tmpl_entry->setPlaceHolder('text', $entry->getText());

         $creationTimestamp = $entry->getCreationTimestamp();
         $tmpl_entry->setPlaceHolder('time', date('H:i:s', strtotime($creationTimestamp)));
         $tmpl_entry->setPlaceHolder('date', date('d.m.Y', strtotime($creationTimestamp)));

         $buffer .= $tmpl_entry->transformTemplate();
      }
      $this->setPlaceHolder('content', $buffer);

      // add the pager
      $this->setPlaceHolder('pager', $gS->getPagerOutput());

      // add dyamic link
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('gbview' => 'create')));
      $this->setPlaceHolder('createlink', $link);
   }

}
