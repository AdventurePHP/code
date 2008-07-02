<?php
   /**
   *  Package modules::teilnehmerliste
   *  Klasse teilnehmerObjekt
   *  Implementiert das Domain-Objekt "teilnehmerObjekt".
   *
   *  Christian Schäfer
   *  Version 0.1, 11.03.2006
   */
   class teilnehmerObjekt
   {

      var $__Betrieb;
      var $__Strasse;
      var $__Ort;
      var $__Telefon;
      var $__Fax;
      var $__Mobil;
      var $__Email;
      var $__Homepage;
      var $__Region;


      function teilnehmerObjekt(){
      }


      function setzeAttribut($Name,$Wert){

         $Attribute = array_keys(get_class_vars(get_class($this)));

         if(in_array('__'.$Name,$Attribute)){
            $this->{'__'.$Name} = $Wert;
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->setzeAttribut()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }


      function zeigeAttribut($Name){

         $Attribute = array_keys(get_class_vars(get_class($this)));

         if(in_array('__'.$Name,$Attribute)){
            return $this->{'__'.$Name};
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->zeigeAttribut()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }

    // end class
   }
?>
