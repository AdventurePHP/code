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
    * Provides static methods to calculate with date and time.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 08.09.2004<br />
    * Version 0.2, 21.01.2006<br />
    * Version 0.3, 27.03.2007 (Methode "showMonthLabel" vom "stringAssistant" �bernommen)<br />
    */
   final class dateTimeManager {

      private function dateTimeManager(){
      }

      /**
      *  @public
      *  @static
      *
      *  Generiert ein Datum der Form 00-00-0000.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2004<br />
      */
      static function generateGermanDate(){
         return date('d.m.Y');
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert ein Datum im Format 0000-00-00.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2004<br />
      */
      static function generateDate(){
         return date('Y-m-d');
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert eine Uhrzeit.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2004<br />
      */
      static function generateTime(){
         return date('H:i:s');
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert einen Zeitstempel.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2004<br />
      */
      static function generateTimeStamp(){
         return date('Y-m-d H:i:s');
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Korrigiert bei einem durch $Wert �bergebenem Datum oder Uhrzeit<br />
      *  fehlende f�hrende Nullen.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2004<br />
      *  Version 0.2, 27.03.2007 (Code aufger�umt)<br />
      */
      static function addLeadingZero($Value){

         $Value = strval($Value);

         if(strlen($Value) == 1){
            return '0'.$Value;
          // end if
         }
         else{
            return $Value;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt den zu einer Zahl korrespondierenden Monats-Text (Januar, Februar, ...) zur�ck.<br />
      *  Ist kein passender Monat zu einer Zahl zu finden, wird 'n/a' ausgegeben.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.06.2006<br />
      */
      static function showMonthLabel($Number,$Lang = 'de'){

         switch(intval($Number)){
            case 12:
               $Monat = 'Dezember';
               break;
            case 11:
               $Monat = 'November';
               break;
            case 10:
               $Monat = 'Oktober';
               break;
            case 9:
               $Monat = 'September';
               break;
            case 8:
               $Monat = 'August';
               break;
            case 7:
               $Monat = 'Juli';
               break;
            case 6:
               $Monat = 'Juni';
               break;
            case 5:
               $Monat = 'Mai';
               break;
            case 4:
               $Monat = 'April';
               break;
            case 3:
               $Monat = 'M�rz';
               break;
            case 2:
               $Monat = 'Februar';
               break;
            case 1:
               $Monat = 'Januar';
               break;
            default:
               $Monat = 'n/a';
               break;
          // end switch
         }

         return $Monat;

       // end function
      }

      /**
       * @public
       * @static
       *
       * Funktion convertDate2Normal wandelt ein SQL-Datumsformat in das im Kalender
       * gebräuchliche Format 00.00.0000 um.
       *
       * @param string $sqlDate The date in sql format.
       * @return The date in "normal" notation.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 26.02.2003<br />
       * Version 0.2, 23.03.2004<br />
       * Version 0.3, 09.09.2004<br />
       * Version 0.4, 30.12.2009 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
       */
      static function convertDate2Normal($sqlDate){
         $temp = explode('-',$sqlDate);
         return trim($temp[2]).'.'.trim($temp[1]).'.'.trim($temp[0]);
      }

      /**
       * @public
       * @static
       *
       * Zerlegt ein Datum in seine Bestandteile und gibt ein assoziatives
       * Array mit den Offsets 'Jahr', 'Monat' und 'Tag' zurück.
       *
       * @param string $currentDate The current date.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, ??.??.????<br />
       * Version 0.2, 30.12.2009 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
       */
      static function splitDate($currentDate){

         $return = array();

         // Normales Datum im Format 00.00.0000
         if(preg_match('/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}/i',$currentDate)){

            $temp = explode('.',$currentDate);
            $return['Jahr'] = trim($temp[2]);
            $return['Monat'] = trim($temp[1]);
            $return['Tag'] = trim($temp[0]);

          // end if
         }

         // SQL Datum im Format 0000-00-00
         if(preg_match('/[0-9]{1,4}\-[0-9]{1,2}\-[0-9]{1,2}/i',$currentDate)){

            $temp = explode('-',$currentDate);
            $return['Jahr'] = trim($temp[0]);
            $return['Monat'] = trim($temp[1]);
            $return['Tag'] = trim($temp[2]);

          // end if
         }

         return $return;

       // end function
      }

      /**
       * @public
       * @static
       *
       * Zerlegt eine Uhrzeit in seine Bestandteile und gibt ein assoziatives
       * Array mit den Offsets 'Stunde', 'Minute' und 'Sekunde' zurück.
       *
       * @param $currentTime The current time.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 09.09.2004<br />
       * Version 0.2, 29.11.2004<br />
       * Version 0.3, 30.12.2009 (Replaced split() with explode() because it is marked deprecated in PHP5.3.0)<br />
       */
      static function splitTime($currentTime){

         $return = array();

         $temp = explode(':',$currentTime);
         $return['Stunde'] = trim($temp[0]);
         $return['Minute'] = trim($temp[1]);

         if(isset($temp[2])){
            $return['Sekunde'] = trim($temp[2]);
          // end if
         }

         return $return;

       // end function
      }

      /**
      *  @public
      *  @static
      *
      *  Gibt eine in der Zukunft oder der Vergangenheit liegende Uhrzeit<br />
      *  zur�ck, die durch die aktuelle und die Differenz (Array mit den<br />
      *  Offsets 'Stunden', 'Minuten' und 'Sekunden') errechnet.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2005<br />
      *  Version 0.1, 05.05.2005<br />
      */
      static function calculateTimeDifference($Uhrzeit,$Differenz = array()){

         // Differenz aufschl�sseln
         if(!isset($Differenz['Stunden'])){
            $DifferenzStunden = (int)0;
          // end if
         }
         else{
            $DifferenzStunden = (int)$Differenz['Stunden'];
          // end else
         }
         if(!isset($Differenz['Minuten'])){
            $DifferenzMinuten = (int)0;
          // end if
         }
         else{
            $DifferenzMinuten = (int)$Differenz['Minuten'];
          // end else
         }
         if(!isset($Differenz['Sekunden'])){
            $DifferenzSekunden = (int)0;
          // end if
         }
         else{
            $DifferenzSekunden = (int)$Differenz['Sekunden'];
          // end else
         }


         // Uhrzeit zerlegen
         $ZerlegteUhrzeit = dateTimeManager::splitTime($Uhrzeit);
         $Stunden = (int)$ZerlegteUhrzeit['Stunde'];
         $Minuten = (int)$ZerlegteUhrzeit['Minute'];
         $Sekunden = (int)$ZerlegteUhrzeit['Sekunde'];


         // Handling f�r Sekunden
         $Sekunden = $Sekunden + $DifferenzSekunden;
         while($Sekunden < 0){
            $Minuten = $Minuten - 1;
            $Sekunden = $Sekunden + 60;
          // end while
         }

         while($Sekunden > 59){
            $Minuten = $Minuten + 1;
            $Sekunden = $Sekunden - 60;
          // end if
         }


         // Handling f�r Minuten
         $Minuten = $Minuten + $DifferenzMinuten;
         while($Minuten < 0){
            $Stunden = $Stunden - 1;
            $Minuten = $Minuten + 60;
          // end if
         }
         while($Minuten > 59){
            $Stunden = $Stunden + 1;
            $Minuten = $Minuten - 60;
          // end if
         }


         // Handling f�r Stunden
         $Stunden = $Stunden + $DifferenzStunden;
         while($Stunden > 23){
           $Stunden = $Stunden - 24;
          // end if
         }
         while($Stunden < 0){
           $Stunden = $Stunden + 24;
          // end if
         }


         $Sekunden = (string) dateTimeManager::addLeadingZero($Sekunden);
         $Minuten = (string) dateTimeManager::addLeadingZero($Minuten);
         $Stunden = (string) dateTimeManager::addLeadingZero($Stunden);


         return $Stunden.':'.$Minuten.':'.$Sekunden;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt ein in der Zukunft oder der Vergangenheit liegendes Datum<br />
      *  zur�ck, das durch die aktuelle und die Differenz (Array mit den<br />
      *  Offsets 'Jahr', 'Monat' und 'Tag') errechnet.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 10.03.2006<br />
      *  Version 0.2, 11.03.2006<br />
      */
      static function calculateDate($Datum,$Differenz = array('Jahr' => '0', 'Monat' => '0', 'Tag' => '0')){

          // Anzahl der Tage im Monat
          $TageImMonat[1] = 31;

          // Schaltjahr ber�cksichtigen
          if((date('Y') % 4) == 0){
             $TageImMonat[2] = 29;
           // end if
          }
          else{
             $TageImMonat[2] = 28;
           // end else
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
         $DifferenzJahr = (int) $Differenz['Jahr'];
         $DifferenzMonat = (int) $Differenz['Monat'];
         $DifferenzTag = (int) $Differenz['Tag'];


         // Datum zerlegen
         $ZerlegtesDatum = dateTimeManager::splitDate($Datum);
         $Jahr = (int) $ZerlegtesDatum['Jahr'];
         $Monat = (int) $ZerlegtesDatum['Monat'];
         $Tag = (int) $ZerlegtesDatum['Tag'];


         // Berechnung der Tages-Differenz
         $Tag = $Tag - $DifferenzTag;
         $Monat = $Monat - $DifferenzMonat;
         $Jahr = $Jahr - $DifferenzJahr;


         // Korrektur der Tage
         while($Tag <= 0){

            $Monat = $Monat - 1;

            while($Monat <= 0){

               $Monat = $Monat + 12;
               $Jahr = $Jahr - 1;

             // end if
            }

            $Tag = $Tag + $TageImMonat[$Monat];

          // end if
         }


         // Korrektur der Monate
         while($Monat <= 0){

            $Monat = $Monat + 12;
            $Jahr = $Jahr - 1;

          // end if
         }


         // Korrektur f�r fehlende f�hrende Null
         $Tag = dateTimeManager::addLeadingZero($Tag);
         $Monat = dateTimeManager::addLeadingZero($Monat);


         // Normales Datum im Format 00.00.0000 zur�ckgeben
         if(preg_match('/[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,4}/i',$Datum)){
            return $Tag.'-'.$Monat.'-'.$Jahr;
          // end if
         }

         // SQL Datum im Format 0000-00-00 zur�ckgeben
         if(preg_match('/[0-9]{1,4}\-[0-9]{1,2}\-[0-9]{1,2}/i',$Datum)){
            return $Jahr.'-'.$Monat.'-'.$Tag;
          // end if
         }

       // end function
      }



      /**
      *  @public
      *  @static
      *
      *  Gibt Informationan �ber einen durch $Month und $Year spezifizierten Monat zur�ck.<br />
      *
      *  @param int $Month; Monat (1-12)
      *  @param int $Year; Jahr
      *  @return array $MonthInfo; Informationen �ber ein Monat
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 18.03.2007<br />
      */
      static function getMonthInfo($Month = null,$Year = null){

         // Monat auf aktuellen setzen, falls keiner gegeben
         if($Month == null){
            $Month = date('n');
          // end if
         }


         // Jahr auf aktuelles setzen, falls keines gegeben
         if($Year == null){
            $Year = date('Y');
          // end if
         }


         // Aktuelles Monat und Jahr verifizieren
         if(!checkdate($Month,1,$Year)){
            return null;
          // end if
         }


         // Informationen zusammensetzen
         $First_of_Month = mktime(0,0,0,$Month,1,$Year);
         $Days_in_Month = date('t',$First_of_Month);
         $Last_of_Month = mktime(0,0,0,$Month,$Days_in_Month,$Year);


         // R�ckgabe-Array vorbereiten
         $MonthInfo = array();
         $MonthInfo['First__Monthday_Number'] = 1;
         $MonthInfo['First__Weekday_Number'] = date('w', $First_of_Month);
         $MonthInfo['First__Weekday_Name'] = strftime('%a', $First_of_Month);
         $MonthInfo['First__YearDay_of_Month'] = date('z', $First_of_Month);
         $MonthInfo['First__Week_Number'] = date('W', $First_of_Month);
         $MonthInfo['Last__Monthday_Number'] = $Days_in_Month;
         $MonthInfo['Last__Weekday_Number'] = date('w', $Last_of_Month);
         $MonthInfo['Last__Weekday_Name'] = strftime('%a', $Last_of_Month);
         $MonthInfo['Last__YearDay_of_Month'] = date('z', $Last_of_Month);
         $MonthInfo['Last__WeekNumber'] = date('W', $Last_of_Month);
         $MonthInfo['Month__Number'] = $Month;
         $MonthInfo['Month__Name'] = strftime('%b', $First_of_Month);
         $MonthInfo['Year'] = $Year;


         // Informationen zur�ckgeben
         return $MonthInfo;

       // end function
      }

    // end class
   }
?>