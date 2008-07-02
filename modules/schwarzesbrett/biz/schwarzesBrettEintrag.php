<?php
   /**
   *  Package modules::schwarzesbrett::biz<br />
   *  Klasse schwarzesBrettEintrag<br />
   *  Implementiert das Domain-Objekt des schwarzen Bretts.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 24.12.2005<br />
   */
   class schwarzesBrettEintrag
   {
      var $__Text;
      var $__Datum;
      var $__Uhrzeit;
      var $__Vorname;
      var $__Nachname;
      var $__Strasse;
      var $__PLZ;
      var $__Ort;
      var $__Tel;
      var $__Fax;
      var $__EMail;
      var $__Anhang;


      function schwarzesBrettEintrag(){

         $this->__Text = (string)'';
         $this->__Datum = (string)'';
         $this->__Uhrzeit = (string)'';
         $this->__Vorname = (string)'';
         $this->__Nachname = (string)'';
         $this->__Strasse = (string)'';
         $this->__PLZ = (string)'';
         $this->__Ort = (string)'';
         $this->__Tel = (string)'';
         $this->__Fax = (string)'';
         $this->__EMail = (string)'';
         $this->__Anhang = (string)'';

       // end function
      }


      function setzeAttribut($Name,$Wert){

         $Attribute = array_keys(get_class_vars('schwarzesBrettEintrag'));

         if(in_array('__'.$Name,$Attribute)){
            $this->{'__'.$Name} = $Wert;
          // end if
         }
         else{
            trigger_error('[schwarzesBrettEintrag->setzeAttribut()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }


      function zeigeAttribut($Name){

         $Attribute = array_keys(get_class_vars('schwarzesBrettEintrag'));

         if(in_array('__'.$Name,$Attribute)){
            return $this->{'__'.$Name};
          // end if
         }
         else{
            trigger_error('[schwarzesBrettEintrag->zeigeAttribut()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }

    // end class
   }
?>