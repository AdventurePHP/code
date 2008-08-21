<?php
   /**
   *  @package core::messaging
   *
   *
   *
   */
   class messagingManager extends coreObject
   {

      /**
      *  @private
      *  Event-Hashtable.
      */
      var $__Events = array();


      function messagingManager(){
      }


      function registerListener($EventIdentifier,&$EventClassReference,$EventMethod){

         // Prüfen, ob EventIdentifier vorhanden ist
         if(!isset($this->__Events[$EventIdentifier])){

            // Listener-Liste initialisieren
            $this->__Events[$EventIdentifier] = array();

          // end if
         }

         // Events zählen
         $EventCount = count($this->__Events);

         // Listener erzeugen
         $this->__Events[$EventIdentifier][$EventCount]['EventClassReference'] = &$EventClassReference;
         $this->__Events[$EventIdentifier][$EventCount]['EventMethod'] = $EventMethod;

       // end function
      }


      function notifyListeners($EventIdentifier){

         // Prüfen, ob EventIdentifier vorhanden ist
         if(isset($this->__Events[$EventIdentifier])){

            // Events holen
            $Events = &$this->__Events[$EventIdentifier];

            // Events zählen
            $EventCount = count($Events);

            // Listeners benachrichtigen
            if($EventCount > 0){

               for($i = 0; $i < $EventCount; $i++){

                  // Methode auf Objekt anwenden
                  //$Object = &$Events[$i]['ObjectReference'];
                  //$Method = $Events[$i]['Method'];
                  //$Object->$Method();
                  $Events[$i]['EventClassReference']->$Events[$i]['EventMethod']();

                // end for
               }

             // end if
            }

          // end if
         }

       // end function
      }

    // end class
   }

   //
   // Beispiel (=wie möchte ich es haben)
   //

   // In der Button-Klasse wird ein onClick-Event registriert
   $this->__registerListener('buttonname_onClick',$this,'triggerFunction');

   // In einer Businesskomponente das Event auslösen -> GUI-Methode wird ausgeführt
   $this->__notifyListeners('MyEvent');


   class xy_controller extends baseController
   {

      function buttonname_onClick(){
      }

   }
?>