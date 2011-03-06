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
 * @class form_taglib_time
 *
 * Represents a APF form time control.
 *
 * @author Werner Liemberger
 * @version
 * Version 0.1, 21.2.2011<br />
 */
class form_taglib_time extends form_control {

   protected $hoursRange;
   protected $offsetNames;
   protected $minutesInterval;
   protected $showSeconds = true;

   /**
    * @public
    *
    * Initializes the member variables.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function __construct() {

      // initialize the offset names
      $this->offsetNames = array('Hours' => 'Hours', 'Minutes' => 'Minutes', 'Seconds' => 'Seconds');

      // initialize the year range
      $this->hoursRange['Start'] = 00;
      $this->hoursRange['End'] = 23;
      $this->minutesInterval = 1;
      $this->showSeconds = true;
   }

   /**
    * @public
    *
    * Creates the children select fields for the time control.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function onParseTime() {

      $this->__initHoursRange();
      $this->__initOffsetNames();

      if (!empty($this->__Attributes['minutesinterval'])) {
         $this->minutesInterval = (int) $this->__Attributes['minutesinterval'];
      }
      if (!empty($this->__Attributes['showseconds']) && $this->__Attributes['showseconds'] == "false") {
         $this->showSeconds = false;
      }

      // create select boxes then apply context and language
      $hours = new form_taglib_select();
      $hours->setLanguage($this->__Language);
      $hours->setContext($this->__Context);

      $minutes = new form_taglib_select();
      $minutes->setLanguage($this->__Language);
      $minutes->setContext($this->__Context);

      if ($this->showSeconds != false) {
         $seconds = new form_taglib_select();
         $seconds->setLanguage($this->__Language);
         $seconds->setContext($this->__Context);
      }

      $name = $this->getAttribute('name');

      // apply field names and calculate id to be able access
      // the child elements using JS
      $hoursIdent = $name . '[' . $this->offsetNames['Hours'] . ']';
      $hours->setAttribute('name', $hoursIdent);
      $hours->setAttribute('id', $hoursIdent);

      $minutesIdent = $name . '[' . $this->offsetNames['Minutes'] . ']';
      $minutes->setAttribute('name', $minutesIdent);
      $minutes->setAttribute('id', $minutesIdent);

      if ($this->showSeconds != false) {
         $secondsIdent = $name . '[' . $this->offsetNames['Seconds'] . ']';
         $seconds->setAttribute('name', $secondsIdent);
         $seconds->setAttribute('id', $secondsIdent);
      }


      // set the values for the hours select box
      for ($i = (int) $this->hoursRange['Start']; $i <= (int) $this->hoursRange['End']; $i++) {
         $i = $this->appendZero($i);
         $hours->addOption($i, $i);
      }

      // set the values for the minutes select box
      $i = 0;
      while ($i < 60) {
         $i = $this->appendZero($i);
         $minutes->addOption($i, $i);
         $i = $i + $this->minutesInterval;
      }


      // set the values for the seconds select box
      if ($this->showSeconds != false) {
         for ($i = 0; $i < 60; $i++) {
            $i = $this->appendZero($i);
            $seconds->addOption($i, $i);
         }
      }

      // preset today's time on startup
      if (!isset($_REQUEST[$name])) {
         $hours->setOption2Selected($this->appendZero(date('G')));
         $minutes->setOption2Selected($this->appendZero(date('i')));
         if ($this->showSeconds != false) {
            $seconds->setOption2Selected(date('s'));
         }
      }

      // execute the on parse time (important for presetting!)
      $hours->onParseTime();
      $minutes->onParseTime();
      if ($this->showSeconds != false) {
         $seconds->onParseTime();
      }

      // reference the father object and add to the children list
      $hours->setParentObject($this);
      $minutes->setParentObject($this);
      if ($this->showSeconds != false) {
         $seconds->setParentObject($this);
      }
      $this->__Children['h'] = $hours;
      $this->__Children['m'] = $minutes;
      if ($this->showSeconds != false) {
         $this->__Children['s'] = $seconds;
      }

      // execute onAfterAppend() to ensure native APF environment
      $this->__Children['h']->onAfterAppend();
      $this->__Children['m']->onAfterAppend();
      if ($this->showSeconds != false) {
         $this->__Children['s']->onAfterAppend();
      }
   }

   /**
    * @private
    *
    * Returns the id of the time element. In case the developer does not
    * provide the <em>id</em> attribute within the tag definition, the
    * <em>name</em> attribute is used instead.
    *
    * @return string The id to display within the HTML tag.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   private function getId() {
      $id = $this->getAttribute('id');
      if ($id === null) {
         return $this->getAttribute('name');
      }
      return $id;
   }

   /**
    * @public
    *
    * Generated the HTML code of the time control.
    *
    * @return string The HTML code of the time control.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function transform() {

      // as of 1.12, the time control should be rendered using a
      // surrounding span do enable the client validator extension
      // to address the control more easily.
      $buffer = (string) '<span id="' . $this->getId() . '"';

      $style = $this->getAttribute('style');
      if ($style != null) {
         $buffer .= ' style="' . $style . '"';
      }

      $class = $this->getAttribute('class');
      if ($class != null) {
         $buffer .= ' class="' . $class . '"';
      }
      $buffer .= '>';
      foreach ($this->__Children as $section => $DUMMY) {
         $buffer .= $this->__Children[$section]->transform();
      }

      return $buffer . '</span>';
   }

   /**
    * @public
    *
    * Re-implements the addValidator() method for the form control due
    * to special behavior.
    *
    * @param AbstractFormValidator $validator The validator to add.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function addValidator(AbstractFormValidator &$validator) {
      if ($validator->isActive()) {
         if (!$validator->validate($this->getTime())) {
            $validator->notify();
         }
      }
   }

   /**
    * @public
    *
    * Returns the time with the pattern HH:MM:SS or if ShowSeconds is false with HH:MM
    *
    * @return string Time with pattern HH:MM:SS or if ShowSeconds is false with HH:MM.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function getTime() {
      $hours = $this->getHoursControl()->getSelectedOption()->getAttribute('value');
      $minutes = $this->getMinutesControl()->getSelectedOption()->getAttribute('value');
      if ($this->showSeconds != false) {
         $seconds = $this->getSecondsControl()->getSelectedOption()->getAttribute('value');
         return $hours . ':' . $minutes . ':' . $seconds;
      }
      return $hours . ':' . $minutes;
   }

   /**
    * @public
    *
    * Allows you to initialize the time control with a given time (e.g. "08:31" or "08:31:20" or "2011-02-21 08:31:00").
    *
    * @param string $time The time to initialize the control with.
    * @throws FormException In case of date parsing errors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.06.2010<br />
    */
   public function setTime($time) {

      $time = date_parse($time);
      if (count($time['errors']) == 0 && count($time['warnings']) == 0) {
         $this->getHoursControl()->setOption2Selected($this->appendZero($time['hour']));
         $this->getMinutesControl()->setOption2Selected($this->appendZero($time['minute']));
         if ($this->showSeconds != false) {
            $this->getSecondsControl()->setOption2Selected($time['second']);
         }
      } else {
         throw new FormException('[form_taglib_time::setTime()] Given time "' . $time
                 . '" cannot be parsed (Errors: ' . implode(', ', $time['errors']) . ', warnings: '
                 . implode(', ', $time['warnings']) . ')');
      }
   }

