<?php
   import('tools::datetime','dateTimeManager');
   import('modules::calendar::data','calendarDataAdapter');
   import('modules::calendar::biz','Year');
   import('modules::calendar::biz','Month');
   import('modules::calendar::biz','Week');
   import('modules::calendar::biz','Day');
   import('modules::calendar::biz','Event');


   /**
   *  @package modules::calendar::biz
   *  @module calendarManager
   *
   *  Implementiert den Business-Service für den Kalender.<br />
   *
   *  @author Christian Schäfer
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
      *  Implementiert die abstrakte Funktion aus coreObject. Ermöglicht die Initialisierung des Services.<br />
      *
      *  @param string $calendarName; Name des Kalenders bzw. Konfigurations-Offset
      *
      *  @author Christian Schäfer
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
      *  Gibt einen Objektbaum für ein übergebenes Monat zurück.<br />
      *
      *  @param string $Day; Tag in der Form 'DD'
      *  @param string $Month; Monat in der Form 'MM'
      *  @param string $Year; Jahr in der Form 'YYYY'
      *  @return array $EventList; Liste von Events
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      *  Version 0.2, 20.04.2008 (Events werden nur zurückgegeben, wenn readEvents == true)<br />
      */
      function getEvents($Day,$Month,$Year){

         if($this->__readEvents == true){

            // DataAdapter holen
            $calDataAdpt = &$this->__getAndInitServiceObject('modules::calendar::data','calendarDataAdapter',$this->__calendarName);

            // Events zurückgeben
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
      *  Gibt einen Objektbaum für ein übergebenes Monat zurück.<br />
      *
      *  @param string $Month; Monat in der Form 'MM'
      *  @param string $Year; Jahr in der Form 'YYYY'
      *  @return array $EventList; Liste von Events
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.03.2007<br />
      *  Version 0.2, 30.04.2007 (Bugfix für Darstellungsproblem bei 01/2006 und 01/2010)<br />
      *  Version 0.3, 01.05.2007 (Bugfix für Version 0.2. Darstellungsproblem bei 12/2006 und 12/2010)<br />
      *  Version 0.4, 20.04.2008 (Events werden nur ausgegeben, wenn readEvents == true)<br />
      */
      function getMonth($Month,$Year){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('getMonth()');


         // DataAdapter holen, falls benötigt
         if($this->__readEvents == true){
            $calDataAdpt = &$this->__getAndInitServiceObject('modules::calendar::data','calendarDataAdapter',$this->__calendarName);
          // end if
         }


         // Informationen über aktuelles Monat ziehen
         $monthInfo = dateTimeManager::getMonthInfo($Month,$Year);


         // Handling für ersten Tag im Monat
         if((int)$monthInfo['First__Weekday_Number'] == 0){
            $monthDay = -6;
          // end if
         }
         else{
            $monthDay = 1 - (int)$monthInfo['First__Weekday_Number'];
          // end els
         }


         // Handling für KWs
         $First__Week_Number = (int) $monthInfo['First__Week_Number'];
         $Last__WeekNumber = (int) $monthInfo['Last__WeekNumber'];

         // Fix für übergreifende Karlenderwochen über Jahre hinweg
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


            // Fix für übergreifende Karlenderwochen über Jahre hinweg
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

               // Tag im Monat erhöhen
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

               // Tag zur Woche hinzufügen
               $oWeek->add('Days',$oDay);

             // end for
            }


            // Woche zum Monat hinzufügen
            $oMonth->add('Weeks',$oWeek);

          // end for
         }


         // Timer stoppen
         $T->stop('getMonth()');


         // Monat zurückgeben
         return $oMonth;

       // end function
      }

    // end class
   }
?>