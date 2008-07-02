<?php
   /**
   *  @package modules::calendar::data
   *  @module calendarDataAdapter
   *
   *  Implementiert den Data-Service für den Kalender.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 01.05.2007<br />
   */
   class calendarDataAdapter extends coreObject
   {

      /**
      *  @private
      *  Name des Kalenders bzw. Konfigurations-Offset.
      */
      var $__calendarName = false;


      /**
      *  @private
      *  Speichert, ob Klasse schon initialisiert ist.
      */
      var $__isInitialized = false;


      /**
      *  @private
      *  Namespaces des Adapters.
      */
      var $__Adapter_Namespace;


      /**
      *  @private
      *  Dateiname des Adapters.
      */
      var $__Adapter_FileName;


      /**
      *  @private
      *  Name der Klasse des Adapters.
      */
      var $__Adapter_ClassName;


      /**
      *  @private
      *  Name der Methode für das Laden der Events.
      */
      var $__Adapter_Methode_GetEvents;


      /**
      *  @private
      *  Name der Methode für das Prüfen, ob Events vorliegen.
      */
      var $__Adapter_Methode_HasEvents;


      function calendarDataAdapter(){
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
      *  Version 0.2, 04.10.2007 (Konfiguration wird nun in der init()-Funktion eingelesen)<br />
      *  Version 0.3, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function init($calendarName = false){

         // Prüfen, ob das Objekt bereits initialisiert ist
         if($this->__isInitialized != $calendarName){

            // Calender-Name speichern
            $this->__calendarName = $calendarName;

            // Konfiguration holen
            $Config = &$this->__getConfiguration('modules::calendar','dataadapter');

            if($Config->getSection($this->__calendarName) == null){
               $Reg = &Singleton::getInstance('Registry');
               $Environment = $Reg->retrieve('apf::core','Environment');
               trigger_error('[calendarDataAdapter->getEvents()] Adapter for section name "'.$this->__calendarName.'" cannot be loaded! Please check configuration in namespace "modules::calendar::'.$this->__Context.'" with name "'.$Environment.'_dataadapter.ini"!',E_USER_ERROR);
               exit();
             // end if
            }


            // Konfiguration speichern
            $this->__Adapter_Namespace = $Config->getValue($this->__calendarName,'Adapter.Namespace').'::adapter';
            $this->__Adapter_FileName = $Config->getValue($this->__calendarName,'Adapter.FileName');
            $this->__Adapter_ClassName = $Config->getValue($this->__calendarName,'Adapter.ClassName');
            $this->__Adapter_Methode_GetEvents = $Config->getValue($this->__calendarName,'Adapter.Methode.GetEvents');
            $this->__Adapter_Methode_HasEvents = $Config->getValue($this->__calendarName,'Adapter.Methode.HasEvents');


            // Adapter einbinden
            import($this->__Adapter_Namespace,$this->__Adapter_FileName);


            // Als initialisiert kennzeichnen
            $this->__isInitialized = $calendarName;

          // end if
         }

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
      *  Version 0.2, 01.05.2007<br />
      */
      function getEvents($Day = '00',$Month = '00',$Year = '0000'){

         // Adapter holen
         $calAdpt = &$this->__getServiceObject($this->__Adapter_Namespace,$this->__Adapter_ClassName);

         // Events auslesen
         return $calAdpt->{$this->__Adapter_Methode_GetEvents}($Day,$Month,$Year);

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

         // Adapter holen
         $calAdpt = &$this->__getServiceObject($this->__Adapter_Namespace,$this->__Adapter_ClassName);

         // Events auslesen
         return $calAdpt->{$this->__Adapter_Methode_HasEvents}($Day,$Month,$Year);

       // end function
      }

    // end class
   }
?>