<?php
   import('tools::variablen','variablenHandler');
   import('modules::footer::biz','oFormData');
   import('modules::footer::biz','recommendManager');


   /**
   *  @ackage modules::footer::pres::documentcontroller
   *  @module weiterempfehlen_v1_controller
   *
   *  Implementiert die Präsentationsschicht der Footer-Funktion 'Weiterempfehlen'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.12.2006<br />
   */
   class recommend_v1_controller extends baseController
   {

      var $_LOCALS;


      function recommend_v1_controller(){

         $this->_LOCALS = variablenHandler::registerLocal(array('AbsenderName',
                                                                'AbsenderEMail',
                                                                'EmpfaengerName',
                                                                'EmpfaengerEMail',
                                                                'Betreff',
                                                                'Text',
                                                                'Weiterempfehlen' => ''
                                                               )
                                                         );

       // end function
      }


      /**
      *  @module transformContent()
      *  @private
      *
      *  Implementiert die abstrakte Methode "transformContent".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.12.2006<br />
      *  Version 0.2, 31.03.2007 (Form wird nun mit der html:form-Taglib generiert)<br />
      */
      function transformContent(){

         // Referenz auf das Formular holen
         $Form = &$this->__getForm('RecommendForm');

         if($Form->get('isValid') && isset($_REQUEST['Senden'])){

            // Was wird gesendet?
            //
            // - AbsenderName
            // - AbsenderEMail
            // - EmpfaengerName
            // - EmpfaengerEMail
            // - Betreff
            // - Text
            // - Seite
            $oFD = new oFormData();
            $oFD->set('SenderName',$this->_LOCALS['AbsenderName']);
            $oFD->set('SenderEMail',$this->_LOCALS['AbsenderEMail']);
            $oFD->set('RecipientName',$this->_LOCALS['EmpfaengerName']);
            $oFD->set('RecipientEMail',$this->_LOCALS['EmpfaengerEMail']);
            $oFD->set('Subject',$this->_LOCALS['Betreff']);
            $oFD->set('Text',$this->_LOCALS['Text']);
            $oFD->set('Page',$this->_LOCALS['Weiterempfehlen']);

            $rM = &$this->__getServiceObject('modules::footer::biz','recommendManager');
            $rM->sendContactForm($oFD);

          // end if
         }
         else{
            $this->setPlaceHolder('Inhalt',$this->__buildForm());
          // end else
         }

       // end function
      }


      /**
      *  @module __buildForm()
      *  @private
      *
      *  Erzeugt das Formular.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.12.2006<br />
      *  Version 0.2, 31.03.2007 (Form wird nun mit der html:form-Taglib generiert)<br />
      */
      function __buildForm(){

         $Form = &$this->__getForm('RecommendForm');
         $Config = &$this->__getConfiguration('tools::form::taglib','formconfig');
         $ValGrp = &$Form->getFormElementByName('MyValGroup');
         $ValGrp->setPlaceHolder('WarnBild',$Config->getValue($this->__Language,'Recomment.WarningImage'));
         return $Form->transformForm();

       // end function
      }

    // end class
   }
?>