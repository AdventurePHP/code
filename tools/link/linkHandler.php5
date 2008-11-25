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
   *  @namespace tools::link
   *  @class linkHandler
   *  @static
   *
   *  Presents a method to generate and valudate urls.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 04.05.2005<br />
   *  Version 0.2, 11.05.2005<br />
   *  Version 0.3, 27.06.2005<br />
   *  Version 0.4, 25.04.2006<br />
   *  Version 0.5, 27.03.2007 (Replaced deprecated code)<br />
   *  Version 0.6, 21.06.2008 (Introduced Registry)<br />
   */
   class linkHandler
   {

      function linkHandler(){
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert aus einer übergebenen URI und einem Parameter-Array eine neue URI.<br />
      *  Es werden folgende Parameter übergeben:<br />
      *  <br />
      *    - string $URL: eine gültige URL.<br />
      *    - array $Parameter: assoziatives, nicht mehr dimensional assoziatives, Array von URL-Parametern.<br />
      *    - boolean $URLRewrite: bei 'true' wird die URL als Pfad-URL rewritet,<br />
      *      bei 'false' so belassen. Der Standardwert wird aus der Registry bezogen.<br />
      *  <br />
      *  Die Option $Parameter bestimmt, welche Parameter der URL gelöscht, welche anders gesetzt,<br />
      *  und welche belassen werden. Aus der URL<br />
      *  <br />
      *    http://myhost.de/index.php?Seite=123&Button=Send&Benutzer=456&Passwort=789<br />
      *  <br />
      *  wird durch Übergabe des Arrays<br />
      *  <br />
      *    array('Seite' => 'neueSeite','Button' => '')<br />
      *  <br />
      *  die URL<br />
      *  <br />
      *    http://myhost.de/index.php?Seite=neueSeite&Benutzer=456&Passwort=789<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.04.2006<br />
      *  Version 0.2, 01.05.2006 (Bug behoben, dass Value mit Länge 1 herausgefiltert wird)<br />
      *  Version 0.3, 06.05.2006 (Bug behoben, dass bei fehlender Query ein Fehler geworfen wird)<br />
      *  Version 0.4, 29.07.2006 (Umbau, damit Links in Rewrite-Technik sauber geparst werden)<br />
      *  Version 0.5, 14.08.2006 (Parameter "RewriteLink" wird nun standardmäßig mit der globalen Konfigurationskonstante "APPS__URL_REWRITING" gefüllt)<br />
      *  Version 0.6, 24.02.2007 (in generateLink() umbenannt)<br />
      *  Version 0.7, 27.05.2007 (Falls URL-Path nich existiert, wird dieser nun als leer angenommen)<br />
      *  Version 0.8, 02.06.2007 (Ampersands werden nun konvertiert, falls URL-Rewriting nicht aktiviert ist)<br />
      *  Version 0.9, 16.06.2007 (Ampersands werden am Anfang decodiert, da sonst Parsing-Fehler auftreten)<br />
      *  Version 1.0, 26.08.2007 (URL wird nun auf is_string() geprüft; URL-Parameter akzeptieren keine mehrdimensionales Arrays!)<br />
      *  Version 1.1, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
      */
      static function generateLink($URL,$Parameter,$URLRewriting = null){

         // Prüfen, ob $URL ein String ist
         if(!is_string($URL)){
            trigger_error('[linkHandler::generateLink()] Given url is not a string!',E_USER_WARNING);
            $URL = strval($URL);
          // end if
         }

         // Apmersands decodieren
         $URL = str_replace('&amp;','&',$URL);

         // URL zerlegen
         $ParsedURL = parse_url($URL);

         // Query-String zerlegen
         if(!isset($ParsedURL['query'])){
            $ParsedURL['query'] = (string)'';
          // end if
         }

         // Path vorgeben, falls nicht vorhanden
         if(!isset($ParsedURL['path'])){
            $ParsedURL['path'] = (string)'';
          // end if
         }

         // set URLRewrite
         if($URLRewriting === null){
            $Reg = &Singleton::getInstance('Registry');
            $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
          // end if
         }

         // URL je nach URL-Typ zerlegen
         if($URLRewriting == true){

            // Request (in diesem Fall der 'Path') in Array extrahieren
            $RequestArray = explode('/',strip_tags($ParsedURL['path']));
            array_shift($RequestArray);

            // Request-Array zurücksetzen
            $SplitURL = array();

            // Offset-Zähler setzen
            $x = 0;

            // RequestArray durchiterieren und auf dem Offset x den Key und auf Offset x+1
            // die Value aus der Request-URI extrahieren
            while($x <= (count($RequestArray) - 1)){

               if(isset($RequestArray[$x + 1])){
                  $SplitURL[$RequestArray[$x]] = $RequestArray[$x + 1];
                // end if
               }

               // Offset-Zähler um 2 erhöhen
               $x = $x + 2;

             // end while
            }

            $SplitParameters = $SplitURL;

          // end if
         }
         else{
            $SplitURL = explode('&',$ParsedURL['query']);

            // Parameter der Query zerlegen
            $SplitParameters = array();

            for($i = 0; $i < count($SplitURL); $i++){

               // Nur Parameter größer 3 Zeichen (z.B. a=b) beachten
               if(strlen($SplitURL[$i]) > 3){

                  // Position des '=' suchen
                  $EqualSign = strpos($SplitURL[$i],'=');

                  // Array mit den Parametern als Key => Value - Paar erstellen
                  $SplitParameters[substr($SplitURL[$i],0,$EqualSign)] = substr($SplitURL[$i],$EqualSign+1,strlen($SplitURL[$i]));

                // end if
               }

             // end for
            }

          // end else
         }

         // Erzeugtes und übergebenes Parameter-Set zusammenführen (dadurch können Löschungen realisiert werden)
         $SplitParameters = array_merge($SplitParameters,$Parameter);

         // Query-String an Hand der gemergten Parameter erzeugen
         $Query = (string)'';

         foreach($SplitParameters as $Key => $Value){

            // Nur Keys mit einer Länge > 1 und Values mit einer Länge > 0 betrachten, damit
            // ein 'Test' => '' eine Löschung bedeutet.
            // Prüfen, ob $Value ein Array ist und dieses ablehnen!
            if(!is_array($Value)){

               if(strlen($Key) > 1 && strlen($Value) > 0){

                  // '?' als erstes Bindezeichen setzen
                  if(strlen($Query) == 0){
                     $Query .= '?';
                   // end if
                  }
                  else{
                     $Query .= '&';
                   // end else
                  }

                  // 'Key' => 'Value' - Paar zusammensetzen
                  $Query .= trim($Key).'='.trim($Value);

                // end if
               }

             // end if
            }

          // end function
         }

         // URL generieren
         $NewURL = (string)'';

         // Falls Schema und Host gegeben, diese einbinden
         if(isset($ParsedURL['scheme']) && isset($ParsedURL['host'])){
            $NewURL .= $ParsedURL['scheme'].'://'.$ParsedURL['host'];
          // end if
         }

         // Falls nur Host gegeben, diesen einsetzen
         if(!isset($ParsedURL['scheme']) && isset($ParsedURL['host'])){
            $NewURL .= '/'.$ParsedURL['host'];
          // end if
         }


         // URL final zusammensetzen
         if($URLRewriting == true){
            $FinishedURL = $NewURL.'/'.$Query;

          // end if
         }
         else{
            $FinishedURL = $NewURL.$ParsedURL['path'].$Query;
          // end else
         }

         // Link URL-Rewriten, falls gewünscht
         if($URLRewriting == true){

            $Replace = array('./?' => '/',
                             '/?' => '/',
                             '=' => '/',
                             '&' => '/'
                            );
            $FinishedURL = strtr($FinishedURL,$Replace);

          // end if
         }
         else{

            // Ampersands codieren
            $FinishedURL = str_replace('&','&amp;',$FinishedURL);

          // end else
         }

         // Fertige URL zurückgeben
         return $FinishedURL;

       // end function
      }

    // end class
   }
?>