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

   import('modules::kontakt4::biz','oFormData');
   import('modules::kontakt4::biz','oRecipient');
   import('modules::kontakt4::data','contactMapper');
   import('tools::mail','mailSender');
   import('tools::link','LinkHandler');
   import('tools::http','HeaderManager');

   /**
    * @package modules::kontakt4::biz
    * @class contactManager
    *
    * Implements the business component for the contact form.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    */
   class contactManager extends APFObject {

      public function contactManager(){
      }

      /**
       * @public
       *
       * Sends the contact form and displays the thanks page.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       * Version 0.3, 21.06.2006 (Now an additional mail is sent to the sender)<br />
       * Version 0.4, 09.03.2007<br />
       * Version 0.5, 31.03.2007<br />
       * Version 0.6, 04.01.2008 (Corrected url generatin for non-rewrite urls)<br />
       */
      public function sendContactForm($formData){

         $cM = &$this->__getServiceObject('modules::kontakt4::data','contactMapper');

         // set up the mail sender
         $MAIL = &$this->__getAndInitServiceObject('tools::mail','mailSender','ContactForm');

         $Recipient = $cM->loadRecipientPerId($formData->get('RecipientID'));
         $MAIL->setRecipient($Recipient->get('Adresse'),$Recipient->get('Name'));

         $Text = 'Sehr geehrter Empfänger, sehr geehrte Empfängerin,';
         $Text .= "\n\n";
         $Text .= $formData->get('SenderName').' (E-Mail: '.$formData->get('SenderEMail').') hat Ihnen folgende Nachricht �ber das Kontaktformular zukommen lassen:';
         $Text .= "\n\n\n";
         $Text .= $formData->get('Text');
         $MAIL->setContent($Text);

         $MAIL->setSubject($formData->get('Subject'));

         // send mail to notify the recipient
         $MAIL->sendMail();

         $MAIL->clearRecipients();
         $MAIL->clearCCRecipients();
         $MAIL->clearContent();

         $MAIL->setRecipient($formData->get('SenderEMail'),$formData->get('SenderName'));

         $Text = 'Sehr geehrter Empfänger, sehr geehrte Empfängerin,';
         $Text .= "\n\n";
         $Text .= 'Ihre Anfrage wurde an die Kontaktperson "'.$Recipient->get('Name').'" weitergeleitet. Wir setzen uns baldm�glich mit Ihnen in Verbindung!';
         $Text .= "\n\n";
         $Text .= 'Hier nochmals Ihr Anfragetext:';
         $Text .= "\n";
         $Text .= $formData->get('Text');
         $MAIL->setContent($Text);

         $MAIL->setSubject($formData->get('Subject'));

         // send mail to notify the sender
         $MAIL->sendMail();

         // redirect to the thanks page to avoid F5 bugs!
         $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'],array('pagepart' => 'meldung'));

         $reg = &Singleton::getInstance('Registry');
         $urlRewriting = $reg->retrieve('apf::core','URLRewriting');

         if($urlRewriting != true){
            $link = str_replace('&amp;','&',$link);
          // end if
         }

         HeaderManager::forward($link);

       // end function
      }

      /**
       * @public
       *
       * Loads the configuration of the recipients.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       */
      public function loadRecipients(){
         $cM = & $this->__getServiceObject('modules::kontakt4::data','contactMapper');
         return $cM->loadRecipients();
       // end function
      }

    // end class
   }
?>