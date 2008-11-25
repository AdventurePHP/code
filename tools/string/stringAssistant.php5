<?php
   /**
   *  @namespace tools::string
   *  @class stringAssistant
   *  @static
   *
   *  Stellt Methoden zur erweiterten String-Bearbeitung zur Verf�gung.<br />
   *
   *  @author Christian Sch�fer
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
      *  Entfernt Sonderzeichen, die f�r die Speicherung in der Datenbank<br />
      *  und das sp�tere Anzeigen in HTML-Code Probleme machen.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.01.2005<br />
      */
      static function escapeSpecialCharacters($String,$escape4mysql = false){

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
      *  Ersetzt die im Web f�r Dateinamen nicht zul�ssigen Sonderzeichen und gibt<br />
      *  bereinigten String zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 2004<br />
      *  Version 0.2, 2004<br />
      *  Version 0.3, 26.01.2005<br />
      *  Version 0.4, 29.03.2005<br />
      *  Version 0.5, 05.06.2006 (Optimierung der Ersetzung (' statt " in der Array-Aufz�hlung))<br />
      *  Version 0.6, 31.03.2007 ("?" in die Liste mit aufgenommen)<br />
      */
      static function replaceSpecialCharacters($String){

         $Ersatz = array(
                         '�' => 'ae',
                         '�' => 'oe',
                         '�' => 'ue',
                         '�' => 'ae',
                         '�' => 'oe',
                         '�' => 'ue',
                         '�' => 'ss',
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
                         '�' => '_',
                         '/' => '_',
                         '\'' => '',
                         '�' => '',
                         '`' => '',
                         '*' => '',
                         '#' => '',
                         '�' => '',
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 24.06.2007<br />
      */
      static function encodeCharactersToHTML($String){

         // Inhalt von Leerzeichen befreien
         $Content = trim($String);

         // Puffer f�r Ausgabe initialisieren
         $EncodedContent = (string)'';

         // Zeichen codieren
         for($i = 0; $i < strlen($Content); $i++){
            $EncodedContent .= '&#'.ord($Content[$i]).';';
          // end for
         }

         // Ergebnis zur�ckgeben
         return $EncodedContent;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert einen String, der als Captcha-String verwendet werden kann.<br />
      *
      *  @param int $Length L�nge des Strings
      *  @return string $CaptchaString Captcha-String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2007<br />
      */
      static function generateCaptchaString($Length){

         // Shuffeln der Zufallszahlen
         srand(stringAssistant::generateSeed());

         // Definition aller zul�ssigen Zeichen
         $StringBase = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';

         // String erzeugen
         $CaptchaString = (string)'';

         while(strlen($CaptchaString) < $Length) {
            $CaptchaString .= substr($StringBase,(rand()%(strlen($StringBase))),1);
          // end while
         }

         // String zur�ckgeben
         return $CaptchaString;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Generiert einen Zufallsstartwert f�r die PHP-Funktion srand().<br />
      *
      *  @return int $StartValue Startwert f�r die srand()-Funktion
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2007<br />
      */
      static function generateSeed(){
        list($usec, $sec) = explode(' ',microtime());
        return (float) $sec + ((float) $usec * 100000);
       // end function
      }

    // end class
   }
?>