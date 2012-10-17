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
 * @class dateTimeManager
 * @static
 *
 * @deprecated Use PHP's DateTime instead.
 *
 * Provides static methods to calculate with date and time.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 08.09.2004<br />
 * Version 0.2, 21.01.2006<br />
 * Version 0.3, 27.03.2007 (Moved "showMonthLabel" from "StringAssistant" to this class)<br />
 */
final class dateTimeManager {

   private function __construct() {
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function generateGermanDate() {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      return date('d.m.Y');
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function generateDate() {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      return date('Y-m-d');
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function generateTime() {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      return date('H:i:s');
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function generateTimeStamp() {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      return date('Y-m-d H:i:s');
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function addLeadingZero($value) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      $value = strval($value);
      return strlen($value) == 1 ? '0' . $value : $value;
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function showMonthLabel($number, $lang = 'de') {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      switch (intval($number)) {
         case 12:
            $month = 'Dezember';
            break;
         case 11:
            $month = 'November';
            break;
         case 10:
            $month = 'Oktober';
            break;
         case 9:
            $month = 'September';
            break;
         case 8:
            $month = 'August';
            break;
         case 7:
            $month = 'Juli';
            break;
         case 6:
            $month = 'Juni';
            break;
         case 5:
            $month = 'Mai';
            break;
         case 4:
            $month = 'April';
            break;
         case 3:
            $month = 'März';
            break;
         case 2:
            $month = 'Februar';
            break;
         case 1:
            $month = 'Januar';
            break;
         default:
            $month = 'n/a';
            break;
      }

      return $month;

   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function convertDate2Normal($sqlDate) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);
      $temp = explode('-', $sqlDate);
      return count($temp) == 3
            ? trim($temp[2]) . '.' . trim($temp[1]) . '.' . trim($temp[0])
            : null;
   }

   /**
    * @deprecated Use PHP's DateTime instead.>
    */
   public static function splitDate($currentDate) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      $return = array();

      // Normales Datum im Format 00.00.0000
      if (preg_match('/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}/i', $currentDate)) {

         $temp = explode('.', $currentDate);
         $return['Jahr'] = trim($temp[2]);
         $return['Monat'] = trim($temp[1]);
         $return['Tag'] = trim($temp[0]);
      }

      // SQL Datum im Format 0000-00-00
      if (preg_match('/[0-9]{1,4}\-[0-9]{1,2}\-[0-9]{1,2}/i', $currentDate)) {

         $temp = explode('-', $currentDate);
         $return['Jahr'] = trim($temp[0]);
         $return['Monat'] = trim($temp[1]);
         $return['Tag'] = trim($temp[2]);
      }

      return $return;

   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function splitTime($currentTime) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      $return = array();

      $temp = explode(':', $currentTime);
      $return['Stunde'] = trim($temp[0]);
      $return['Minute'] = trim($temp[1]);

      if (isset($temp[2])) {
         $return['Sekunde'] = trim($temp[2]);
      }

      return $return;

   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function calculateTimeDifference($Uhrzeit, $Differenz = array()) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      if (!isset($Differenz['Stunden'])) {
         $DifferenzStunden = (int)0;
      } else {
         $DifferenzStunden = (int)$Differenz['Stunden'];
      }
      if (!isset($Differenz['Minuten'])) {
         $DifferenzMinuten = (int)0;
      } else {
         $DifferenzMinuten = (int)$Differenz['Minuten'];
      }
      if (!isset($Differenz['Sekunden'])) {
         $DifferenzSekunden = (int)0;
      } else {
         $DifferenzSekunden = (int)$Differenz['Sekunden'];
      }

      // Uhrzeit zerlegen
      $ZerlegteUhrzeit = dateTimeManager::splitTime($Uhrzeit);
      $Stunden = (int)$ZerlegteUhrzeit['Stunde'];
      $Minuten = (int)$ZerlegteUhrzeit['Minute'];
      $Sekunden = (int)$ZerlegteUhrzeit['Sekunde'];

      // Handling f�r Sekunden
      $Sekunden = $Sekunden + $DifferenzSekunden;
      while ($Sekunden < 0) {
         $Minuten = $Minuten - 1;
         $Sekunden = $Sekunden + 60;
      }

      while ($Sekunden > 59) {
         $Minuten = $Minuten + 1;
         $Sekunden = $Sekunden - 60;
      }


      // Handling f�r Minuten
      $Minuten = $Minuten + $DifferenzMinuten;
      while ($Minuten < 0) {
         $Stunden = $Stunden - 1;
         $Minuten = $Minuten + 60;
      }
      while ($Minuten > 59) {
         $Stunden = $Stunden + 1;
         $Minuten = $Minuten - 60;
      }

      // Handling f�r Stunden
      $Stunden = $Stunden + $DifferenzStunden;
      while ($Stunden > 23) {
         $Stunden = $Stunden - 24;
      }
      while ($Stunden < 0) {
         $Stunden = $Stunden + 24;
      }

      $Sekunden = (string)dateTimeManager::addLeadingZero($Sekunden);
      $Minuten = (string)dateTimeManager::addLeadingZero($Minuten);
      $Stunden = (string)dateTimeManager::addLeadingZero($Stunden);

      return $Stunden . ':' . $Minuten . ':' . $Sekunden;

   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function calculateDate($Datum, $Differenz = array('Jahr' => '0', 'Monat' => '0', 'Tag' => '0')) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      // Anzahl der Tage im Monat
      $TageImMonat[1] = 31;

      // Schaltjahr ber�cksichtigen
      if ((date('Y') % 4) == 0) {
         $TageImMonat[2] = 29;
      } else {
         $TageImMonat[2] = 28;
      }

      $TageImMonat[3] = 31;
      $TageImMonat[4] = 30;
      $TageImMonat[5] = 31;
      $TageImMonat[6] = 30;
      $TageImMonat[7] = 31;
      $TageImMonat[8] = 31;
      $TageImMonat[9] = 30;
      $TageImMonat[10] = 31;
      $TageImMonat[11] = 30;
      $TageImMonat[12] = 31;

      // Differenz aufschl�sseln
      $DifferenzJahr = (int)$Differenz['Jahr'];
      $DifferenzMonat = (int)$Differenz['Monat'];
      $DifferenzTag = (int)$Differenz['Tag'];

      // Datum zerlegen
      $ZerlegtesDatum = dateTimeManager::splitDate($Datum);
      $Jahr = (int)$ZerlegtesDatum['Jahr'];
      $Monat = (int)$ZerlegtesDatum['Monat'];
      $Tag = (int)$ZerlegtesDatum['Tag'];

      // Berechnung der Tages-Differenz
      $Tag = $Tag - $DifferenzTag;
      $Monat = $Monat - $DifferenzMonat;
      $Jahr = $Jahr - $DifferenzJahr;

      // Korrektur der Tage
      while ($Tag <= 0) {

         $Monat = $Monat - 1;

         while ($Monat <= 0) {
            $Monat = $Monat + 12;
            $Jahr = $Jahr - 1;
         }

         $Tag = $Tag + $TageImMonat[$Monat];

      }

      // Korrektur der Monate
      while ($Monat <= 0) {
         $Monat = $Monat + 12;
         $Jahr = $Jahr - 1;
      }

      // Korrektur f�r fehlende f�hrende Null
      $Tag = dateTimeManager::addLeadingZero($Tag);
      $Monat = dateTimeManager::addLeadingZero($Monat);

      // Normales Datum im Format 00.00.0000 zur�ckgeben
      if (preg_match('/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}/i', $Datum)) {
         return $Tag . '-' . $Monat . '-' . $Jahr;
      }

      // SQL Datum im Format 0000-00-00 zur�ckgeben
      if (preg_match('/[0-9]{1,4}\-[0-9]{1,2}\-[0-9]{1,2}/i', $Datum)) {
         return $Jahr . '-' . $Monat . '-' . $Tag;
      }

      // in case no date mapping can be done, return null to indicate failure.
      return null;
   }

   /**
    * @deprecated Use PHP's DateTime instead.
    */
   public static function getMonthInfo($Month = null, $Year = null) {
      trigger_error('dateTimeManager is deprecated as of release 1.15. Please use PHP\'s DateTime instead!', E_USER_DEPRECATED);

      // Monat auf aktuellen setzen, falls keiner gegeben
      if ($Month == null) {
         $Month = date('n');
      }

      // Jahr auf aktuelles setzen, falls keines gegeben
      if ($Year == null) {
         $Year = date('Y');
      }

      // Aktuelles Monat und Jahr verifizieren
      if (!checkdate($Month, 1, $Year)) {
         return null;
      }

      $First_of_Month = mktime(0, 0, 0, $Month, 1, $Year);
      $Days_in_Month = date('t', $First_of_Month);
      $Last_of_Month = mktime(0, 0, 0, $Month, $Days_in_Month, $Year);

      $monthInfo = array();
      $monthInfo['First__Monthday_Number'] = 1;
      $monthInfo['First__Weekday_Number'] = date('w', $First_of_Month);
      $monthInfo['First__Weekday_Name'] = strftime('%a', $First_of_Month);
      $monthInfo['First__YearDay_of_Month'] = date('z', $First_of_Month);
      $monthInfo['First__Week_Number'] = date('W', $First_of_Month);
      $monthInfo['Last__Monthday_Number'] = $Days_in_Month;
      $monthInfo['Last__Weekday_Number'] = date('w', $Last_of_Month);
      $monthInfo['Last__Weekday_Name'] = strftime('%a', $Last_of_Month);
      $monthInfo['Last__YearDay_of_Month'] = date('z', $Last_of_Month);
      $monthInfo['Last__WeekNumber'] = date('W', $Last_of_Month);
      $monthInfo['Month__Number'] = $Month;
      $monthInfo['Month__Name'] = strftime('%b', $First_of_Month);
      $monthInfo['Year'] = $Year;

      return $monthInfo;
   }

}
