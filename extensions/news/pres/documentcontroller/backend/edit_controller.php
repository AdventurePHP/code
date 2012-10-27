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
import('extensions::news::pres::documentcontroller', 'news_base_controller');

/**
 * @package extensions::news::pres::documentcontroller::backend
 * @class edit_controller
 *
 * Document controller for editing and creating news.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 0.1,  17.06.2011<br />
 */
class edit_controller extends news_base_controller {

   public function transformContent() {

      $appKey = $this->getAppKey();
      $form = $this->getForm('edit');

      $cfg = $this->getConfiguration('extensions::news', 'labels.ini');
      $lang = $cfg->getSection($this->getLanguage());

      $newsManager = $this->getNewsManager();

      // If an id is given, an existing news should be updated,
      // so we check here if it really exists.
      $editId = RequestHandler::getValue('editnewsid');
      if ($editId !== null && $editId !== '') {
         $news = $newsManager->getNewsById((int)$editId);
         if ($news === null) {
            $this->getTemplate('notfound')->transformOnPlace();
            return;
         }
      }

      // Get the form elements we need later
      $formTitle = $form->getFormElementByID('news-edit-title');
      $formText = $form->getFormElementByID('news-edit-text');
      $formUser = $form->getFormElementByID('news-edit-user');
      $button = $form->getFormElementByName('send');

      // If input is valid, save the news.
      if ($form->isSent() && $form->isValid()) {
         if (!isset($news)) {
            $news = new News();
            $news->setAppKey($appKey);
         }

         $news->setTitle($formTitle->getAttribute('value'));
         $news->setAuthor($formUser->getAttribute('value'));
         $news->setText($formText->getContent());

         $newsManager->saveNews($news);
      }

      // Pre-fill form elements if an existing news should be updated
      // and take care of the right text of the button.
      if ($editId !== null && $editId !== '') {
         $buttonValue = $lang->getValue('Form.Button.Edit');

         // retrieve the charset from the registry to guarantee interoperability!
         $charset = Registry::retrieve('apf::core', 'Charset');

         $formTitle->setAttribute('value', htmlspecialchars($news->getTitle(), ENT_QUOTES, $charset, false));
         $formUser->setAttribute('value', htmlspecialchars($news->getAuthor(), ENT_QUOTES, $charset, false));
         $formText->setContent(htmlspecialchars($news->getText(), ENT_QUOTES, $charset, false));
      } else {
         $buttonValue = $lang->getValue('Form.Button.New');

         // Clear form inputs
         if ($form->isSent() && $form->isValid()) {
            $formText->setContent('');
            $formTitle->setAttribute('value', '');
            $formUser->setAttribute('value', '');
         }
      }
      $button->setAttribute('value', $buttonValue);

      $form->transformOnPlace();
   }

   /**
    * Overwriting parent's function
    *
    * @return string The application identifier (for login purposes).
    */
   protected function getAppKey() {
      return $this->__Document->getParentObject()->getAttribute('app-ident', $this->getContext());
   }

}
