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

   import('tools::request','RequestHandler');
   import('modules::kontakt4::biz','contactManager');


   /**
   *  @namespace modules::kontakt4::pres::documentcontroller
   *  @class kontakt_v4_controller
   *
   *  Implementiert die Präsentationsschicht des Kontaktformulars.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2006<br />
   *  Version 0.2, 04.06.2006<br />
   *  Version 0.3, 23.02.2007 (Implementierung nach PageController v2)<br />
   */
   class kontakt_v4_controller extends baseController
   {

      /**
      *  @private
      *  Array von lokalen Variablen.
      */
      protected $_LOCALS;


      function kontakt_v4_controller(){

         $this->_LOCALS = RequestHandler::getValues(array('Empfaenger',
                                                                'AbsenderName',
                                                                'AbsenderAdresse',
                                                                'Betreff',
                                                                'Text'
                                                               )
                                                         );

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode transformContent() des coreObjects.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 29.03.2007 (Form->isValid wird nun abgefragt, statt nochmal zu validieren!)<br />
      *  Version 0.4, 27.05.2007 (Status "abgesendet" wird nun per Form->isSent abgefragt!)<br />
      */
      function transformContent(){

         // Referenz auf die Form holen
         $Form = &$this->__getForm('Kontakt');

         if($Form->get('isValid') && $Form->get('isSent')){

            // Was wird gesendet?
            //
            // - Kontakt-Person-ID (Empfänger-Person der Mail)
            // - Name
            // - E-Mail
            // - Betreff
            // - Text
            $oFD = new oFormData();
            $oFD->set('RecipientID',$this->_LOCALS['Empfaenger']);
            $oFD->set('SenderName',$this->_LOCALS['AbsenderName']);
            $oFD->set('SenderEMail',$this->_LOCALS['AbsenderAdresse']);
            $oFD->set('Subject',$this->_LOCALS['Betreff']);
            $oFD->set('Text',$this->_LOCALS['Text']);

            // Formular absenden
            $cM = &$this->__getServiceObject('modules::kontakt4::biz','contactManager');
            $cM->sendContactForm($oFD);

          // end if
         }
         else{
            $this->setPlaceHolder('Inhalt',$this->__buildForm());
          // end else
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Erzeugt das Kontakt-Formular.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 23.02.2007 (Implementierung nach PageController v2)<br />
      *  Version 0.4, 28.03.2007 (Generisches Bild in der Validatorgruppe hinzugefügt)<br />
      *  Version 0.5, 13.12.2007 (Auf Mehrsprachigkeit erweitert)<br />
      */
      protected function __buildForm(){

         // Referenz auf die Form holen
         $Form__Kontakt = &$this->__getForm('Kontakt');

         // Action setzen
         $Form__Kontakt->setAttribute('action',$_SERVER['REQUEST_URI']);

         // Button beschriften und formatieren
         $Config = &$this->__getConfiguration('modules::kontakt4','language');
         $Button = &$Form__Kontakt->getFormElementByName('KontaktSenden');
         $Button->setAttribute('value',$Config->getValue($this->__Language,'form.button'));
         $Button->setAttribute('style',$Config->getValue($this->__Language,'form.button.style'));

         // Bild in der ValidatorGroup setzen (Auslesen der formconfig)
         $Config = &$this->__getConfiguration('tools::form::taglib','formconfig');
         $ValGroup = &$Form__Kontakt->getFormElementByName('FormValGroup');
         $ValGroup->setPlaceHolder('WarnImage',$Config->getValue($this->__Language,'Contact.Warning.Image'));
         $ValGroup->setPlaceHolder('WarnText',$Config->getValue($this->__Language,'Contact.Warning.Text'));

         // Auswahlfeld Person
         $Recipients = & $Form__Kontakt->getFormElementByName('Empfaenger');

         // RecipientList laden
         $cM = &$this->__getServiceObject('modules::kontakt4::biz','contactManager');
         $RecipientList = $cM->loadRecipients();

         for($i = 0; $i < count($RecipientList); $i++){
            $Recipients->addOption($RecipientList[$i]->get('Name'),$RecipientList[$i]->get('oID'));
          // end if
         }

         return $Form__Kontakt->transformForm();

       // end function
      }

    // end class
   }
?>