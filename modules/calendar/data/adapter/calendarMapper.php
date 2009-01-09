<?php
   import('core::database','MySQLHandler');
   import('modules::calendar::biz','Event');


   /**
   *  @namespace modules::calender::data::adapter
   *  @module calendarMapper
   *
   *  Daten-Adapter für das Termin-Modul des CMS.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 30.04.2007<br />
   *  Version 0.2, 01.05.2007<br />
   */
   class calendarMapper extends coreObject
   {

      function calendarMapper(){
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
      *  Version 0.2, 01.05.2007<br />
      */
      function getEvents($Day = '00',$Month = '00',$Year = '0000'){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('getEvents('.$Day.'-'.$Month.'-'.$Year.')');

         // SQL-Handler holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Event-Daten für Datum holen
         $select = 'SELECT * FROM termine WHERE Datum = \''.$Year.'-'.$Month.'-'.$Day.'\';';
         $result = $SQL->executeTextStatement($select);

         $Events = array();

         while($data = $SQL->fetchData($result)){
            $Events[] = $this->__mapEvent2DomainObject($data);
          // end while
         }

         // Timer stoppen
         $T->stop('getEvents('.$Day.'-'.$Month.'-'.$Year.')');

         return $Events;

       // end function
      }


      /**
      *  @module hasEvents()
      *  @public
      *
      *  Gibt einen Objektbaum für ein übergebenes Monat zurück.<br />
      *
      *  @param string $Day; Tag in der Form 'DD'
      *  @param string $Month; Monat in der Form 'MM'
      *  @param string $Year; Jahr in der Form 'YYYY'
      *  @return bool $hasEvents; true|false;
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      *  Version 0.2, 01.05.2007<br />
      */
      function hasEvents($Day = '00',$Month = '00',$Year = '0000'){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('hasEvents('.$Year.'-'.$Month.'-'.$Day.')');


         // SQL-Handler holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');


         // Event-Daten für Datum holen
         $select = 'SELECT * FROM termine WHERE Datum = \''.$Year.'-'.$Month.'-'.$Day.'\';';
         $result = $SQL->executeTextStatement($select);
         $count = (int) $SQL->getNumRows($result);

         if($count > 0){
            $T->stop('hasEvents('.$Year.'-'.$Month.'-'.$Day.')');
            return true;
          // end if
         }

         $T->stop('hasEvents('.$Year.'-'.$Month.'-'.$Day.')');

         return false;

       // end function
      }


      /**
      *  @module __mapEvent2DomainObject()
      *  @private
      *
      *  Mappt ein Datenbank-Result-Set in ein Event-Objekt.<br />
      *
      *  @param array $ResultSet; Datenbank Result-Set
      *  @return Event $Event; Event-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.04.2007<br />
      */
      function __mapEvent2DomainObject($ResultSet){

         $Event = new Event();

         if(isset($ResultSet['TIndex'])){
            $Event->set('EventID',$ResultSet['TIndex']);
          // end if
         }
         if(isset($ResultSet['Text'])){
            $Event->set('Title',$ResultSet['Text']);
          // end if
         }
         if(isset($ResultSet['DetailText'])){
            $Event->set('Description',$ResultSet['DetailText']);
          // end if
         }

         return $Event;


       // end function
      }

    // end class
   }
?>