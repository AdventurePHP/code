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

   import('modules::guestbook::biz','GuestbookManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');

   /**
    *  @package modules::guestbook::pres::documentcontroller
    *  @class guestbook_adminedit_v1_controller
    *
    *  Implementiert den DocumentController f�r das Stylesheet 'adminedit.html'.<br />
    *
    *  @author Christian Sch�fer
    *  @version
    *  Version 0.1, 05.05.2007<br />
    */
   class guestbook_adminedit_v1_controller extends guestbookBaseController {

      /**
       *  @public
       *
       *  Implementiert die asbtrakte Methode "transformContent" aus "APFObject".<br />
       *
       *  @author Christian Sch�fer
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      public function transformContent() {

         $values = RequestHandler::getValues(array('Name','EMail','City','Website','ICQ','MSN','Skype','AIM','Yahoo','Text','entryid'));

         $form = &$this->getForm('GuestbookEntry');

         if ($form->isSent()) {

            if ($form->isValid()) {

               $gM = &$this->getGuestbookManager();

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
               $entry->setId($values['entryid']);

               $gM->saveEntry($entry);

            }

         } else {

            $gM = &$this->getGuestbookManager();
            $entry = $gM->loadEntry($values['entryid']);

            $Name = & $form->getFormElementByName('Name');
            $Name->setAttribute('value', $entry->getName());

            $EMail = & $form->getFormElementByName('EMail');
            $EMail->setAttribute('value', $entry->getEmail());

            $City = & $form->getFormElementByName('City');
            $City->setAttribute('value', $entry->getCity());

            $Website = & $form->getFormElementByName('Website');
            $Website->setAttribute('value', $entry->getWebsite());

            $ICQ = & $form->getFormElementByName('ICQ');
            $ICQ->setAttribute('value', $entry->getIcq());

            $MSN = & $form->getFormElementByName('MSN');
            $MSN->setAttribute('value', $entry->getMsn());

            $Skype = & $form->getFormElementByName('Skype');
            $Skype->setAttribute('value', $entry->getSkype());

            $AIM = & $form->getFormElementByName('AIM');
            $AIM->setAttribute('value', $entry->getAim());

            $Yahoo = & $form->getFormElementByName('Yahoo');
            $Yahoo->setAttribute('value', $entry->getYahoo());

            $Text = & $form->getFormElementByName('Text');
            $Text->setContent($entry->getText());

            $ID = & $form->getFormElementByName('entryid');
            $ID->setAttribute('value', $entry->getId());

         }

         $this->setPlaceHolder('Form', $form->transformForm());

      }

   }
?>