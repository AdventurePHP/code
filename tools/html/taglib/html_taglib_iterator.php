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

   import('tools::html::taglib','iterator_taglib_item');


   /**
   *  @namespace tools::html::taglib
   *  @class html_taglib_iterator
   *
   *  Implementiert ein HTML-Iterator-Container um eine Liste von gleichen<br />
   *  Array-Offsets oder Objekten auszugeben.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 01.06.2008<br />
   *  Version 0.2, 04.06.2008 (Methode __getIteratorItem() durch Verwendung von key() ersetzt)<br />
   */
   class html_taglib_iterator extends Document
   {

      /**
      *  @private
      *  Daten-Container. Array mit numerischen Offsets und assoziativen Unteroffsets oder Liste von Objekten.
      */
      var $__DataContainer = array();


      /**
      *  @private
      *  Indiziert, ob das Iterator-Template an der Definitionsstelle transformiert und ausgegeben werden soll.
      */
      var $__TransformOnPlace = false;


      /**
      *  @public
      *
      *  Definiert die verwendeten TagLibs.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function html_taglib_iterator(){
         $this->__TagLibs[] = new TagLib('tools::html::taglib','iterator','item');
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die Methode onParseTime() für die aktuelle TagLib.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  Mit dieser Methode wird der auszugebende Daten-Container gefüllt.<br />
      *
      *  @param array $Data; Liste von Objekten oder assoziativen Arrays
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function fillDataContainer($Data){
         $this->__DataContainer = $Data;
       // end function
      }


      /**
      *  @public
      *
      *  Definiert, dass das Iterator-Template an der exakten Definitionsstelle transformiert und<br />
      *  ausgegeben werden soll.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function transformOnPlace(){
         $this->__TransformOnPlace = true;
       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt die Ausgabe des Iterator-Templates.<br />
      *
      *  @return string $Buffer; Transformierter Inhalt des Iterators
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      *  Version 0.2, 04.06.2008 (Funktion erweitert)<br />
      *  Version 0.3, 15.06.2008 (Bug behoben, dass in PHP 5 das Item nicht gefunden wurde)<br />
      */
      function transformIterator(){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('(html_taglib_iterator) '.$this->__ObjectID.'::transformIterator()');

         // Ausgabe-Puffer initialisieren
         $Buffer = (string)'';

         // Platzhalter holen
         $ItemObjectID = array_keys($this->__Children);
         $IteratorItem = &$this->__Children[$ItemObjectID[0]];

         // Getter für Objekte definieren
         $Getter = $IteratorItem->getAttribute('getter');
         if($Getter === null){
            $Getter = 'get';
          // end if
         }

         // Platzhalter holen
         $Placeholders = &$IteratorItem->getByReference('Children');

         // Ausgabe erzeugen
         $itemcount = count($this->__DataContainer);
         for($i = 0; $i < $itemcount; $i++){

            if(is_array($this->__DataContainer[$i])){

               foreach($Placeholders as $ObjectID => $DUMMY){
                  $Placeholders[$ObjectID]->set('Content',$this->__DataContainer[$i][$Placeholders[$ObjectID]->getAttribute('name')]);
                // end foreach
               }

               // Item transformieren und in Buffer schreiben
               $Buffer .= $IteratorItem->transform();

             // end if
            }
            elseif(is_object($this->__DataContainer[$i])){

               foreach($Placeholders as $ObjectID => $DUMMY){
                  $Placeholders[$ObjectID]->set('Content',$this->__DataContainer[$i]->{$Getter}($Placeholders[$ObjectID]->getAttribute('name')));
                // end foreach
               }

               // Item transformieren und in Buffer schreiben
               $Buffer .= $IteratorItem->transform();

             // end elseif
            }
            else{
               trigger_error('[html_taglib_iterator::transformIterator()] Given list entry is not an array or object ('.$this->__DataContainer[$i].')! The data container must contain a list of associative arrays or objects!',E_USER_WARNING);
             // end else
            }

          // end for
         }

         // Timer stoppen
         $T->stop('(html_taglib_iterator) '.$this->__ObjectID.'::transformIterator()');

         // Buffer mit der fertigen Ausgabe zurückgeben
         return $Buffer;

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform" des "coreObject"s.<br />
      *
      *  @return string $Content; Leer-String oder Inhalt des Tags
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function transform(){

         // Prüfen, ob Template ausgegeben werden soll
         if($this->__TransformOnPlace === true){
            return $this->transformIterator();
          // end if
         }

         // Leerstring zurückgeben
         return (string)'';

       // end function
      }

    // end class
   }
?>