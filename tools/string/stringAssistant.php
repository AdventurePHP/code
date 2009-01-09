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
   *  @namespace tools::string
   *  @class stringAssistant
   *  @static
   *
   *  Stellt Methoden zur erweiterten String-Bearbeitung zur Verfügung.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.02.2006<br />
   */
   class stringAssistant
   {

      function stringAssistant(){
      }


      /**
      *  @public
      *  @static
      *
      *  Entfernt Sonderzeichen, die für die Speicherung in der Datenbank<br />
      *  und das spätere Anzeigen in HTML-Code Probleme machen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.01.2005<br />
      */
      function escapeSpecialCharacters($String,$escape4mysql = false){

         //
         //   Ist magic_quotes_gpc aktiviert, dann nur htmlspecialcharacters,
         //   da $_POST/$_GET Variablen auto_addslashes haben.
         //   Wenn nicht aktiviert, dann beides.
         //

         $INI_Value = ini_get('magic_quotes_gpc');

         if($INI_Value == '1'){
            $return = htmlspecialchars($String,ENT_QUOTES);
          // end if
         }
         else{
            $return = addslashes(htmlspecialchars($String,ENT_QUOTES));
          // end
         }

         return $return;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Ersetzt die im Web für Dateinamen nicht zulässigen Sonderzeichen und gibt<br />
      *  bereinigten String zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2004<br />
      *  Version 0.2, 2004<br />
      *  Version 0.3, 26.01.2005<br />
      *  Version 0.4, 29.03.2005<br />
      *  Version 0.5, 05.06.2006 (Optimierung der Ersetzung (' statt " in der Array-Aufzählung))<br />
      *  Version 0.6, 31.03.2007 ("?" in die Liste mit aufgenommen)<br />
      */
      function replaceSpecialCharacters($String){

         $Ersatz = array(
                         'ä' => 'ae',
                         'ö' => 'oe',
                         'ü' => 'ue',
                         'Ä' => 'ae',
                         'Ö' => 'oe',
                         'Ü' => 'ue',
                         'ß' => 'ss',
                         '-' => '_',
                         ' ' => '',
                         '[' => '',
                         ']' => '',
                         '(' => '',
                         ')' => '',
                         ',' => '',
                         ';' => '',
                         '=' => '_',
                         '&' => '_',
                         '+' => '_',
                         '%' => '_',
                         '!' => '_',
                         '$' => '_',
                         '§' => '_',
                         '/' => '_',
                         '\'' => '',
                         '´' => '',
                         '`' => '',
                         '*' => '',
                         '#' => '',
                         '°' => '',
                         '^' => '',
                         '<' => '',
                         '>' => '',
                         '|' => '',
                         ':' => '',
                         '~' => '',
                         '@' => '',
                         '\\' => '',
                         '?' => '_'
                        );

         return strtolower(strtr($String,$Ersatz));

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Codiert eine Zeichenkette in HTML-Entities.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.06.2007<br />
      */
      function encodeCharactersToHTML($String){

         // Inhalt von Leerzeichen befreien
         $Content = trim($String);

         // Puffer für Ausgabe initialisieren
         $EncodedContent = (string)'';

         // Zeichen codieren
         for($i = 0; $i < strlen($Content); $i++){
            $EncodedContent .= '&#'.ord($Content[$i]).';';
          // end for
         }

         // Ergebnis zurückgeben
         return $EncodedContent;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert einen String, der als Captcha-String verwendet werden kann.<br />
      *
      *  @param int $Length Länge des Strings
      *  @return string $CaptchaString Captcha-String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2007<br />
      */
      function generateCaptchaString($Length){

         // Shuffeln der Zufallszahlen
         srand(stringAssistant::generateSeed());

         // Definition aller zulässigen Zeichen
         $StringBase = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';

         // String erzeugen
         $CaptchaString = (string)'';

         while(strlen($CaptchaString) < $Length) {
            $CaptchaString .= substr($StringBase,(rand()%(strlen($StringBase))),1);
          // end while
         }

         // String zurückgeben
         return $CaptchaString;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert einen Zufallsstartwert für die PHP-Funktion srand().<br />
      *
      *  @return int $StartValue Startwert für die srand()-Funktion
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2007<br />
      */
      function generateSeed(){
        list($usec, $sec) = explode(' ',microtime());
        return (float) $sec + ((float) $usec * 100000);
       // end function
      }

    // end class
   }
?>