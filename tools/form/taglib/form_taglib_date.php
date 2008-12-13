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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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
   *  Represents a APF form date control. Please remember the following things when using the control:
   *  <ul>
   *    <li>The "class" attribute is applied to all child elements.</li>
   *    <li>The "style" attribute is only applied to the first element.</li>
   *    <li>The value of the "name" attribute is suffixed with the day, month and year array offset indicator.</li>
   *    <li>The range of the year can be defined as "1998 - 2009".</li>
   *  </ul>
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 10.01.2007<br />
   *  Version 0.2, 12.01.2007 (Renamed to "form_taglib_date")<br />
   */
   class form_taglib_date extends ui_element
   {

      /**
      *  @private
      *  Start and end of the year range.
      */
      var $__YearRange;


      /**
      *  @private
      *  Names of the offsets for day, month and year.
      */
      var $__OffsetNames;


      /**
      *  @public
      *
      *  Initializes the member variables.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      */
      function form_taglib_date(){

         // initialize the offset names
         $this->__OffsetNames = array('Day' => 'Day', 'Month' => 'Month', 'Year' => 'Year');

         // initialize the year range
         $this->__YearRange['Start'] = (int) date('Y') - 10;
         $this->__YearRange['End'] = (int) date('Y') + 10;

       // end function
      }


      /**
      *  @public
      *
      *  Creates the children select fields for the date control.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      *  Version 0.2, 26.08.2007 (The "class" And "style" attributes are now optional)<br />
      */
      function onParseTime(){

         // read the range for the year select box
         if(isset($this->__Attributes['yearrange'])){

            $YearRange = split('-',$this->__Attributes['yearrange']);

            if(count($YearRange) == 2){
               $this->__YearRange['Start'] = trim($YearRange[0]);
               $this->__YearRange['End'] = trim($YearRange[1]);
             // end if
            }

          // end if
         }

         // set offset values
         if(isset($this->__Attributes['offsetnames'])){

            $OffsetNames = split(';',$this->__Attributes['offsetnames']);

            if(count($OffsetNames) == 3){
               $this->__OffsetNames = array('Day' => $OffsetNames[0], 'Month' => $OffsetNames[1], 'Year' => $OffsetNames[2]);
             // end if
            }

          // end if
         }

         // create select boxes
         $Day = new form_taglib_select();
         $Month = new form_taglib_select();
         $Year = new form_taglib_select();

         // set classes
         if(isset($this->__Attributes['class'])){
            $Day->setAttribute('class',$this->__Attributes['class']);
            $Month->setAttribute('class',$this->__Attributes['class']);
            $Year->setAttribute('class',$this->__Attributes['class']);
          // end if
         }

         // set names
         $Day->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Day'].']');
         $Month->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Month'].']');
         $Year->setAttribute('name',$this->__Attributes['name'].'['.$this->__OffsetNames['Year'].']');

         // set styles
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


         // set the valued for the day select box
         $CheckDayForPreset = false;

         if(isset($_REQUEST[$this->__Attributes['name']][$this->__OffsetNames['Day']])){
            $CheckDayForPreset = true;
          // end if
         }

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


         // set the valued for the month select box
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


         // set the valued for the year select box
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

         // reference the father object
         $Day->setByReference('ParentObject',$this);
         $Month->setByReference('ParentObject',$this);
         $Year->setByReference('ParentObject',$this);

         // add the select boxes to the children list
         $this->__Children['Day'] = $Day;
         $this->__Children['Month'] = $Month;
         $this->__Children['Year'] = $Year;

       // end function
      }


      /**
      *  @public
      *
      *  Returns the HTML code of the date control.
      *
      *  @return string $date the HTML code of the date control
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 10.01.2007<br />
      */
      function transform(){

         $buffer = (string)'';

         foreach($this->__Children as $section => $DUMMY){
            $buffer .= $this->__Children[$section]->transform();
          // end foreach
         }

         return $buffer;

       // end function
      }

    // end class
   }
?>