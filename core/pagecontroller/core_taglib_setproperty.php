<?php
   /**
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
   */

   /**
   *  @package core::pagecontroller
   *  @class core_taglib_setproperty
   *
   *  Bietet die Möglichkeit eine Eigenschaft eines Documents in der Template-Datei zu setzen.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 04.04.2007<br />
   *  Version 0.2, 22.04.2007 (Um rekursives Setzen erweitert)<br />
   */
   class core_taglib_setproperty extends coreObject
   {

      function core_taglib_setproperty(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend" und setzt eine Eigenschaft der<br />
      *  Eltern-Klasse, sowie deren Kinder rekursiv.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.04.2007<br />
      *  Version 0.2, 22.04.2007 (Kompletter Baum ausgehend vom Parent wird mit dem Wert versorgt)<br />
      */
      function onAfterAppend(){

         // Timer starten
         $T = &Singleton::getInstance('BenchmarkTimer');
         $ID = '('.get_class($this).') '.$this->__ObjectID.'::onAfterAppend() ['.$this->__Attributes['name'].'] => '.$this->__Attributes['value'];
         $T->start($ID);

         // Property des Parent's setzen
         $this->__ParentObject->set($this->__Attributes['name'],$this->__Attributes['value']);

         // Kinder des aktuellen Parent's holen
         $Children = &$this->__ParentObject->getByReference('Children');

         // Kinder iterieren und die Property rekursiv setzen
         foreach($Children as $ObjectID => $Child){
            $this->__setPropertyRecursive($Children[$ObjectID]);
          // end foreach
         }

         // Timer stoppen
         $T->stop($ID);

       // end function
      }


      /**
      *  @private
      *  @since 0.2
      *
      *  Iteriert einen Objektbaum rekursiv und setzt die in den Attributen des Tags angegebenen<br />
      *  Schl�ssel => Wert-Paare in den Objekten per set().<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.04.2007<br />
      */
      private function __setPropertyRecursive(&$Object){

         // Wert des Objekts selbst setzen
         $Object->set($this->__Attributes['name'],$this->__Attributes['value']);

         // Kinder des Objekts holen
         $Children = &$Object->getByReference('Children');

         // Kinder rekursiv mit der Sprache versorgen
         if(count($Children) > 0){

            // Kinder mit dem aktuellen Wert versorgen
            foreach($Children as $ObjectID => $Child){
               $this->__setPropertyRecursive($Children[$ObjectID]);
             // end foreach
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>