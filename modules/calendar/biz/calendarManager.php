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

   import('tools::datetime','dateTimeManager');
   import('modules::calendar::data','calendarDataAdapter');
   import('modules::calendar::biz','Year');
   import('modules::calendar::biz','Month');
   import('modules::calendar::biz','Week');
   import('modules::calendar::biz','Day');
   import('modules::calendar::biz','Event');


   /**
   *  @namespace modules::calendar::biz
   *  @module calendarManager
   *
   *  Implementiert den Business-Service f�r den Kalender.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 19.03.2007<br />
   *  Version 0.2, 30.04.2007<br />
   *  Version 0.3, 01.05.2007<br />
   */
   class calendarManager extends coreObject
   {

      /**
      *  @private
      *  Speichert, ob Events eingelesen werden oder nicht. Standard: true.
      */
      var $__readEvents = false;


      /**
      *  @private
      *  Name des Kalenders bzw. Konfigurations-Offset.
      */
      var $__calendarName = false;


      function calendarManager(){
      }


      /**
      *  @module init()
      *  @public
      *
      *  Implementiert die abstrakte Funktion aus coreObject. Erm�glicht die Initialisierung des Services.<br />
      *
      *  @param string $calendarName; Name des Kalenders bzw. Konfigurations-Offset
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.05.2007<br />
      */
      function init($calendarName = false){
         $this->__calendarName = $calendarName;
       // end function
      }


      /**
      *  @module getEvents()
      *  @public
      *
      *  Gibt einen Objektbaum f�r ein �bergebenes Monat zur�ck.<br />
      *
      *  @param string $Day; Tag in der Form 'DD'
      *  @param string $Month; Monat in der Form 'MM'
      *  @param string $Year; Jahr in der Form 'YYYY'
      *  @return array $EventList; Liste von Events
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      *  Version 0.2, 20.04.2008 (Events werden nur zur�ckgegeben, wenn readEvents == true)<br />
      */
      function getEvents($Day,$Month,$Year){

         if($this->__readEvents == true){

            // DataAdapter holen
            $calDataAdpt = &$this->__getAndInitServiceObject('modules::calendar::data','calendarDataAdapter',$this->__calendarName);

            // Events zur�ckgeben
            return $calDataAdpt->getEvents($Day,$Month,$Year);

          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @module getMonth()
      *  @public
      *
      *  Gibt einen Objektbaum f�r ein �bergebenes Monat zur�ck.<br />
      *
      *  @param string $Month; Monat in der Form 'MM'
      *  @param string $Year; Jahr in der Form 'YYYY'
      *  @return array $EventList; Liste von Events
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.03.2007<br />
      *  Version 0.2, 30.04.2007 (Bugfix f�r Darstellungsproblem bei 01/2006 und 01/2010)<br />
      *  Version 0.3, 01.05.2007 (Bugfix f�r Version 0.2. Darstellungsproblem bei 12/2006 und 12/2010)<br />
      *  Version 0.4, 20.04.2008 (Events werden nur ausgegeben, wenn readEvents == true)<br />
      */
      function getMonth($Month,$Year){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('getMonth()');


         // DataAdapter holen, falls ben�tigt
         if($this->__readEvents == true){
            $calDataAdpt = &$this->__getAndInitServiceObject('modules::calendar::data','calendarDataAdapter',$this->__calendarName);
          // end if
         }


         // Informationen �ber aktuelles Monat ziehen
         $monthInfo = dateTimeManager::getMonthInfo($Month,$Year);


         // Handling f�r ersten Tag im Monat
         if((int)$monthInfo['First__Weekday_Number'] == 0){
            $monthDay = -6;
          // end if
         }
         else{
            $monthDay = 1 - (int)$monthInfo['First__Weekday_Number'];
          // end els
         }


         // Handling f�r KWs
         $First__Week_Number = (int) $monthInfo['First__Week_Number'];
         $Last__WeekNumber = (int) $monthInfo['Last__WeekNumber'];

         // Fix f�r �bergreifende Karlenderwochen �ber Jahre hinweg
         if($Month == 12){

            if($Last__WeekNumber < $First__Week_Number){
               $Last__WeekNumber = 53;
             // end if
            }

          // end if
         }
         else{

            if($Last__WeekNumber < $First__Week_Number){
               $Last__WeekNumber = $First__Week_Number + $Last__WeekNumber;
             // end if
            }

          // end else
         }


         // Monat erzeugen
         $oMonth = new Month();
         $oMonth->set('MonthNumber',$Month);


         // Wochen zeichnen
         for($i = $First__Week_Number; $i <= $Last__WeekNumber; $i++){

            // Woche initialisieren
            $oWeek = new Week();


            // Fix f�r �bergreifende Karlenderwochen �ber Jahre hinweg
            if($i > 52){
               $oWeek->set('WeekNumber',$i - 52);
             // end if
            }
            else{
               $oWeek->set('WeekNumber',$i);
             // end else
            }


            // Tage erzeugen
            for($j = 0; $j < 7; $j++){

               // Tag im Monat erh�hen
               $monthDay++;
               $oDay = new Day();

               if($monthDay > 0 && $monthDay <= (int)$monthInfo['Last__Monthday_Number']){

                  // Tages-Zahl setzen
                  $oDay->set('DayNumber',$monthDay);

                  // Event vormerken
                  if($this->__readEvents == true){
                     $oDay->set('hasEvents',$calDataAdpt->hasEvents(dateTimeManager::addLeadingZero($monthDay),dateTimeManager::addLeadingZero($monthInfo['Month__Number']),$monthInfo['Year']));
                   // end if
                  }

                // end if
               }
               else{
                  $oDay->set('DayNumber','');
                // end else
               }

               // Tag zur Woche hinzuf�gen
               $oWeek->add('Days',$oDay);

             // end for
            }


            // Woche zum Monat hinzuf�gen
            $oMonth->add('Weeks',$oWeek);

          // end for
         }


         // Timer stoppen
         $T->stop('getMonth()');


         // Monat zur�ckgeben
         return $oMonth;

       // end function
      }

    // end class
   }
?>