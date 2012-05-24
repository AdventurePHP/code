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
import('modules::guestbook::biz', 'GuestbookManager');
import('modules::guestbook::pres::documentcontroller', 'guestbookBaseController');

/**
 * @package modules::guestbook::pres::documentcontroller
 * @class guestbook_createentry_v1_controller
 *
 *  Document controller for 'createentry'.<br />
 *
 * @author Christian Achatz
 * @version
 *  Version 0.1, 12.04.2007<br />
 */
class guestbook_createentry_v1_controller extends guestbookBaseController {

   public function transformContent() {

      $form = &$this->getForm('GuestbookEntry');

      if ($form->isValid() && $form->isSent()) {

         $values = RequestHandler::getValues(array('Name', 'EMail', 'City', 'Website', 'ICQ', 'MSN', 'Skype', 'AIM', 'Yahoo', 'Text'));

         $entry = new Entry();
         $entry->setName($values['Name']);
         $entry->setEmail($values['EMail']);
         $entry->setCity($values['City']);
         $entry->setWebsite($values['Website']);
         $entry->setIcq($values['ICQ']);
         $entry->setMsn($values['MSN']);
         $entry->setSkype($values['Skype']);
         $entry->setAim($values['AIM']);
         $entry->setYahoo($values['Yahoo']);
         $entry->setText($values['Text']);

         $gM = &$this->getGuestbookManager();
         $gM->saveEntry($entry);

      } else {

         // label button
         $config = $this->getConfiguration('modules::guestbook', 'guestbook_lang.ini');
         $button = &$form->getFormElementByName('CreateGuestbookEntryButton');
         $button->setAttribute('value', $config->getSection($this->__Language)->getValue('CreateEntry.Form.Button'));

         $form->transformOnPlace();

      }

   }

}