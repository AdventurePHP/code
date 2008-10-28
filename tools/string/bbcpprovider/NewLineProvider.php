<?php
   /**
   *  @class FontSizeProvider
   *
   *  Implements the font color parser.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class NewLineProvider extends BBCodeParserProvider
   {

      function NewLineProvider(){
      }


      /**
      *  @public
      *
      *  Implements the getOutput() method of the abstract BBCodeParserProvider. Parses newline
      *  characters.
      *
      *  @param string $string the content to parse
      *  @return string $parsedString the parsed contebt
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function getOutput($string){
         return nl2br($string);
       // end function
      }

    // end class
   }
?>