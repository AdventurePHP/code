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
   *  @class htmlLinkRewriteFilter
   *
   *  Implementiert den URL-Rewriting-Filter für HTML-Quelltext.<br />
   *  Bei Links mit dem Attribut <code>linkrewrite="false"</code> wird der Link nicht rewritet.<br />
   *  Ist das Attribut auf "true" oder einen anderen Wert gesetzt oder fehlt dieses, wird der Link<br />
   *  umgeschrieben.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.05.2007 (Erste Version des generischen Link-Rewritings)<br />
   *  Version 0.2, 08.05.2007 (Kapselung als Filter)<br />
   *  Version 0.3, 17.06.2007 (Um Action-Rewriting ergänzt)<br />
   */
   class htmlLinkRewriteFilter extends AbstractFilter
   {

      function htmlLinkRewriteFilter(){
      }


      /**
      *  @public
      *
      *  Implementiert die Filer-Funktion für das Rewriten von HTML-Links und Actions.<br />
      *
      *  @param string $HTMLContent; HTML-Quelltext
      *  @return string $HTMLContent; Rewriteter HTML-Quelltext
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007 (Erste Version des generischen Link-Rewritings)<br />
      *  Version 0.2, 08.05.2007 (Kapselung als Filter)<br />
      */
      function filter($HTMLContent){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('htmlLinkRewriteFilter::filter()');

         // Links filtern
         $HTMLContent = $this->__filter($HTMLContent,'<a','>','href');

         // Actions filtern
         $HTMLContent = $this->__filter($HTMLContent,'<form','>','action');

         // Timer stoppen
         $T->stop('htmlLinkRewriteFilter::filter()');

         // Rewriteten HTMLContent zurückgeben
         return $HTMLContent;

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert eine generische Filter-Methode.<br />
      *
      *  @param string $HTMLContent; HTML-Quelltext
      *  @param string $StartToken; Start-Token für die Suche im Quelltext
      *  @param string $EndToken; End-Token für die Suche im Quelltext
      *  @param string $AttributeToken; Name des zu parsenden Attributs
      *  @return string $HTMLContent; Rewriteter HTML-Quelltext
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.07.2007 (Auslagerung der Filter-Methode, damit sowohl Links als auch Actions gefiltert werden können)<br />
      */
      function __filter($HTMLContent,$StartToken = '<a',$EndToken = '>',$AttributeToken = 'href'){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('htmlLinkRewriteFilter::__filter()');

         // Offset deklarieren
         $SearchOffset = 0;

         // Token gefunden?
         $TokenFound = true;

         // Text durchsuchen
         while($TokenFound == true){

            // Token-Start-Position finden
            $CurrentLinkStartPos = strpos($HTMLContent,$StartToken,$SearchOffset);

            if($CurrentLinkStartPos !== false){

               // Token-End-Position finden
               $CurrentLinkEndPos = strpos($HTMLContent,$EndToken,$CurrentLinkStartPos);

               // Link-String extrahieren
               $CurrentLinkString = substr($HTMLContent,$CurrentLinkStartPos + strlen($StartToken),$CurrentLinkEndPos - $CurrentLinkStartPos - strlen($StartToken));

               // Attribute des Links parsen
               $CurrentLinkAttributes = xmlParser::getAttributesFromString($CurrentLinkString);

               // Link rewriten, falls gewünscht
               if(isset($CurrentLinkAttributes[$AttributeToken])){

                  // Prüfen, ob Attribut "linkrewrite" vorhanden ist
                  if(isset($CurrentLinkAttributes['linkrewrite']) && $CurrentLinkAttributes['linkrewrite'] == 'false'){
                   // end if
                  }
                  else{
                     $CurrentLinkAttributes[$AttributeToken] = $this->__replaceURISeparators($CurrentLinkAttributes[$AttributeToken]);
                   // end else
                  }

                // end if
               }

               // Neuen Link-String erzeugen
               $CurrentReplacedLinkString = $StartToken.' '.$this->__getAttributesAsString($CurrentLinkAttributes,array('linkrewrite')).'>';

               // Bisherigen Link-String ersetzen
               $HTMLContent = substr_replace($HTMLContent,$CurrentReplacedLinkString,$CurrentLinkStartPos,$CurrentLinkEndPos - $CurrentLinkStartPos + strlen($EndToken));

               // SearchOffset erhöhen
               $SearchOffset = $CurrentLinkEndPos + strlen($EndToken);

             // end if
            }
            else{
               $TokenFound = false;
             // end else
            }

          // end while
         }

         // Timer stoppen
         $T->stop('htmlLinkRewriteFilter::__filter()');

         // Rewriteten HTMLContent zurückgeben
         return $HTMLContent;

       // end function
      }


      /**
      *  @private
      *
      *  Ersetzt in URLs übliche Request-Strings durch Slashes.<br />
      *
      *  @param string $String; URL-Teil
      *  @return string $String; Ersetzter URL-Teil
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.03.2006<br />
      *  Version 0.2, 16.04.2006<br />
      *  Version 0.3, 27.07.2006 (Bug beim Replacen behoben ('./?' statt '/?'))<br />
      *  Version 0.4, 01.08.2006 (Bug behoben, dass eine URI http://localhost/?Seite=123 falsch rewritet wurde)<br />
      *  Version 0.5, 02.06.2007 (Encoded ampersands werden nun auch ersetzte)<br />
      *  Version 0.6, 08.06.2007 (von "Page" nach "htmlLinkRewriteFilter" umgezogen)<br />
      */
      function __replaceURISeparators($String){

         $Replace = array('/?' => '/',
                          './?' => '/',
                          '=' => '/',
                          '&' => '/',
                          '&amp;' => '/',
                          '?' => '/'
                         );
         return strtr($String,$Replace);

       // end function
      }

    // end class
   }
?>