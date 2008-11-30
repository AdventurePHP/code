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

   import('core::filter','abstractRequestFilter');
   import('core::frontcontroller','Frontcontroller');


   /**
   *  @namespace core::request
   *  @class frontcontrollerRewriteRequestFilter
   *
   *  Implementiert den Request-URL-Filter für den Frontcontroller mit aktiviertem URL-Rewriting.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   */
   class frontcontrollerRewriteRequestFilter extends abstractRequestFilter
   {

      /**
      *  @private
      *  Definiert das URL-Rewriting URL-Trennzeichen.
      */
      var $__RewriteURLDelimiter = '/';


      /**
      *  @private
      *  Trennzeichen zwischen Parameter- und Action-Strings.
      */
      var $__ActionDelimiter = '/~/';


      /**
      *  @private
      *  Action-Keyword.
      */
      var $__FrontcontrollerActionKeyword;


      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function frontcontrollerRewriteRequestFilter(){
         $fC = &Singleton::getInstance('Frontcontroller');
         $this->__FrontcontrollerActionKeyword = $fC->get('NamespaceKeywordDelimiter').$fC->get('ActionKeyword');
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Filter-Funktion aus "abstractRequestFilter".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (In "filter()" umbenannt)<br />
      *  Version 0.3, 17.06.2007 (Stripslashes- und Htmlentities-Filter hinzugefügt)<br />
      *  Version 0.4, 08.09.2007 (Es wird nun auch abgefragt, ob sich nur das ActionKeyword in der URL befindet (Fall: nur eine Action, ohne ActionDelimiter)<br />
      *  Version 0.5, 29.09.2007 (Filter löscht nun $_REQUEST['query'])<br />
      */
      function filter(){

         // Instanz des Frontcontrollers holen
         $fC = &Singleton::getInstance('Frontcontroller');


         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('frontcontrollerRewriteRequestFilter::filter()');


         // PHPSESSID aus $_REQUEST extrahieren, falls vorhanden
         $T->start('extractSessionValueFromRequest()');
         $PHPSESSID = (string)'';
         $SessionName = ini_get('session.name');

         if(isset($_REQUEST[$SessionName])){
            $PHPSESSID = $_REQUEST[$SessionName];
          // end if
         }

         $T->stop('extractSessionValueFromRequest()');


         // Timer starten
         $T->start('filterRequestURI()');


         // Offset "query" aus $_REQUEST löschen, damit pagecontrollerRewriteRequestFilter nicht
         // mehr anspringt.
         unset($_REQUEST['query']);


         // Request-URI in Array extrahieren
         //
         // BETA (08.09.2007): Es wird nun mit
         //   substr_count($_SERVER['REQUEST_URI'],$this->__FrontcontrollerActionKeyword.'/') > 0
         // auch auf das vorkommen eines ActionKeywords geprüft - ohne Delimiter. Bei Verwendung des
         // frontcontrollerLinkHandlers ist das zwar nicht notwendig, bei manuellem Erstellen des
         // FC-Links schon. Sollte es Probleme damit geben wird das Verhalten im folgenden Release
         // wieder entfernt.
         if(substr_count($_SERVER['REQUEST_URI'],$this->__ActionDelimiter) > 0 || substr_count($_SERVER['REQUEST_URI'],$this->__FrontcontrollerActionKeyword.'/') > 0){

            // URL nach Delimiter trennen
            $RequestURLParts = explode($this->__ActionDelimiter,$_SERVER['REQUEST_URI']);

            for($i = 0; $i < count($RequestURLParts); $i++){

               // Slashed am Anfang entfernen
               $RequestURLParts[$i] = $this->__deleteTrailingSlash($RequestURLParts[$i]);

               // Frontcontroller-Action enthalten
               if(substr_count($RequestURLParts[$i],$this->__FrontcontrollerActionKeyword) > 0){

                  // Timer starten
                  $T->start('filterFrontcontrollerAction('.$i.')');


                  // String zerlegen
                  $RequestArray = explode($this->__RewriteURLDelimiter,$RequestURLParts[$i]);

                  if(isset($RequestArray[1])){

                     // Action-Parameter erzeugen
                     $ActionNamespace = str_replace($this->__FrontcontrollerActionKeyword,'',$RequestArray[0]);
                     $ActionName = $RequestArray[1];
                     $ActionParams = array_slice($RequestArray,2);


                     // Action-Parameter-Array erzeugen
                     $ActionParamsArray = array();

                     if(count($ActionParams) > 0){

                        $x = 0;

                        while($x <= (count($ActionParams) - 1)){

                           if(isset($ActionParams[$x + 1])){
                              $ActionParamsArray[$ActionParams[$x]] = $ActionParams[$x + 1];
                            // end if
                           }

                           // Offset-Zähler um 2 erhöhen
                           $x = $x + 2;

                         // end while
                        }

                      // end if
                     }


                     // Action zum Frontcontroller hinzufügen
                     $fC->addAction($ActionNamespace,$ActionName,$ActionParamsArray);

                   // end if
                  }

                  // Timer stoppen
                  $T->stop('filterFrontcontrollerAction('.$i.')');

                // end if
               }
               else{

                  $T->start('filterRewriteParameters('.$i.')');
                  $ParamArray = $this->__createRequestArray($RequestURLParts[$i]);
                  $_REQUEST = array_merge($_REQUEST,$ParamArray);
                  $T->stop('filterRewriteParameters('.$i.')');

                // end else
               }

             // end for
            }

          // end if
         }
         else{

            // Standard-Rewrite wie PageController URL-Rewriting
            $T->start('filterRewriteParameters()');
            $ParamArray = $this->__createRequestArray($_SERVER['REQUEST_URI']);
            $_REQUEST = array_merge($_REQUEST,$ParamArray);
            $T->stop('filterRewriteParameters()');

          // end if
         }

         // Timer stoppen
         $T->stop('filterRequestURI()');


         // Post-Parameter mit einbeziehen
         $_REQUEST = array_merge($_REQUEST,$_POST);


         // PHPSESSID in Request wieder einsetzen
         $T->start('addSessionValueToRequest()');

         if(!empty($PHPSESSID)){
            $_REQUEST[$SessionName] = $PHPSESSID;
          // end if
         }

         $T->stop('addSessionValueToRequest()');

         // Request-Array filtern
         $this->__filterRequestArray();

         // Timer stoppen
         $T->stop('frontcontrollerRewriteRequestFilter::filter()');

       // end function
      }

    // end class
   }
?>