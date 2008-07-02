<?php
   class abstractObject
   {
      var $__oID;


      function abstractObject(){
      }


      /**
      *  Funktion set()  [public/nonatstic]<br />
      *  Implementiert die abstrakte set()-Methode aller erbenden Domain-Objekte.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 03.06.2006<br />
      */
      function set($Name,$Value){

         if(in_array('__'.$Name,$this->__getClassAttributes())){
            $this->{'__'.$Name} = $Value;
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->set()] Given attribute ('.$Name.') does not exist!');
          // end else
         }

       // end function
      }


      /**
      *  Funktion get()  [public/nonatstic]<br />
      *  Implementiert die abstrakte get()-Methode aller erbenden Domain-Objekte.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 03.06.2006<br />
      */
      function get($Name){

         if(in_array('__'.$Name,$this->__getClassAttributes())){
            return $this->{'__'.$Name};
          // end if
         }
         else{
            trigger_error('['.get_class($this).'->get()] Given attribute ('.$Name.') does not exist!');
          // end else
         }

       // end function
      }


      function __getClassAttributes(){
         return array_keys(get_class_vars(get_class($this)));
      }

    // end class
   }
?>