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
namespace APF\tools\form\taglib;

use DateTime;

/**
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
class DateSelectorTag extends AbstractFormControl {

   /**
    * Start and end of the year range.
    *
    * @var string[] $yearRange
    */
   protected $yearRange;

   /**
    * Names of the offsets for day, month and year.
    *
    * @var string[] $offsetNames
    */
   protected $offsetNames;

   /**
    * Initializes the member variables.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 10.01.2007<br />
    */
   public function __construct() {

      // initialize the offset names
      $this->offsetNames = ['Day' => 'Day', 'Month' => 'Month', 'Year' => 'Year'];

      // initialize the year range
      $this->yearRange['Start'] = (int) date('Y') - 10;
      $this->yearRange['End'] = (int) date('Y') + 10;
   }

   /**
    * Creates the children select fields for the date control.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 10.01.2007<br />
    * Version 0.2, 26.08.2007 (The "class" And "style" attributes are now optional)<br />
    */
   public function onParseTime() {

      $this->initYearRange();
      $this->initOffsetNames();

      // create select boxes
      $day = new SelectBoxTag();
      $month = new SelectBoxTag();
      $year = new SelectBoxTag();

      // apply context and language
      $day->setLanguage($this->language);
      $month->setLanguage($this->language);
      $year->setLanguage($this->language);
      $day->setContext($this->context);
      $month->setContext($this->context);
      $year->setContext($this->context);

      // apply field names and calculate id to be able access
      // the child elements using JS
      $name = $this->getAttribute('name');

      $dayIdent = $name . '[' . $this->offsetNames['Day'] . ']';
      $monthIdent = $name . '[' . $this->offsetNames['Month'] . ']';
      $yearIdent = $name . '[' . $this->offsetNames['Year'] . ']';

      $day->setAttribute('name', $dayIdent);
      $month->setAttribute('name', $monthIdent);
      $year->setAttribute('name', $yearIdent);

      $day->setAttribute('id', $dayIdent);
      $month->setAttribute('id', $monthIdent);
      $year->setAttribute('id', $yearIdent);

      // apply "tabindex" attribute to enhance usability
      $tabIndices = $this->getTabIndices();
      if ($tabIndices !== null) {
         $day->setAttribute('tabindex', $tabIndices[0]);
         $month->setAttribute('tabindex', $tabIndices[1]);
         $year->setAttribute('tabindex', $tabIndices[2]);
      }

      $prependEmptyOption = $this->getAttribute('prepend-empty-options', 'false') === 'true';

      // set the values for the day select box
      if ($prependEmptyOption === true) {
         $day->addOption('', '');
         $day->setOption2Selected('');
      }

      for ($i = 1; $i <= 31; $i++) {
         $i = $this->appendZero($i);
         $day->addOption($i, $i);
      }

      // set the values for the month select box
      if ($prependEmptyOption === true) {
         $month->addOption('', '');
         $month->setOption2Selected('');
      }

      for ($i = 1; $i <= 12; $i++) {
         $i = $this->appendZero($i);
         $month->addOption($i, $i);
      }

      // set the values for the year select box
      if ($prependEmptyOption === true) {
         $year->addOption('', '');
         $year->setOption2Selected('');
      }

      for ($i = (int) $this->yearRange['Start']; $i <= (int) $this->yearRange['End']; $i++) {
         $yearNumber = sprintf('%04s', $i);
         $year->addOption($yearNumber, $yearNumber);
      }

      // preset today's date on startup if we have no empty options
      $request = $this->getRequest();
      $value = $request->getParameter($name);
      if ($value === null && $prependEmptyOption !== true) {
         $day->setOption2Selected($this->appendZero(date('d')));
         $month->setOption2Selected($this->appendZero(date('m')));
         $year->setOption2Selected(date('Y'));
      }

      // execute the on parse time (important for presetting!)
      $day->onParseTime();
      $month->onParseTime();
      $year->onParseTime();

      // since the onParseTime() methods directly presets the value from the request, we have
      // to correct implausible dates using the PHP DateTime API.
      if ($value !== null && is_array($value)) {
         $date = DateTime::createFromFormat('Y-m-d', $value[$this->offsetNames['Year']]
               . '-' . $value[$this->offsetNames['Month']]
               . '-' . $value[$this->offsetNames['Day']]);
         if ($date !== false) {
            $day->setOption2Selected($date->format('d'));
            $month->setOption2Selected($date->format('m'));
            $year->setOption2Selected($date->format('Y'));
         }
      }

      // reference the father object and add to the children list
      $day->setParentObject($this);
      $month->setParentObject($this);
      $year->setParentObject($this);
      $this->children['d'] = $day;
      $this->children['m'] = $month;
      $this->children['y'] = $year;

      // execute onAfterAppend() to ensure native APF environment
      $this->children['d']->onAfterAppend();
      $this->children['m']->onAfterAppend();
      $this->children['y']->onAfterAppend();
   }

   /**
    * Initializes the year range to display.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    * Version 0.2, 30.12.2009 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
    * Version 0.3, 19.01.2016 (ID#255: added "now" keyword for current year)<br />
    */
   protected function initYearRange() {

      // read the range for the year select box
      if ($this->hasAttribute('yearrange')) {

         $yearRange = explode('-', $this->getAttribute('yearrange'));

         if (count($yearRange) == 2) {
            $this->yearRange['Start'] = trim($yearRange[0]);
            $this->yearRange['End'] = trim($yearRange[1]);

            // re-map special key "now" to current year
            if ($this->yearRange['End'] === 'now') {
               $this->yearRange['End'] = (new DateTime('now'))->format('Y');
            }
         }
      }
   }

   /**
    * Initializes the offset names of the fields.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    * Version 0.2, 09.04.2010 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
    */
   protected function initOffsetNames() {

      if ($this->hasAttribute('offsetnames')) {

         $offsetNames = explode(';', $this->getAttribute('offsetnames'));

         if (count($offsetNames) == 3) {
            $this->offsetNames = [
                  'Day'   => $offsetNames[0],
                  'Month' => $offsetNames[1],
                  'Year'  => $offsetNames[2]
            ];
         }
      }
   }

   /**
    * @return array|null The list of tab indices for the day, month, and year field or <em>null</em>.
    */
   private function getTabIndices() {
      $indices = $this->getAttribute('tab-indexes');

      if ($indices === null) {
         return null;
      }

      $indexList = explode(';', $indices);
      if (count($indexList) == 3) {
         return [
               trim($indexList[0]),
               trim($indexList[1]),
               trim($indexList[2])
         ];
      }

      return null;
   }

   /**
    * Appends a zero for month or day numbers without leading zeros.
    *
    * @param int $input The month or day number.
    *
    * @return string Month or day number with leading zero.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   protected function appendZero($input) {
      return sprintf('%02s', $input);
   }

   /**
    * Generated the HTML code of the date control.
    *
    * @return string The HTML code of the date control.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 10.01.2007<br />
    * Version 0.2, 18.03.2010 (Introduced surrounding span to support client validation)<br />
    * Version 0.3, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {

      if ($this->isVisible) {

         // as of 1.12, the date control should be rendered using a
         // surrounding span do enable the client validator extension
         // to address the control more easily.
         $buffer = '<span id="' . $this->getId() . '"';

         $style = $this->getAttribute('style');
         if ($style != null) {
            $buffer .= ' style="' . $style . '"';
         }

         $class = $this->getAttribute('class');
         if ($class != null) {
            $buffer .= ' class="' . $class . '"';
         }
         $buffer .= '>';
         foreach ($this->children as $section => $DUMMY) {
            $buffer .= $this->children[$section]->transform();
         }

         return $buffer . '</span>';
      }

      return '';
   }

   /**
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
   private function getId() {
      $id = $this->getAttribute('id');
      if ($id === null) {
         return $this->getAttribute('name');
      }

      return $id;
   }

   /**
    * Re-implements the retrieving of values for date controls.
    *
    * @return DateTime The current value or content of the control.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function getValue() {
      return $this->getDate();
   }

   /**
    * Returns the date as DateTime instance with the pattern YYYY-MM-DD.
    *
    * @return DateTime Instance of DateTime with pattern YYYY-MM-DD.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    * Version 0.2, 30.04.2012 (Introduced a more fail-safe way of reading the date from the control)<br />
    * Version 0.3, 27.07.2014 (ID#208: added DateTime support)<br />
    */
   public function getDate() {

      $day = $this->getDayControl()->getSelectedOption();
      $month = $this->getMonthControl()->getSelectedOption();
      $year = $this->getYearControl()->getSelectedOption();

      // in case any of the select boxes are missing a none-empty selection, the date is null
      if ($day === null || $month === null || $year === null) {
         return null;
      }

      // use date time API to ensure calender conforming dates (e.g. don't create implausible
      // dates such as 1937-04-31).
      $date = DateTime::createFromFormat('Y-m-d', $year->getValue() . '-' . $month->getValue() . '-' . $day->getValue());

      // In case an empty date has been submitted (e.g. because the "prepend-empty-options" attribute
      // is set) return null.
      if ($date === false) {
         return null;
      }

      return $date;
   }

   /**
    * Returns a reference on the day control of the date control.
    *
    * @return SelectBoxTag The day control.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function &getDayControl() {
      return $this->children['d'];
   }

   /**
    * Returns a reference on the month control of the date control.
    *
    * @return SelectBoxTag The month control.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function &getMonthControl() {
      return $this->children['m'];
   }

   /**
    * Returns a reference on the year control of the date control.
    *
    * @return SelectBoxTag The year control.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function &getYearControl() {
      return $this->children['y'];
   }

   /**
    * Re-implements the setting of values for date controls.
    *
    * @param DateTime|string $value The date to set (e.g. DateTime or "2012-04-30").
    *
    * @return $this This control for further usage.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function &setValue($value) {
      $this->setDate($value);

      return $this;
   }

   /**
    * Allows you to initialize the date control with a given date (e.g. "2010-06-16" or the respective instance or DateTime).
    *
    * @param DateTime|string $date The date to initialize the control with (either DateTime instance or string).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.06.2010<br />
    * Version 0.2, 30.04.2012 (Introduced a more fail-safe way of initializing the date)<br />
    * Version 0.3, 27.07.2014 (ID#208: added DateTime support)<br />
    */
   public function setDate($date) {

      if (!($date instanceof DateTime)) {
         $date = DateTime::createFromFormat('Y-m-d', $date);
      }

      $this->getDayControl()->setOption2Selected($this->appendZero($date->format('d')));
      $this->getMonthControl()->setOption2Selected($this->appendZero($date->format('m')));
      $this->getYearControl()->setOption2Selected($date->format('Y'));
   }

   public function reset() {
      // rely on SelectBoxTag::reset()...
      $this->getYearControl()->reset();
      $this->getMonthControl()->reset();
      $this->getDayControl()->reset();
   }

}
