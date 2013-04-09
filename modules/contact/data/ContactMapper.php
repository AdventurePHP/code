<?php
namespace APF\modules\contact\data;

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
use APF\core\pagecontroller\APFObject;
use APF\modules\contact\biz\ContactFormRecipient;

/**
 * @package APF\modules\contact\data
 * @class ContactMapper
 *
 * Implements the data layer component of the contact form.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 03.06.2006<br />
 * Version 0.2, 04.06.2006<br />
 */
class ContactMapper extends APFObject {

   /**
    * @public
    *
    * Loads the list of recipients.
    *
    * @return ContactFormRecipient[] The list contact reasons.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 04.03.2007 (Switched to ConfigurationManager)<br />
    */
   public function loadRecipients() {

      $config = $this->getConfiguration('APF\modules\contact', 'recipients.ini');

      /* @var $recipients ContactFormRecipient[] */
      $recipients = array();
      foreach ($config->getSectionNames() as $name) {

         $section = $config->getSection($name);

         $count = count($recipients);
         $recipients[$count] = new ContactFormRecipient();

         preg_match('/Contact ([0-9]+)/i', $name, $matches);
         $recipients[$count]->setId($matches[1]);

         $recipients[$count]->setName($section->getValue('recipient-name'));
         $recipients[$count]->setEmailAddress($section->getValue('recipient-address'));

      }

      return $recipients;

   }

   /**
    * @public
    *
    * Loads an recipient by a given id.
    *
    * @param string $id The id if the contact reason to load.
    * @return ContactFormRecipient|null The desired contact reason.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 04.03.2007<br />
    */
   public function loadRecipientPerId($id) {

      /* @var $recipients ContactFormRecipient[] */
      $recipients = $this->loadRecipients();

      if (!is_array($recipients)) {
         return null;
      }

      for ($i = 0; $i < count($recipients); $i++) {
         if ($recipients[$i]->getId() == $id) {
            return $recipients[$i];
         }
      }

      return null;

   }

}
