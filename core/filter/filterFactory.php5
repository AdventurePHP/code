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

   /**
   *  @namespace core::filter
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
   *  @namespace core::filter
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
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 13.08.2008 (Removed unused code)<br />
      */
      function getFilter($Namespace,$FilterName){

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

       // end function
      }

    // end class
   }
?>