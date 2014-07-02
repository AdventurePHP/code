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
namespace APF\modules\contact\pres\controller;

use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\contact\biz\ContactFormData;
use APF\modules\contact\biz\ContactManager;
use APF\tools\form\taglib\SelectBoxTag;

/**
 * Document controller for the form view of the contact module.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 03.06.2006<br />
 * Version 0.2, 04.06.2006<br />
 * Version 0.3, 23.02.2007<br />
 * Version 0.4, 12.09.2009 (Refactoring due to changes of the form tags)<br />
 * Version 0.5, 04.01.2014 (Button label now applied by <button:getstring /> tag)<br />
 */
class ContactFormController extends BaseDocumentController {

   /**
    * Displays the form and handles the user input.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 29.03.2007<br />
    * Version 0.4, 27.05.2007<br />
    * Version 0.5, 12.09.2009 (Refactoring due to changes of the form tags)<br />
    */
   public function transformContent() {

      $form = & $this->getForm('contact');

      // fill recipient list
      /* @var $recipients SelectBoxTag */
      $recipients = & $form->getFormElementByName('recipient');

      /* @var $cM ContactManager */
      $cM = & $this->getServiceObject('APF\modules\contact\biz\ContactManager');
      $recipientList = $cM->loadRecipients();

      for ($i = 0; $i < count($recipientList); $i++) {
         $recipients->addOption($recipientList[$i]->getName(), $recipientList[$i]->getId());
      }

      if ($form->isSent() && $form->isValid()) {

         $formData = new ContactFormData();

         $option = & $recipients->getSelectedOption();
         $recipientId = $option->getValue();
         $formData->setRecipientId($recipientId);

         $name = & $form->getFormElementByName('sender-name');
         $formData->setSenderName($name->getAttribute('value'));

         $email = & $form->getFormElementByName('sender-address');
         $formData->setSenderEmail($email->getAttribute('value'));

         $subject = & $form->getFormElementByName('subject');
         $formData->setSubject($subject->getAttribute('value'));

         $text = & $form->getFormElementByName('content');
         $formData->setMessage($text->getContent());

         /* @var $cM ContactManager */
         $cM = & $this->getServiceObject('APF\modules\contact\biz\ContactManager');
         $cM->sendContactForm($formData);

      } else {
         $form->transformOnPlace();
      }

   }

}