   /**
    * @public
    *
    * Returns a reference on the Dours control of the time control.
    *
    * @return form_taglib_select The hours control.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function &getHoursControl() {
      return $this->__Children['h'];
   }

   /**
    * @public
    *
    * Returns a reference on the minutes control of the time control.
    *
    * @return form_taglib_select The minutes control.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function &getMinutesControl() {
      return $this->__Children['m'];
   }

   /**
    * @public
    *
    * Returns a reference on the seconds control of the date control.
    *
    * @return form_taglib_select The seconds control.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   public function &getSecondsControl() {
      return $this->__Children['s'];
   }

   /**
    * @protected
    *
    * Initializes the hours range to display.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   protected function __initHoursRange() {

      // read the range for the hours select box
      if (isset($this->__Attributes['hoursrange'])) {

         $hoursrange = explode('-', $this->__Attributes['hoursrange']);

         if (count($hoursrange) == 2) {
            $this->hoursRange['Start'] = trim($this->appendZero($hoursrange[0]));
            $this->hoursRange['End'] = trim($this->appendZero($hoursrange[1]));
         }
      }
   }

   /**
    * @protected
    *
    * Initializes the offset names of the fields.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   protected function __initOffsetNames() {

      if (isset($this->__Attributes['offsetnames'])) {

         $offsetNames = explode(';', $this->__Attributes['offsetnames']);

         if (count($offsetNames) == 3) {
            $this->offsetNames = array(
                'Hours' => $offsetNames[0],
                'Minutes' => $offsetNames[1],
                'Seconds' => $offsetNames[2]
            );
         }
         if (count($offsetNames) == 2) {
            $this->offsetNames = array(
                'Hours' => $offsetNames[0],
                'Minutes' => $offsetNames[1]
            );
         }
      }
   }

   /**
    * @protected
    *
    * Appends a zero for hours, miuntes or seconds numbers without leading zeros.
    *
    * @param int $input The hour, minute or second number.
    * @return string Hour, minute or second number with leading zero.
    *
    * @author Werner Liemberger
    * @version
    * Version 0.1, 21.2.2011<br />
    */
   protected function appendZero($input) {
      return sprintf('%02s', $input);
   }

}
?>