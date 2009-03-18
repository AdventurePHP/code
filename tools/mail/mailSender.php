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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::validator','myValidator');


   /**
   *  @namespace tools::mail
   *  @class mailSender
   *
   *  Provides a mail() wrapper.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 09.06.2004<br />
   *  Version 0.2, 04.01.2005<br />
   *  Version 0.3, 27.04.2005<br />
   *  Version 0.4, 14.01.2006 (Redesign / introduced new technology)<br />
   *  Version 0.5, 21.06.2006 (Added CC recipients capability)<br />
   *  Version 0.5, 30.03.2007 (Switched to the ConfigurationManager)<br />
   *  Version 0.5, 03.09.2007 (Added BCC recipients capability and futher header)<br />
   */
   class mailSender extends coreObject
   {

      /**
      *  @private
      *  Indicates the sender.
      *  <pre>$this->__Sender['Name']  = '...';
      *  $this->__Sender['EMail'] = '...';</pre>
      */
      var $__Sender = array();


      /**
      *  @private
      *  Indicates the recipients.
      *  <pre>$this->__Recipients[0]['Name']  = '...';
      *  $this->__Recipients[0]['EMail'] = '...';
      *  $this->__Recipients[1]['Name']  = '...';
      *  $this->__Recipients[1]['EMail'] = '...';</pre>
      */
      var $__Recipients = array();


      /**
      *  @private
      *  Indicates the CC recipients.
      *  <pre>$this->__CCRecipients[0]['Name']  = '...';
      *  $this->__CCRecipients[0]['EMail'] = '...';
      *  $this->__CCRecipients[1]['Name']  = '...';
      *  $this->__CCRecipients[1]['EMail'] = '...';</pre>
      */
      var $__CCRecipients = array();

      /**
      *  @private
      *  Indicates the BCC recipients.
      *  <pre>$this->__BCCRecipients[0]['Name']  = '...';
      *  $this->__BCCRecipients[0]['EMail'] = '...';
      *  $this->__BCCRecipients[1]['Name']  = '...';
      *  $this->__BCCRecipients[1]['EMail'] = '...';</pre>
      */
      var $__BCCRecipients = array();


      /**
      *  @private
      *  Header of the mail.
      */
      var $__MailHeader = null;


      /**
      *  @private
      *  The mail's subject.
      */
      var $__Subject;


      /**
      *  @private
      *  Content of the mail.
      */
      var $__Content = '';


      /**
      *  @private
      *  Content type of the mail.
      */
      var $__ContentType;


      /**
      *  @private
      *  Return path.
      */
      var $__ReturnPath;


      /**
      *  @private
      *  EOL sign.
      */
      var $__EOL = "\n";


      /**
      *  @private
      *  CRLF sign.
      */
      var $__CRLF = "\r\n";


      function mailSender(){
      }


      /**
      *  @public
      *
      *  Initializes the conmponent. Loads the configuration file for the current instance of the mailSender.
      *
      *  @param string $configSection the name of the config section to initialize the component with
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.01.2006<br />
      *  Version 0.2, 15.01.2006<br />
      *  Version 0.3, 30.03.2007 (Renamed to init() to be able to use it with the ServiceManager)<br />
      *  Version 0.4, 31.03.2007 (Text and recipients are now cleared to allow multiple usage)<br />
      */
      function init($configSection = 'Standard'){

         // load config
         $Config = &$this->__getConfiguration('tools::mail','mailsender');

         // set sender
         $this->__Sender['Name'] = trim($Config->getValue($configSection,'Mail.SenderName'));
         $this->__Sender['EMail'] = trim($Config->getValue($configSection,'Mail.SenderEMail'));

         // set ContentType
         $this->__ContentType = trim($Config->getValue($configSection,'Mail.ContentType'));

         // set ReturnPath
         $this->__ReturnPath = trim($Config->getValue($configSection,'Mail.ReturnPath'));

         // reset text and recipients to avoid interference during multiple usage
         $this->clearRecipients();
         $this->clearCCRecipients();
         $this->clearContent();

       // end function
      }


      /**
      *  @private
      *
      *  Generates the mail's header.
      *
      *  @return string $header the final header of the mail
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 03.01.2005<br />
      *  Version 0.3, 17.01.2005<br />
      *  Version 0.4, 14.01.2006<br />
      *  Version 0.5, 21.06.2005 (Added the CC recipients)<br />
      *  Version 0.6, 03.09.2007 (Added some more headers and BCC recipients)<br />
      */
      function __generateHeader(){

         // Header-Puffer initialisieren
         $MailHeader = (string)'';
         $MailHeader .= 'From: "'.($this->__Sender['Name']).'" <'.($this->__Sender['EMail']).'>'.$this->__EOL;

         // CC-Empfaenger zum Header hinzufügen
         if(count($this->__CCRecipients) > 0){

            $CCRecipients = array();

            for($i = 0; $i < count($this->__CCRecipients); $i++){
               $CCRecipients[] = '"'.($this->__CCRecipients[$i]['Name']).'" <'.($this->__CCRecipients[$i]['EMail']).'>';
             // end for
            }

            $MailHeader .= 'CC: '.implode(', ',$CCRecipients).''.$this->__EOL;

          // end if
         }

         // BCC-Empfaenger zum Header hinzufügen
         if(count($this->__BCCRecipients) > 0){

            $BCCRecipients = array();

            for($i = 0; $i < count($this->__BCCRecipients); $i++){
               $BCCRecipients[] = '"'.($this->__BCCRecipients[$i]['Name']).'" <'.($this->__BCCRecipients[$i]['EMail']).'>';
             // end for
            }

            $MailHeader .= 'BCC: '.implode(', ',$BCCRecipients).''.$this->__EOL;

          // end if
         }

         // Header vervollstänigen
         $MailHeader .= 'X-Sender: APF-E-Mail-Client'.$this->__EOL;
         $MailHeader .= 'X-Mailer: PHP/'.phpversion().''.$this->__EOL;
         $MailHeader .= 'X-Priority: 3'.$this->__EOL; //1 Dringende E-Mail, 3: Priorität Normal
         $MailHeader .= 'MIME-Version: 1.0'.$this->__EOL;
         $MailHeader .= 'Return-Path: '.($this->__ReturnPath).''.$this->__EOL;
         $MailHeader .= 'Content-Type: '.($this->__ContentType).''.$this->__EOL;

         // Zusätzliche Header setzen, falls vorhanden
         if($this->__MailHeader != null){
            $MailHeader .= $this->__MailHeader;
          // end if
         }

         // Fertigen Header zurückgeben
         return $MailHeader;

       // end function
      }


      /**
      *  @public
      *
      *  Allows to add headers to the mail.
      *
      *  @param string $header the header to add
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 03.09.2007<br />
      */
      function addHeader($header = ''){

         if(strpos($header,':') !== false){
            $this->__MailHeader .= $header.''.$this->__EOL;
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Allows you to add recipients.
      *
      *  @param string $recipientEMail the email of the BCC recipient
      *  @param string $recipientName the name of the BCC recipient
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setRecipient($recipientEMail,$recipientName){

         if(myValidator::validateEMail($recipientEMail)){

            $this->__Recipients[count($this->__Recipients)] = array('Name' => $recipientName,
                                                                    'EMail' => $recipientEMail
                                                                   );

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Allows you to clear the recipients.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.03.2007<br />
      */
      function clearRecipients(){
         $this->__Recipients = array();
       // end function
      }


      /**
      *  @public
      *
      *  Allows you to add CC recipients.
      *
      *  @param string $recipientEMail the email of the CC recipient
      *  @param string $recipientName the name of the CC recipient
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.06.2006<br />
      */
      function setCCRecipient($recipientEMail,$recipientName){

         if(myValidator::validateEMail($recipientEMail)){

            $this->__CCRecipients[count($this->__CCRecipients)] = array('Name' => $recipientName,
                                                                        'EMail' => $recipientEMail
                                                                       );
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Allows you to clear CC recipients.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.03.2007<br />
      */
      function clearCCRecipients(){
         $this->__CCRecipients = array();
       // end function
      }


      /**
      *  @public
      *
      *  Allows you to add BCC recipients.
      *
      *  @param string $recipientEMail the email of the BCC recipient
      *  @param string $recipientName the name of the BCC recipient
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 03.09.2007<br />
      */
      function setBCCRecipient($recipientEMail,$recipientName){

         if(myValidator::validateEMail($recipientEMail)){

            $this->__BCCRecipients[count($this->__BCCRecipients)] = array('Name' => $recipientName,
                                                                          'EMail' => $recipientEMail
                                                                         );
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Allows you to clear BCC recipients.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.03.2007<br />
      */
      function clearBCCRecipients(){
         $this->__BCCRecipients = array();
       // end function
      }


      /**
      *  @public
      *
      *  Allows you to maipulate the sender.
      *
      *  @param string $senderEMail the email of the sender
      *  @param string $senderName the name of the sender
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.12.2006<br />
      */
      function setSender($senderEMail,$senderName){

         if(myValidator::validateEMail($senderEMail)){

            $this->__Sender['Name'] = $senderName;
            $this->__Sender['EMail'] = $senderEMail;

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Allows you to add content to the mail.
      *
      *  @param string $content the content to add to the mail
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setContent($content){
         $this->__Content .= $content.''.$this->__EOL;
       // end function
      }


      /**
      *  @public
      *
      *  Resets the content of the mail.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.03.2007<br />
      */
      function clearContent(){
         $this->__Content = (string)'';
       // end function
      }


      /**
      *  @public
      *
      *  Sets the subject of the mail.
      *
      *  @param string $subject the mail's subject
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setSubject($subject){
         $this->__Subject = $subject;
       // end function
      }


      /**
      *  @public
      *
      *  Send an email to the recipients configured.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 03.01.2005<br />
      *  Version 0.3, 17.01.2005<br />
      *  Version 0.4, 14.01.2006<br />
      */
      function sendMail(){

         // Header generieren
         $Header = $this->__generateHeader();

         // LOG initialisieren
         $L = &Singleton::getInstance('Logger');

         // E-Mail senden
         $versMails = array();

         for($i = 0; $i < count($this->__Recipients); $i++){

            $result = @mail($this->__Recipients[$i]['EMail'],$this->__Subject,$this->__Content,$Header);

            if($result == 1 || $result == true){
               $L->logEntry('mail','[   OK   ] Mail an '.$this->__Recipients[$i]['EMail'].' senden.','INFO');
               $versMails[] = '1';
             // end if
            }
            else{
               $L->logEntry('mail','[ Fehler ] Mail an '.$this->__Recipients[$i]['EMail'].' senden.','ERROR');
             // end if
            }

          // end for
         }


         // Rückgabe-Werte erzeugen
         $return['AnzEMail'] = count($this->__Recipients);
         $return['Versandt'] = count($versMails);
         return $return;

       // end function
      }

    // end class
   }
?>