<?php
   import('modules::kontakt4::biz','oFormData');
   import('modules::kontakt4::biz','oRecipient');
   import('modules::kontakt4::data','contactMapper');
   import('tools::mail','mailSender');
   import('tools::link','linkHandler');


   /**
   *  @package modules::kontakt4::biz
   *  @class contactManager
   *
   *  Implementiert die Businessschicht des Kontaktformulars.<br />
   *
   *  @author Christian Sch�fer
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
      *  Sendet das Kontaktformular ab und zeigt die Best�tigungsseite an.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 21.06.2006 (Kontaktformular wird nun auch an den Absender geschickt.)<br />
      *  Version 0.4, 09.03.2007 (Absender erh�lt eine andere Mail wie der Empf�nger)<br />
      *  Version 0.5, 31.03.2007 (Umstellung auf neuen mailSender)<br />
      *  Version 0.6, 04.01.2008 (Weiterleitungslink f�r nicht REWRITE-URL-Betrieb korrigiert)<br />
      */
      function sendContactForm($oFD){

         // contactMapper holen
         $cM = &$this->__getServiceObject('modules::kontakt4::data','contactMapper');


         // E-Mail f�r Empf�nger aufsetzen
         $MAIL = &$this->__getAndInitServiceObject('tools::mail','mailSender','Kontaktformular');


         // Empfaenger setzen
         $Recipient = $cM->loadRecipientPerId($oFD->get('RecipientID'));
         $MAIL->setRecipient($Recipient->get('Adresse'),$Recipient->get('Name'));

         // Text einsetzen
         $Text = 'Sehr geehrter Empf�nger, sehr geehrte Empf�ngerin,';
         $Text .= "\n\n";
         $Text .= $oFD->get('SenderName').' (E-Mail: '.$oFD->get('SenderEMail').') hat Ihnen folgende Nachricht �ber das Kontaktformular zukommen lassen:';
         $Text .= "\n\n\n";
         $Text .= $oFD->get('Text');
         $MAIL->setContent($Text);

         // Betreff setzen
         $MAIL->setSubject($oFD->get('Subject'));

         // Mail senden
         $MAIL->sendMail();


         // E-Mail f�r Absender aufsetzen
         $MAIL->clearRecipients();
         $MAIL->clearCCRecipients();
         $MAIL->clearContent();

         // Empfaenger setzen
         $MAIL->setRecipient($oFD->get('SenderEMail'),$oFD->get('SenderName'));

         // Text einsetzen
         $Text = 'Sehr geehrter Empf�nger, sehr geehrte Empf�ngerin,';
         $Text .= "\n\n";
         $Text .= 'Ihre Anfrage wurde an die Kontaktperson "'.$Recipient->get('Name').'" weitergeleitet. Wir setzen uns baldm�glich mit Ihnen in Verbindung!';
         $Text .= "\n\n";
         $Text .= 'Hier nochmals Ihr Anfragetext:';
         $Text .= "\n";
         $Text .= $oFD->get('Text');
         $MAIL->setContent($Text);

         // Betreff setzen
         $MAIL->setSubject($oFD->get('Subject'));

         // Mail senden
         $MAIL->sendMail();


         // Best�tigungsseite anzeigen
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
      *  L�d die in der Konfiguration abgelegten Empf�nger-Objekte.<br />
      *
      *  @author Christian Sch�fer
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