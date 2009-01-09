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
   *  @class abstractRequestFilter
   *  @abstract
   *
   *  Definiert abstrakten Request-Filter.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   *  Version 0.2, 08.06.2007 (Klasse erbt nun von "abstractFilter")<br />
   */
   class abstractRequestFilter extends abstractFilter
   {

      function abstractRequestFilter(){
      }


      /**
      *  @private
      *
      *  Behandlung des Interface-Funktion für konkrete Request-Filter.<br />
      *
      *  @param string $URLString; URL-String
      *  @return array $ReturnArray; Array der URL-Parameter
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function __createRequestArray($URLString){

         // Slashed am Anfang entfernen
         $URLString = $this->__deleteTrailingSlash($URLString);

         // Request-Array erzeugen
         $RequestArray = explode($this->__RewriteURLDelimiter,strip_tags($URLString));

         // Rückgabe-Array initialisieren
         $ReturnArray = array();

         // Offset-Zähler setzen
         $x = 0;

         // RequestArray durchiterieren und auf dem Offset x den Key und auf Offset x+1
         // die Value aus der Request-URI extrahieren
         while($x <= (count($RequestArray) - 1)){

            if(isset($RequestArray[$x + 1])){
               $ReturnArray[$RequestArray[$x]] = $RequestArray[$x + 1];
             // end if
            }

            // Offset-Zähler um 2 erhöhen
            $x = $x + 2;

          // end while
         }

         // Array zurückgeben
         return $ReturnArray;

       // end function
      }


      /**
      *  @private
      *
      *  Eliminiert führende Slashed in URL-Strings.<br />
      *
      *  @param string $URLString; URL-String
      *  @return string $URLString; URL-String ohne führenden Slash
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function __deleteTrailingSlash($URLString){

         // Prüfen, ob "trailing slash" vorhanden
         if(substr($URLString,0,1) == $this->__RewriteURLDelimiter){
            $URLString = substr($URLString,1);
          // end if
         }

         // URL-String zurückgeben
         return $URLString;

       // end function
      }


      /**
      *  @private
      *
      *  Filtert das Request-Array, entfernt Escape-Sequenzen und ersetzt Sonderzeichen mit Ihren<br />
      *  HTML-Entsprechung, damit z.B. Formularfelder nicht falsch angezeigt werden.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.06.2007<br />
      *  Version 0.2, 26.08.2007 (Arrays werden nun sauber behandelt)<br />
      */
      function __filterRequestArray(){

         // Wert für 'magic_quotes_gpc' auslesen
         $MagicQuotesGPC = ini_get('magic_quotes_gpc');

         // Request-Array filtern
         foreach($_REQUEST as $Key => $Value){

            // Zuvor hinzugefügte Slashes removen und decoden
            if(!is_array($Value)){

               if($MagicQuotesGPC == '1'){
                  $_REQUEST[$Key] = htmlspecialchars(stripcslashes($Value));
                // end if
               }
               else{
                  $_REQUEST[$Key] = htmlspecialchars($Value);
                // end
               }

             // end if
            }

          // end foreach
         }

       // end function
      }

    // end class
   }
?>