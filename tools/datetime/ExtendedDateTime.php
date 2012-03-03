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
 * @package tools::datetime
 * @class ExtendedDateTime
 *
 * Extends PHP's DateTime to support easy arithmetic. Adds some more convenience for date handling.
 * <p/>
 * For details on the PHP default methods, have a look at http://de3.php.net/manual/en/class.datetime.php.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012
 */
class ExtendedDateTime extends DateTime {

   /**
    * @static
    *
    * This method is a synonym for DateTime::createFromFormat(). It has been created due
    * to PHP bug https://bugs.php.net/bug.php?id=55407.
    *
    * @param string $format The date format.
    * @param string $time The time to create a date from.
    * @param DateTimeZone|null $timezone The timezone to apply to the given date.
    * @return ExtendedDateTime An instance of the applied date.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public static function fromFormat($format, $time, DateTimeZone $timezone = null) {
      if ($timezone === null) {
         $timezone = new DateTimeZone(date_default_timezone_get());
      }
      return new ExtendedDateTime(self::createFromFormat($format, $time, $timezone)->getTimestamp(), $timezone);
   }

   /**
    * @static
    *
    * Creates an instance of APF's <em>ExtendedDateTime</em> by a simple PHP DateTime instance.
    *
    * @param DateTime $date A plain PHP DateTime instance.
    * @return ExtendedDateTime An instance of the APF extended date and time represenation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public static function fromDateTime(DateTime $date) {
      return new ExtendedDateTime($date->getTimestamp(), $date->getTimezone());
   }

   /**
    * @public
    *
    * Let's you test, whether this date is located before another date.
    *
    * @param DateTime $date  The reference date.
    * @return bool True in case this date is before the applied date, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function before(DateTime $date) {
      return $this < $date;
   }

   /**
    * @public
    *
    * Let's you test, whether this date is located after another date.
    *
    * @param DateTime $date The reference date.
    * @return bool True in case this date is after the applied date, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function after(DateTime $date) {
      return $this > $date;
   }

   /**
    * @public
    *
    * Returns the date's year.
    *
    * @return string The date's year.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getYear() {
      return $this->format('Y');
   }

   /**
    * @public
    *
    * Returns the date's month.
    *
    * @return string The date's month.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getMonth() {
      return $this->format('m');
   }

   /**
    * @public
    *
    * Returns the date's day.
    *
    * @return string The date's day.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getDay() {
      return $this->format('d');
   }

   /**
    * @public
    *
    * Returns the date's hour.
    *
    * @return string The date's hour.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getHour() {
      return $this->format('H');
   }

   /**
    * @public
    *
    * Returns the date's minute.
    *
    * @return string The date's minute.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getMinute() {
      return $this->format('i');
   }

   /**
    * @public
    *
    * Returns the date's second.
    *
    * @return string The date's second.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012
    */
   public function getSecond() {
      return $this->format('s');
   }

}
