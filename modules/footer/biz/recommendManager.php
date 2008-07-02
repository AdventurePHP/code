<?php
   import('modules::footer::biz','oFormData');
   import('tools::mail','mailSender');
   import('tools::link','linkHandler');


   /**
   *  Package modules::footer::biz<br />
   *  Klasse recommendManager<br />
   *  Implementiert die Businessschicht des Weiterempfehlen-Formulars<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 03.06.2006<br />
   */
   class recommendManager extends coreObject
   {

      function recommendManager(){
      }


      /**
      *  Funktion sendContactForm()  [public/static]<br />
      *  Sendet das Weiterempfehlungs-Formular ab und zeigt die Bestätigungsseite an.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 17.12.2006<br />
      */
      function sendContactForm($oFD){

         // E-Mail aufsetzen
         $MAIL = &$this->__getAndInitServiceObject('tools::mail','mailSender','Standard');

         // Absender setzen
         $MAIL->setSender($oFD->get('SenderEMail'),$oFD->get('SenderName'));

         // Empfaenger setzen
         $MAIL->setRecipient($oFD->get('RecipientEMail'),$oFD->get('RecipientName'));

         // Betreff setzen
         $MAIL->setSubject($oFD->get('Subject'));

         // Text einsetzen
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         $RecommendedPage = linkHandler::generateLink($URLBasePath.'/',array('Seite' => $oFD->get('Page')));

         $MAIL->setContent('Sehr geehrte Damen und Herren,

Sie haben eine Weiterempfehlung für '.$RecommendedPage.' von

'.$oFD->get('SenderName').' ('.$oFD->get('SenderEMail').')

mit folgendem Text erhalten:

'.$oFD->get('Text'));

         // Mail senden
         $MAIL->sendMail();

         // Bestätigungsseite anzeigen
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('pagepart' => 'message','Weiterempfehlen' => ''));
         header('Location: '.$Link);

       // end function
      }

    // end class
   }
?>
