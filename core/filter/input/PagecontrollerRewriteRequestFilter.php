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

   import('core::filter::input','AbstractRequestFilter');


   /**
   *  @namespace core::filter::input
   *  @class PagecontrollerRewriteRequestFilter
   *
   *  Implements the URL filter for the page controller in rewrite url mode.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 02.06.2007<br />
   */
   class PagecontrollerRewriteRequestFilter extends AbstractRequestFilter
   {

      /**
      *  @private
      *  Definiert das URL-Rewriting URL-Trennzeichen.
      */
      var $__RewriteURLDelimiter = '/';


      function PagecontrollerRewriteRequestFilter(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Filter-Funktion aus "abstractRequestFilter".<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (In "filter()" umbenannt)<br />
      *  Version 0.3, 16.06.2007 (URL-Rewriting ge�ndert, so dass ein Mix aus Rewrite-URLs und klassichen URLs m�glich ist)<br />
      *  Version 0.4, 29.09.2007 (Filter springt nur dann an, wenn $_REQUEST['query'] gesetzt ist)<br />
      */
      function filter(){

         // invoke timer
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('PagecontrollerRewriteRequestFilter::filter()');

         // PHPSESSID aus $_REQUEST extrahieren, falls vorhanden
         $PHPSESSID = (string)'';
         $SessionName = ini_get('session.name');

         if(isset($_REQUEST[$SessionName])){
            $PHPSESSID = $_REQUEST[$SessionName];
          // end if
         }

         // Query-String Filtern
         if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){

            // Query-String auslesen
            $Query = $_REQUEST['query'];

            // URL-Rewriting-Kenner l�schen
            unset($_REQUEST['query']);

            // Bisheriges Request.Array sichern
            $RequestBackup = $_REQUEST;

            // Request-Array zur�cksetzen
            $_REQUEST = array();

            // Request-URI in REQUEST-Array extrahieren
            $T->start('filterRewriteParameters()');
            $_REQUEST = $this->__createRequestArray($Query);

            // Request-Array aus Sicherung und neu generiertem Array wieder zusammensetzen
            $_REQUEST = array_merge($_REQUEST,$RequestBackup);
            $T->stop('filterRewriteParameters()');

            // Post-Parameter mit einbeziehen
            $_REQUEST = array_merge($_REQUEST,$_POST);

            // PHPSESSID in Request wieder einsetzen
            if(!empty($PHPSESSID)){
               $_REQUEST[$SessionName] = $PHPSESSID;
             // end if
            }

            // Request-Array filtern
            $this->__filterRequestArray();

          // end if
         }

         // stop timer
         $T->stop('PagecontrollerRewriteRequestFilter::filter()');

       // end function
      }

    // end class
   }
?>