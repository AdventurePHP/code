<?php
   import('modules::calendar::biz','calendarManager');
   import('modules::calendar::biz','Year');
   import('modules::calendar::biz','Month');
   import('modules::calendar::biz','Week');
   import('modules::calendar::biz','Day');
   import('modules::calendar::biz','Event');
   import('tools::link','linkHandler');
   import('tools::variablen','variablenHandler');


   /**
   *  @package modules::calendar::pres
   *  @module calendar_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'calendar.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 19.03.2007<br />
   *  Version 0.2, 30.04.2007<br />
   *  Version 0.3, 01.05.2007<br />
   */
   class calendar_v1_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokale Variablen.
      */
      var $_LOCALS;


      /**
      *  @module calendar_v1_controller
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.03.2007<br />
      *  Version 0.2, 30.04.2007<br />
      *  Version 0.3, 01.05.2007<br />
      */
      function calendar_v1_controller(){
         //$this->__Attributes['calendarname'] = 'TerminDB';
         $this->_LOCALS = variablenHandler::registerLocal(array('Month' => date('m'),'Year' => date('Y'),'Day'));
       // end function
      }


      /**
      *  @module calendar_v1_controller
      *  @public
      *
      *  Implementiert die abstrakte Methode 'transformContent' aus 'coreObject'.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.03.2007<br />
      *  Version 0.2, 30.04.2007<br />
      *  Version 0.3, 01.05.2007<br />
      */
      function transformContent(){

         // Config-Offset ggf. auf Standardsetzen
         if(!isset($this->__Attributes['calendarname']) || empty($this->__Attributes['calendarname'])){
            $this->__Attributes['calendarname'] = 'Standard';
          // end if
         }

         // Ausgabe für den Kalender erstellen
         $this->setPlaceHolder('Content',$this->__displayMonth());

         // Detail-Anzeige für einen Tag
         $this->setPlaceHolder('Events',$this->__displayEvents($this->_LOCALS['Day'],$this->_LOCALS['Month'],$this->_LOCALS['Year']));

       // end if
      }


      /**
      *  @module __displayEvents()
      *  @private
      *
      *  Erzeugt eine Ausgabe von Events für das angegebene Datum, falls vorhanden.<br />
      *
      *  @param string $Day; Tag
      *  @param string $Month; Monat
      *  @param string $Year; Jahr
      *  @return string $EventList; HTML-Ausgabe der Liste von Events
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      *  Version 0.2, 01.05.2007<br />
      */
      function __displayEvents($Day,$Month,$Year){

         if(!empty($this->_LOCALS['Day'])){

            // Manager holen
            $calManager = &$this->__getAndInitServiceObject('sites::testsite::biz','calendarManager',$this->__Attributes['calendarname']);

            // Events zurückgeben
            $Events = $calManager->getEvents($Day,$Month,$Year);
            if(count($Events) > 0){

               $Buffer = (string)'';

               $Template__Entry = &$this->__getTemplate('Entry');

               for($i = 0; $i < count($Events); $i++){

                  $Template__Entry->setPlaceHolder('Title',$Events[$i]->get('Title'));
                  $Template__Entry->setPlaceHolder('Description',$Events[$i]->get('Description'));
                  $Buffer .= $Template__Entry->transformTemplate();

                // end for
               }

               return $Buffer;

             // end if
            }
            else{
               return (string)'';
             // end else
            }

          // end if
         }
         else{
            return (string)'';
          // end else
         }


       // end function
      }


      /**
      *  @module __displayMonth()
      *  @private
      *
      *  Erzeugt die Ausgabe eines Monats.<br />
      *
      *  @return string $Month; HTML-Ausgabe eines Monats
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      *  Version 0.2, 01.05.2007<br />
      */
      function __displayMonth(){

         // Objektbaum für ein Monat holen
         $calManager = &$this->__getAndInitServiceObject('sites::testsite::biz','calendarManager',$this->__Attributes['calendarname']);
         $Month = $calManager->getMonth($this->_LOCALS['Month'],$this->_LOCALS['Year']);


         // Ausgabepuffer initialisieren
         $Buffer = (string)'';


         // Header anzeigen
         $Template__Header = &$this->__getTemplate('Header');
         $Template__Header->setPlaceHolder('Title',date('M',strtotime($this->_LOCALS['Year'].'-'.($Month->get('MonthNumber')).'-01')).' '.$this->_LOCALS['Year']);


         // Handling für Pager vor
         if(($this->_LOCALS['Month'] + 1) > 12){
            $Next_Month = 1;
            $Next_Year = $this->_LOCALS['Year'] + 1;

          // end if
         }
         elseif(($this->_LOCALS['Month'] - 1) < 0){
            $Next_Month = 12;
            $Next_Year = $this->_LOCALS['Year'] - 1;
          // end if
         }
         else{
            $Next_Month = $this->_LOCALS['Month'] + 1;
            $Next_Year = $this->_LOCALS['Year'];
          // end else
         }

         // Handling für Pager zurück
         if(($this->_LOCALS['Month'] - 1) > 1){
            $Prev_Month = $this->_LOCALS['Month'] - 1;
            $Prev_Year = $this->_LOCALS['Year'];
          // end if
         }
         elseif(($this->_LOCALS['Month'] - 1) < 1){
            $Prev_Month = 12;
            $Prev_Year = $this->_LOCALS['Year'] - 1;
          // end if
         }
         else{
            $Prev_Month = $this->_LOCALS['Month'] - 1;
            $Prev_Year = $this->_LOCALS['Year'];
          // end else
         }

         $Template__Header->setPlaceHolder('PrevMonth',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Day' => '', 'Month' => $Prev_Month, 'Year' => $Prev_Year)));
         $Template__Header->setPlaceHolder('NextMonth',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Day' => '', 'Month' => $Next_Month, 'Year' => $Next_Year)));


         // Heute-Link darstellen
         $Template__TodayLink = &$this->__getTemplate('TodayLink');
         $Template__TodayLink->setPlaceHolder('TodayLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Day' => date('d'), 'Month' => date('m'), 'Year' => date('Y'))));
         $Template__Header->setPlaceHolder('TodayLink',$Template__TodayLink->transformTemplate());


         // Header in Puffer schreiben
         $Buffer .= $Template__Header->transformTemplate();


         // Template für die Darstellung einer Woche holen
         $Template__Week = &$this->__getTemplate('Week');


         // Template für den EventLink holen
         $Template__EventLink = &$this->__getTemplate('EventLink');


         // Ausgabe einer Wochen generieren
         foreach($Month->get('Weeks') as $WeekNumber => $Week){

            // Puffer für die einzelnen Tage anlegen
            $DayBuffer = (string)'';


            // KW einsetzen
            $Template__Week->setPlaceHolder('Week',$Week->get('WeekNumber'));


            // Laufvariable für die Anzahl der Tag
            $DayCount = 0;


            // Tage erzeugen
            foreach($Week->get('Days') as $DayNumber => $Day){

               // Prüfen, ob 7ter Tag angezeigt werden muss (Tebellen-Design!)
               if($DayCount == 6){

                  if(!empty($this->_LOCALS['Day']) && $this->_LOCALS['Day'] == $Day->get('DayNumber')){
                     $Template__Day = &$this->__getTemplate('Day_Left_Active');
                   // end if
                  }
                  else{
                     $Template__Day = &$this->__getTemplate('Day_Left');
                   // end else
                  }

                // end if
               }
               else{

                  if(!empty($this->_LOCALS['Day']) && $this->_LOCALS['Day'] == $Day->get('DayNumber')){
                     $Template__Day = &$this->__getTemplate('Day_Active');
                   // end if
                  }
                  else{
                     $Template__Day = &$this->__getTemplate('Day');
                   // end else
                  }

                // end else
               }


               // Leeren Tag mit Leerzeichen füllen
               if(strlen($Day->get('DayNumber')) < 1){
                  $Template__Day->setPlaceHolder('Day','&nbsp;');
                // end if
               }
               else{

                  // Events ausgeben
                  if($Day->get('hasEvents') == true){

                     // Template für EventLink füllen
                     $Template__EventLink->setPlaceHolder('Text',$Day->get('DayNumber'));
                     $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Year' => $this->_LOCALS['Year'], 'Month' => $this->_LOCALS['Month'], 'Day' => $Day->get('DayNumber')));
                     $Template__EventLink->setPlaceHolder('Link',$Link);

                     // Link in Template einsetzen
                     $Template__Day->setPlaceHolder('Day',$Template__EventLink->transformTemplate());

                   // end if
                  }
                  else{
                     $Template__Day->setPlaceHolder('Day',$Day->get('DayNumber'));
                   // end else
                  }

                // end if
               }


               // Tage in Puffer schreiben
               $DayBuffer .= $Template__Day->transformTemplate();


               // Angezeigte Tageszahl erhöhen
               $DayCount++;

             // end foreach
            }


            // Tage in Wochentemplate einsetzen
            $Template__Week->setPlaceHolder('Days',$DayBuffer);


            // Komplette Woche in Monats-Puffer einsetzen
            $Buffer .= $Template__Week->transformTemplate();

          // end foreach
         }


         // Footer einsetzen
         $Template__Footer = &$this->__getTemplate('Footer');
         $Buffer .= $Template__Footer->transformTemplate();


         // Puffer zurückgeben
         return $Buffer;

       // end function
      }

    // end class
   }
?>