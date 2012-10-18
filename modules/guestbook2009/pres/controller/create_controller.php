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
import('modules::guestbook2009::biz', 'User');
import('modules::guestbook2009::biz', 'Entry');
import('tools::link', 'LinkGenerator');

/**
 * @package modules::guestbook2009::pres::controller
 * @class create_controller
 *
 * Implements the document controller, that handles the "create new entry" view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.05.2009<br />
 */
class create_controller extends base_controller {

   public function transformContent() {

      $form = &$this->getForm('create_entry');

      if ($form->isSent() && $form->isValid()) {

         // Fill domain objects by extracting the values
         // from the form elements directly.
         $name = &$form->getFormElementByName('name');
         $email = &$form->getFormElementByName('email');
         $website = &$form->getFormElementByName('website');

         $user = new User();
         $user->setName($name->getAttribute('value'));
         $user->setEmail($email->getAttribute('value'));
         $user->setWebsite($website->getAttribute('value'));

         $title = &$form->getFormElementByName('title');
         $text = &$form->getFormElementByName('text');
         $entry = new Entry();
         $entry->setTitle($title->getAttribute('value'));
         $entry->setText($text->getContent());

         $entry->setEditor($user);

         // Save the entry using the business component.
         $gbService = &$this->getDIServiceObject('modules::guestbook2009::biz', 'GuestbookService');
         $gbService->saveEntry($entry);
      }

      // set language dependent button label by using the
      // language and context information of the current
      // DOM node.
      $config = $this->getConfiguration('modules::guestbook2009::pres', 'language.ini');
      $buttonLabel = $config->getSection($this->__Language)->getValue('form.label.button');
      $button = &$form->getFormElementByName('send');
      $button->setAttribute('value', $buttonLabel);

      // Transform on definition place to render
      // the content within the surrounding div.
      $form->transformOnPlace();

      // add dynamic link
      $this->setPlaceHolder('overviewlink', LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('gbview' => 'list'))));
   }

}

?>