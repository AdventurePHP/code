<?php
   /**
   *  Klasse SuchErgebnis
   *  Implementiert ein SuchErgebnis-Objekt
   *
   *  Christian Schäfer
   *  Version 0.1, 29.03.2006
   */
   class SuchErgebnis
   {
      var $__Name;
      var $__Inhalt;
      var $__Relevanz;
      var $__ID;


      function SuchErgebnis(){

         $this->__Name = (string)'';
         $this->__Inhalt = (string)'';
         $this->__ID = (string)'';

       // end function
      }


      function setze($Name,$Wert){

         $Attribute = array_keys(get_class_vars(get_class($this)));

         if(in_array('__'.$Name,$Attribute)){
            $this->{'__'.$Name} = $Wert;
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->setze()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }


      function zeige($Name){

         $Attribute = array_keys(get_class_vars(get_class($this)));

         if(in_array('__'.$Name,$Attribute)){
            return $this->{'__'.$Name};
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->zeige()] Spezifiziertes Attribut ('.$Name.') ist nicht existent!');
          // end else
         }

       // end function
      }

    // end class
   }
?>
