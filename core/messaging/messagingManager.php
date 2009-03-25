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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */


   /**
   *  @namespace core::messaging
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