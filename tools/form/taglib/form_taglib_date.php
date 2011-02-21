<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package tools::form::taglib
    * @class form_taglib_date
    *
    * Represents a APF form date control. Please remember the following things when using the control:
    * <ul>
    *   <li>The "class" attribute is applied to all child elements.</li>
    *   <li>The "style" attribute is only applied to the first element.</li>
    *   <li>The value of the "name" attribute is suffixed with the day, month and year array offset indicator.</li>
    *   <li>The range of the year can be defined as "1998 - 2009".</li>
    * </ul>
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 10.01.2007<br />
    * Version 0.2, 12.01.2007 (Renamed to "form_taglib_date")<br />
    */
   class form_taglib_date extends form_control {

      /**
       * @protected
       * @var string[] Start and end of the year range.
       */
      protected $__YearRange;

      /**
       * @protected
       * @var string[] Names of the offsets for day, month and year.
       */
      protected $__OffsetNames;

      /**
       * @public
       *
       * Initializes the member variables.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 10.01.2007<br />
       */
      public function form_taglib_date(){

         // initialize the offset names
         $this->__OffsetNames = array('Day' => 'Day', 'Month' => 'Month', 'Year' => 'Year');

         // initialize the year range
         $this->__YearRange['Start'] = (int) date('Y') - 10;
         $this->__YearRange['End'] = (int) date('Y') + 10;

       // end function
      }

      /**
       * @public
       *
       * Creates the children select fields for the date control.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 10.01.2007<br />
       * Version 0.2, 26.08.2007 (The "class" And "style" attributes are now optional)<br />
       */
      public function onParseTime(){

         $this->__initYearRange();
         $this->__initOffsetNames();

         // create select boxes
         $day = new form_taglib_select();
         $month = new form_taglib_select();
         $year = new form_taglib_select();

         // apply context and language
         $day->setLanguage($this->__Language);
         $month->setLanguage($this->__Language);
         $year->setLanguage($this->__Language);
         $day->setContext($this->__Context);
         $month->setContext($this->__Context);
         $year->setContext($this->__Context);

         // apply field names and calculate id to be able access
         // the child elements using JS
         $name = $this->getAttribute('name');

         $dayIdent = $name.'['.$this->__OffsetNames['Day'].']';
         $monthIdent = $name.'['.$this->__OffsetNames['Month'].']';
         $yearIdent = $name.'['.$this->__OffsetNames['Year'].']';

         $day->setAttribute('name',$dayIdent);
         $month->setAttribute('name',$monthIdent);
         $year->setAttribute('name',$yearIdent);

         $day->setAttribute('id',$dayIdent);
         $month->setAttribute('id',$monthIdent);
         $year->setAttribute('id',$yearIdent);

         // set the values for the day select box
         for($i = 1; $i <= 31; $i++){
            $i = $this->__appendZero($i);
            $day->addOption($i,$i);
          // end for
         }

         // set the values for the month select box
         for($i = 1; $i <= 12; $i++){
            $i = $this->__appendZero($i);
            $month->addOption($i,$i);
          // end for
         }

         // set the values for the year select box
         for($i = (int)$this->__YearRange['Start']; $i <= (int)$this->__YearRange['End']; $i++){
            if(strlen($i) < 2){
               $i = '0'.$i;
             // end if
            }
            $year->addOption($i,$i);
          // end for
         }

         // preset today's date on startup
         if(!isset($_REQUEST[$name])){
            $day->setOption2Selected($this->__appendZero(date('d')));
            $month->setOption2Selected($this->__appendZero(date('m')));
            $year->setOption2Selected(date('Y'));
          // end if
         }

         // execute the on parse time (important for presetting!)
         $day->onParseTime();
         $month->onParseTime();
         $year->onParseTime();

         // reference the father object and add to the children list
         $day->setParentObject($this);
         $month->setParentObject($this);
         $year->setParentObject($this);
         $this->__Children['d'] = $day;
         $this->__Children['m'] = $month;
         $this->__Children['y'] = $year;
         
         // execute onAfterAppend() to ensure native APF environment
         $this->__Children['d']->onAfterAppend();
         $this->__Children['m']->onAfterAppend();
         $this->__Children['y']->onAfterAppend();

       // end function
      }

      /**
       * @private
       *
       * Returns the id of the date element. In case the developer does not
       * provide the <em>id</em> attribute within the tag definition, the
       * <em>name</em> attribute is used instead.
       *
       * @return string The id to display within the HTML tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 18.03.2010<br />
       */
      private function getId(){
         $id = $this->getAttribute('id');
         if($id === null){
            return $this->getAttribute('name');
         }
         return $id;
      }

      /**
       * @public
       *
       * Generated the HTML code of the date control.
       *
       * @return string The HTML code of the date control.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 10.01.2007<br />
       * Version 0.2, 18.03.2010 (Introduced surrounding span to support client validation)<br />
       */
      public function transform(){

         // as of 1.12, the date control should be rendered using a
         // surrounding span do enable the client validator extension
         // to address the control more easily.
         $buffer = (string)'<span id="'.$this->getId().'"';

         $style = $this->getAttribute('style');
         if($style != null){
            $buffer .= ' style="'.$style.'"';
         }

         $class = $this->getAttribute('class');
         if($class != null){
            $buffer .= ' class="'.$class.'"';
         }
         $buffer .= '>';
         foreach($this->__Children as $section => $DUMMY){
            $buffer .= $this->__Children[$section]->transform();
          // end foreach
         }

         return $buffer.'</span>';

       // end function
      }

      /**
       * @public
       *
       * Re-implements the addValidator() method for the form control due
       * to special behavior.
       *
       * @param AbstractFormValidator $validator The validator to add.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function addValidator(AbstractFormValidator &$validator){
         if($validator->isActive()){
            if(!$validator->validate($this->getDate())){
               $validator->notify();
            }
         }
       // end function
      }

      /**
       * @public
       * 
       * Returns the date with the pattern YYYY-MM-DD.
       *
       * @return string Date with pattern YYYY-MM-DD.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function getDate(){
         $day = $this->getDayControl()->getSelectedOption()->getAttribute('value');
         $month = $this->getMonthControl()->getSelectedOption()->getAttribute('value');
         $year = $this->getYearControl()->getSelectedOption()->getAttribute('value');
         return $year.'-'.$month.'-'.$day;
       // end function
      }

      /**
       * @public
       *
       * Allows you to initialize the date control with a given date (e.g. "2010-06-16").
       *
       * @param string $date The date to initialize the control with.
       * @throws FormException In case of date parsing errors.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.06.2010<br />
       */
      public function setDate($date){
         
         $time = date_parse($date);
         if(count($time['errors']) == 0 && count($time['warnings']) == 0){
            $this->getDayControl()->setOption2Selected($this->__appendZero($time['day']));
            $this->getMonthControl()->setOption2Selected($this->__appendZero($time['month']));
            $this->getYearControl()->setOption2Selected($time['year']);
         }
         else{
            throw new FormException('[form_taglib_date::setDate()] Given date "'.$date
                    .'" cannot be parsed (Errors: '.implode(', ', $time['errors']).', warnings: '
                    .implode(', ',$time['warnings']).')');
         }
         
      }

      /**
       * @public
       *
       * Returns a reference on the day control of the date control.
       *
       * @return form_taglib_select The day control.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function &getDayControl(){
         return $this->__Children['d'];
       // end function
      }

      /**
       * @public
       *
       * Returns a reference on the month control of the date control.
       *
       * @return form_taglib_select The month control.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function &getMonthControl(){
         return $this->__Children['m'];
       // end function
      }

      /**
       * @public
       *
       * Returns a reference on the year control of the date control.
       *
       * @return form_taglib_select The year control.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function &getYearControl(){
         return $this->__Children['y'];
       // end function
      }

      /**
       * @protected
       *
       * Initializes the year range to display.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       * Version 0.2, 30.12.2009 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
       */
      protected function __initYearRange(){

         // read the range for the year select box
         if(isset($this->__Attributes['yearrange'])){

            $yearRange = explode('-',$this->__Attributes['yearrange']);

            if(count($yearRange) == 2){
               $this->__YearRange['Start'] = trim($yearRange[0]);
               $this->__YearRange['End'] = trim($yearRange[1]);
             // end if
            }

          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Initializes the offset names of the fields.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       * Version 0.2, 09.04.2010 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
       */
      protected function __initOffsetNames(){

         if(isset($this->__Attributes['offsetnames'])){

            $offsetNames = explode(';',$this->__Attributes['offsetnames']);

            if(count($offsetNames) == 3){
               $this->__OffsetNames = array(
                  'Day' => $offsetNames[0],
                  'Month' => $offsetNames[1],
                  'Year' => $offsetNames[2]
               );
             // end if
            }

          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Appends a zero for month or day numbers without leading zeros.
       *
       * @param int $input The month or day number.
       * @return string Month or day number with leading zero.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      protected function __appendZero($input){
         if(strlen($input) < 2){
            $input = '0'.(string)$input;
          // end if
         }
         return $input;
       // end function
      }

    // end class
   }
?>