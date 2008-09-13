<?php
   import('core::logging','Logger');
   import('tools::validator','myValidator');


   /**
   *  @package core::mail
   *  @class mailSender
   *
   *  Abstrahiertes Mail-Versenden mit PHP.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 09.06.2004<br />
   *  Version 0.2, 04.01.2005<br />
   *  Version 0.3, 27.04.2005<br />
   *  Version 0.4, 14.01.2006 (Redesign / Einbindung neuer Technologien)<br />
   *  Version 0.5, 21.06.2006 (CC-Empf�nger hinzugef�gt)<br />
   *  Version 0.5, 30.03.2007 (Umstellung auf configurationManager)<br />
   *  Version 0.5, 03.09.2007 (Erweiterung um zus�tzliche Header und BCC-Empf�nger)<br />
   */
   class mailSender extends coreObject
   {

      /**
      *  @private
      *  Absender-Kennung.
      *  @example
      *          $this->__Sender['Name']  = '...'<br />
      *          $this->__Sender['EMail'] = '...'<br />
      */
      var $__Sender = array();


      /**
      *  @private
      *  Empf�nger (Array aus Empf�ngern).
      *  @example
      *           $this->__Recipients[0]['Name']  = '...'<br />
      *           $this->__Recipients[0]['EMail'] = '...'<br />
      *           $this->__Recipients[1]['Name']  = '...'<br />
      *           $this->__Recipients[1]['EMail'] = '...'<br />
      */
      var $__Recipients = array();


      /**
      *  @private
      *  CCEmpf�nger (Array aus Empf�ngern).
      *  @example
      *           $this->__CCRecipients[0]['Name']  = '...'<br />
      *           $this->__CCRecipients[0]['EMail'] = '...'<br />
      *           $this->__CCRecipients[1]['Name']  = '...'<br />
      *           $this->__CCRecipients[1]['EMail'] = '...'<br />
      */
      var $__CCRecipients = array();

      /**
      *  @private
      *  BCCEmpf�nger (Array aus Empf�ngern).
      *  @example
      *           $this->__BCCRecipients[0]['Name']  = '...'<br />
      *           $this->__BCCRecipients[0]['EMail'] = '...'<br />
      *           $this->__BCCRecipients[1]['Name']  = '...'<br />
      *           $this->__BCCRecipients[1]['EMail'] = '...'<br />
      */
      var $__BCCRecipients = array();


      /**
      *  @private
      *  Header der Mail.
      */
      var $__MailHeader = null;


      /**
      *  @private
      *  Betreff der Mail.
      */
      var $__Subject;


      /**
      *  @private
      *  Inhalt der Mail.
      */
      var $__Content = '';


      /**
      *  @private
      *  Content-Type der Mail.
      */
      var $__ContentType;


      /**
      *  @private
      *  Return Path der Mail.
      */
      var $__ReturnPath;


      /**
      *  @private
      *  EOL-Zeichen.
      */
      var $__EOL = "\n";


      /**
      *  @private
      *  CRLF-Zeichen.
      */
      var $__CRLF = "\r\n";


      function mailSender(){
      }


      /**
      *  @public
      *
      *  L�d die Konfiguration des mailSenders (Absender-Daten, DebugMod, ..).<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.01.2006<br />
      *  Version 0.2, 15.01.2006<br />
      *  Version 0.3, 30.03.2007 (Umbenannt in init() f�r Kompatibilit�t zu PC V2-Implementierungen)<br />
      *  Version 0.4, 31.03.2007 (Text und Empf�nger werden nun gecleared)<br />
      */
      function init($ConfigSection = 'Standard'){

         // Config laden
         $Config = &$this->__getConfiguration('tools::mail','mailsender');

         // Absender-Daten setzen
         $this->__Sender['Name'] = trim($Config->getValue($ConfigSection,'Mail.SenderName'));
         $this->__Sender['EMail'] = trim($Config->getValue($ConfigSection,'Mail.SenderEMail'));

         // ContentType setzen
         $this->__ContentType = trim($Config->getValue($ConfigSection,'Mail.ContentType'));

         // ReturnPath setzen
         $this->__ReturnPath = trim($Config->getValue($ConfigSection,'Mail.ReturnPath'));

         // Text und Empf�nger resetten, wenn init() aufgerufen werden, damit es nicht zu
         // �berlagerungen und Fehlern kommt.
         $this->clearRecipients();
         $this->clearCCRecipients();
         $this->clearContent();

       // end function
      }


      /**
      *  @private
      *
      *  Konstruiert den Header der E-Mail.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 03.01.2005<br />
      *  Version 0.3, 17.01.2005<br />
      *  Version 0.4, 14.01.2006<br />
      *  Version 0.5, 21.06.2005 (CC-Empf�nger hinzugef�gt)<br />
      *  Version 0.6, 03.09.2007 (Zus�tzliche Header + BCCs eingef�gt)<br />
      */
      function __generateHeader(){

         // Header-Puffer initialisieren
         $MailHeader = (string)'';
         $MailHeader .= 'From: "'.($this->__Sender['Name']).'" <'.($this->__Sender['EMail']).'>'.$this->__EOL;


         // CC-Empfaenger zum Header hinzuf�gen
         if(count($this->__CCRecipients) > 0){

            $CCRecipients = array();

            for($i = 0; $i < count($this->__CCRecipients); $i++){
               $CCRecipients[] = '"'.($this->__CCRecipients[$i]['Name']).'" <'.($this->__CCRecipients[$i]['EMail']).'>';
             // end for
            }

            $MailHeader .= 'CC: '.implode(', ',$CCRecipients).''.$this->__EOL;

          // end if
         }


         // BCC-Empfaenger zum Header hinzuf�gen
         if(count($this->__BCCRecipients) > 0){

            $BCCRecipients = array();

            for($i = 0; $i < count($this->__BCCRecipients); $i++){
               $BCCRecipients[] = '"'.($this->__BCCRecipients[$i]['Name']).'" <'.($this->__BCCRecipients[$i]['EMail']).'>';
             // end for
            }

            $MailHeader .= 'BCC: '.implode(', ',$BCCRecipients).''.$this->__EOL;

          // end if
         }


         // Header vervollst�nigen
         $MailHeader .= 'X-Sender: PHP-E-Mail-Client'.$this->__EOL;
         $MailHeader .= 'X-Mailer: PHP/'.phpversion().''.$this->__EOL;
         $MailHeader .= 'X-Priority: 3'.$this->__EOL; //1 Dringende E-Mail, 3: Priorit�t Normal
         $MailHeader .= 'MIME-Version: 1.0'.$this->__EOL;
         $MailHeader .= 'Return-Path: '.($this->__ReturnPath).''.$this->__EOL;
         $MailHeader .= 'Content-Type: '.($this->__ContentType).''.$this->__EOL;


         // Zus�tzliche Header setzen, falls vorhanden
         if($this->__MailHeader != null){
            $MailHeader .= $this->__MailHeader;
          // end if
         }


         // Fertigen Header zur�ckgeben
         return $MailHeader;

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum Setzen von weiteren Mail-Headern.<br />
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 03.09.2007<br />
      */
      function addHeader($Header = ''){

         if(strpos($Header,':') !== false){
            $this->__MailHeader .= $Header.''.$this->__EOL;
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum setzen von Empf�ngern.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setRecipient($RecipientEMail,$RecipientName){

         if(myValidator::validateEMail($RecipientEMail)){

            $this->__Recipients[count($this->__Recipients)] = array('Name' => $RecipientName,
                                                                    'EMail' => $RecipientEMail
                                                                   );

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum resetten von Empf�ngern.<br />
      *
      *  @author Christian Sch�fer
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
      *  Methode zum Setzen von CC-Empf�ngern.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.06.2006<br />
      */
      function setCCRecipient($RecipientEMail,$RecipientName){

         if(myValidator::validateEMail($RecipientEMail)){

            $this->__CCRecipients[count($this->__CCRecipients)] = array('Name' => $RecipientName,
                                                                        'EMail' => $RecipientEMail
                                                                       );
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum resetten von CCEmpf�ngern.<br />
      *
      *  @author Christian Sch�fer
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
      *  Methode zum Setzen von BCC-Empf�ngern.<br />
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 03.09.2007<br />
      */
      function setBCCRecipient($RecipientEMail,$RecipientName){

         if(myValidator::validateEMail($RecipientEMail)){

            $this->__BCCRecipients[count($this->__BCCRecipients)] = array('Name' => $RecipientName,
                                                                          'EMail' => $RecipientEMail
                                                                         );
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum resetten von BCCEmpf�ngern.<br />
      *
      *  @author Christian Sch�fer
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
      *  Methode zum Manipulieren des Absenders.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.12.2006<br />
      */
      function setSender($SenderEMail,$SenderName){

         if(myValidator::validateEMail($SenderEMail)){

            $this->__Sender['Name'] = $SenderName;
            $this->__Sender['EMail'] = $SenderEMail;

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  F�gt Inhalt zu einer E-Mail hinzu.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setContent($Content){
         $this->__Content .= $Content.''.$this->__EOL;
       // end function
      }


      /**
      *  @public
      *
      *  Methode zum resetten des Contents.<br />
      *
      *  @author Christian Sch�fer
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
      *  Setzt den Betreff der E-Mail.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 09.06.2004<br />
      *  Version 0.2, 14.01.2006<br />
      */
      function setSubject($Subject){
         $this->__Subject = $Subject;
       // end function
      }


      /**
      *  @public
      *
      *  Sendet E-Mails an die eingegebenen Empf�nger.<br />
      *  Gibt ein assoziatives Array zur�ck, das<br />
      *  die Anzahl der zu verschicken E-Mails im Offset "AnzEMail"<br />
      *  und die erfolgreich verschickten im Offset "Versandt" enth�lt.<br />
      *
      *  @author Christian Sch�fer
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


         // R�ckgabe-Werte erzeugen
         $return['AnzEMail'] = count($this->__Recipients);
         $return['Versandt'] = count($versMails);
         return $return;

       // end function
      }

    // end class
   }
?>