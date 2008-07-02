<?php
   /**
   *  @package core::filter
   *  @class abstractFilter
   *  @abstract
   *
   *  Abstrakte Filter-Klasse.<br />
   *
   *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
   *  Implementiert die Factory für die URI- und HTML-Filter-Klassen. Factory muss als<br />
   *  Service-Objekt initialisiert werden.<br />
   *
   *  @author Christian Schäfer
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
      *  Liefert eine Instanz des gewünschten Filters.<br />
      *
      *  @param string $FilterName; Name des Filters
      *  @return object $Filter; Instanz des Filters oder NULL
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.06.2007<br />
      */
      static function getFilter($Namespace,$FilterName){

         // Prüfen, ob Filter vorhanden
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

         // Filternamen erzeugen
         $FilterName = $Type.'RequestFilter';

         // Instanz des Filters zurückgeben
         return new $FilterName;

       // end function
      }

    // end class
   }
?>