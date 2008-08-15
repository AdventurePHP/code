<?php
   /**
   *  @package core::filter
   *  @class abstractFilter
   *  @abstract
   *
   *  Abstrakte Filter-Klasse.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 08.06.2007<br />
   */
   class abstractFilter extends coreObject
   {

      function abstractFilter(){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Abstrakte Filter-Methode, die in konkreten Filtern implementiert werden muss.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.06.2007<br />
      */
      function filter(){
      }

    // end class
   }


   /**
   *  @package core::filter
   *  @class filterFactory
   *
   *  Implementiert die Factory f�r die URI- und HTML-Filter-Klassen. Factory muss als<br />
   *  Service-Objekt initialisiert werden.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 08.06.2007<br />
   */
   class filterFactory
   {

      function filterFactory(){
      }


      /**
      *  @public
      *  @static
      *
      *  Liefert eine Instanz des gew�nschten Filters.<br />
      *
      *  @param string $FilterName; Name des Filters
      *  @return object $Filter; Instanz des Filters oder NULL
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 13.08.2008 (Removed unused code)<br />
      */
      static function getFilter($Namespace,$FilterName){

         // Pr�fen, ob Filter vorhanden
         if(file_exists(APPS__PATH.'/'.str_replace('::','/',$Namespace).'/'.$FilterName.'.php')){
            import('core::filter',$FilterName);
            return new $FilterName;
          // end if
         }
         else{
            trigger_error('[filterFactory::getFilter()] Requested filter "'.$FilterName.'" cannot be loaded from namespace "'.$Namespace.'"!',E_USER_ERROR);
            return null;
          // end else
         }

       // end function
      }

    // end class
   }
?>