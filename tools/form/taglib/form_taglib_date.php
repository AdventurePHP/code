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
   *  @namespace tools::form::taglib
   *  @class form_taglib_date
   *
   *  Repr�sentiert ein Datums-Feld-Objekt (HTML-Form).<br />
   *
   *  VERHALTEN:<br />
   *   - class gilt f�r alle 3 Selects<br />
   *   - style kann nur f�r das erste Element gelten (wg. Abst�nd zu vorhergehenden Elementen!)<br />
   *   - name wird f�r jedes eingesetzt, mit dem Zusatz "Tag", "Monat", "Jahr", oder die englischen Entsprechungen<br />
   *   - WerteRange kann angegeben werden �ber z.B. yearrange="1998 - 2009"<br />
   *   - muss ein interface haben, mit dem Attribute bzw. Werte (Array) gesetzt werden k�nnen<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 10.01.2007<br />
   *  Version 0.2, 12.01.2007 (Umbenannt in "form_taglib_date")<br />
   */
   class form_taglib_date extends ui_element
   {

      /**
      *  @private
      *  Start und Ende des Jahres-Ranges
      */
      var $__YearRange;

      /**
      *  @private
      *  Namen der Offsets f�r Tag, Monat, Jahr
      */
      var $__OffsetNames;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert die Member-Attribute.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      */
      function form_taglib_date(){

         // Bezeichnungen f�r die Offsets setzen
         $this->__OffsetNames = array('Day' => 'Day', 'Month' => 'Month', 'Year' => 'Year');

         // Range f�r das Jahres-Feld setzen
         $this->__YearRange['Start'] = (int) date('Y') - 10;
         $this->__YearRange['End'] = (int) date('Y') + 10;

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onParseTime". Erzeugt die Kinder des Date-Fields.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      *  Version 0.2, 26.08.2007 (Attribute "class" und "style" werden nun als optional erkannt)<br />
      */
      function onParseTime(){

         // Attribute einlesen und zuordnen
         if(isset($this->__Attributes['class'])){
            $this->__CSSClass = $this->__Attributes['class'];
          // end if
         }


         // Range f�r Jahres-Angabe einlesen
         if(isset($this->__Attributes['yearrange'])){

            $YearRange = split('-',$this->__Attributes['yearrange']);

            if(count($YearRange) == 2){
               $this->__YearRange['Start'] = trim($YearRange[0]);
               $this->__YearRange['End'] = trim($YearRange[1]);
             // end if
            }

          // end if
         }


         // Offset-Werte setzen
         if(isset($this->__Attributes['offsetnames'])){

            $OffsetNames = split(';',$this->__Attributes['offsetnames']);

            if(count($OffsetNames) == 3){
               $this->__OffsetNames = array('Day' => $OffsetNames[0], 'Month' => $OffsetNames[1], 'Year' => $OffsetNames[2]);
             // end if
            }

          // end if
         }


         // 3 neue SelectFelder erzeugen
         $Day = new form_taglib_select();
         $Month = new form_taglib_select();
         $Year = new form_taglib_select();


         // Class Attribut setzen
         if(isset($this->__Attributes['class'])){
            $Day->setAttribute('class',$this->__Attributes['class']);
            $Month->setAttribute('class',$this->__Attributes['class']);
            $Year->setAttribute('class',$this->__Attributes['class']);
          // end if
         }


         // Name setzen
         $Day->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Day'].']');
         $Month->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Month'].']');
         $Year->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Year'].']');


         // Styles setzen
         if(isset($this->__Attributes['style'])){
            $Day->setAttribute('style',$this->__Attributes['style'].' width: 40px;');
          // end if
         }
         else{
            $Day->setAttribute('style','width: 40px;');
          // end else
         }
         $Month->setAttribute('style','width: 40px;');
         $Year->setAttribute('style','width: 55px;');


         // Werte setzen (Day)
         $CheckDayForPreset = false;

         if(isset($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Day']])){
            $CheckDayForPreset = true;
          // end if
         }

         // Werte einsetzen
         for($i = 1; $i <= 31; $i++){

            if(strlen($i) < 2){
               $i = '0'.$i;
             // end if
            }

            if($CheckDayForPreset == true){

               if($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Day']] == $i){
                  $Day->addOption($i,$i,true);
                // end if
               }
               else{
                  $Day->addOption($i,$i);
                // end else
               }

             // end if
            }
            else{
               $Day->addOption($i,$i);
             // end else
            }

          // end for
         }


         // Werte setzen (Month)
         $CheckMonthForPreset = false;

         if(isset($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Month']])){
            $CheckMonthForPreset = true;
          // end if
         }

         for($i = 1; $i <= 12; $i++){

            if(strlen($i) < 2){
               $i = '0'.$i;
             // end if
            }

            if($CheckDayForPreset == true){

               if($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Month']] == $i){
                  $Month->addOption($i,$i,true);
                // end if
               }
               else{
                  $Month->addOption($i,$i);
                // end else
               }

             // end if
            }
            else{
               $Month->addOption($i,$i);
             // end else
            }

          // end for
         }


         // Werte setzen (Year)
         $CheckYearForPreset = false;

         if(isset($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Year']])){
            $CheckYearForPreset = true;
          // end if
         }

         for($i = $this->__YearRange['Start']; $i <= $this->__YearRange['End']; $i++){

            if(strlen($i) < 2){
               $i = '0'.$i;
             // end if
            }

            if($CheckYearForPreset == true){

               if($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Year']] == $i){
                  $Year->addOption($i,$i,true);
                // end if
               }
               else{
                  $Year->addOption($i,$i);
                // end else
               }

             // end if
            }
            else{
               $Year->addOption($i,$i);
             // end else
            }

          // end for
         }


         // Vater bekanntmachen
         $Day->setByReference('ParentObject',$this);
         $Month->setByReference('ParentObject',$this);
         $Year->setByReference('ParentObject',$this);


         // Kinder bekanntmachen
         $this->__Children['Day'] = $Day;
         $this->__Children['Month'] = $Month;
         $this->__Children['Year'] = $Year;

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform". Erzeugt das Date-Field.<br />
      *
      *  @return string $Date; HTML-Code des Datums-Felds
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      */
      function transform(){

         $Buffer = (string)'';

         foreach($this->__Children as $ObjectID => $Child){
            $Buffer .= $Child->transform();
          // end foreach
         }

         return $Buffer;

       // end function
      }

    // end class
   }
?>