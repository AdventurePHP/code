<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::kontakt4::biz','oFormData');
   import('modules::kontakt4::biz','oRecipient');
   import('modules::kontakt4::data','contactMapper');
   import('tools::mail','mailSender');
   import('tools::link','linkHandler');


   /**
   *  @namespace modules::kontakt4::biz
   *  @class contactManager
   *
   *  Implementiert die Businessschicht des Kontaktformulars.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2006<br />
   */
   class contactManager extends coreObject
   {

      function contactManager(){
      }


      /**
      *  @public
      *
      *  Sendet das Kontaktformular ab und zeigt die Bestätigungsseite an.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 21.06.2006 (Kontaktformular wird nun auch an den Absender geschickt.)<br />
      *  Version 0.4, 09.03.2007 (Absender erhält eine andere Mail wie der Empfänger)<br />
      *  Version 0.5, 31.03.2007 (Umstellung auf neuen mailSender)<br />
      *  Version 0.6, 04.01.2008 (Weiterleitungslink für nicht REWRITE-URL-Betrieb korrigiert)<br />
      */
      function sendContactForm($oFD){

         // contactMapper holen
         $cM = &$this->__getServiceObject('modules::kontakt4::data','contactMapper');


         // E-Mail für Empfänger aufsetzen
         $MAIL = &$this->__getAndInitServiceObject('tools::mail','mailSender','Kontaktformular');


         // Empfaenger setzen
         $Recipient = $cM->loadRecipientPerId($oFD->get('RecipientID'));
         $MAIL->setRecipient($Recipient->get('Adresse'),$Recipient->get('Name'));

         // Text einsetzen
         $Text = 'Sehr geehrter Empfänger, sehr geehrte Empfängerin,';
         $Text .= "\n\n";
         $Text .= $oFD->get('SenderName').' (E-Mail: '.$oFD->get('SenderEMail').') hat Ihnen folgende Nachricht über das Kontaktformular zukommen lassen:';
         $Text .= "\n\n\n";
         $Text .= $oFD->get('Text');
         $MAIL->setContent($Text);

         // Betreff setzen
         $MAIL->setSubject($oFD->get('Subject'));

         // Mail senden
         $MAIL->sendMail();


         // E-Mail für Absender aufsetzen
         $MAIL->clearRecipients();
         $MAIL->clearCCRecipients();
         $MAIL->clearContent();

         // Empfaenger setzen
         $MAIL->setRecipient($oFD->get('SenderEMail'),$oFD->get('SenderName'));

         // Text einsetzen
         $Text = 'Sehr geehrter Empfänger, sehr geehrte Empfängerin,';
         $Text .= "\n\n";
         $Text .= 'Ihre Anfrage wurde an die Kontaktperson "'.$Recipient->get('Name').'" weitergeleitet. Wir setzen uns baldmöglich mit Ihnen in Verbindung!';
         $Text .= "\n\n";
         $Text .= 'Hier nochmals Ihr Anfragetext:';
         $Text .= "\n";
         $Text .= $oFD->get('Text');
         $MAIL->setContent($Text);

         // Betreff setzen
         $MAIL->setSubject($oFD->get('Subject'));

         // Mail senden
         $MAIL->sendMail();


         // Bestätigungsseite anzeigen
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('pagepart' => 'meldung'));

         $Reg = &Singleton::getInstance('Registry');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');

         if($URLRewriting != true){
            $Link = str_replace('&amp;','&',$Link);
          // end if
         }

         header('Location: '.$Link);

       // end function
      }


      /**
      *  @public
      *
      *  Läd die in der Konfiguration abgelegten Empfänger-Objekte.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      */
      function loadRecipients(){
         $cM = & $this->__getServiceObject('modules::kontakt4::data','contactMapper');
         return $cM->loadRecipients();
       // end function
      }

    // end class
   }
?>