<?php
   /**
   *  @class FontStyleProvider
   *
   *  Implements the font style parser.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class FontStyleProvider extends BBCodeParserProvider
   {

      function FontStyleProvider(){
      }


      /**
      *  @public
      *
      *  Implements the getOutput() method of the abstract BBCodeParserProvider. Parses font styles
      *  like [b], [i] and [u].
      *
      *  @param string $string the content to parse
      *  @return string $parsedString the parsed content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function getOutput($string){
         return strtr(
                      $string,
                      array('[b]' => '<strong>',
                            '[B]' => '<strong>',
                            '[/b]' => '</strong>',
                            '[/B]' => '</strong>',
                            '[i]' => '<em>',
                            '[I]' => '<em>',
                            '[/i]' => '</em>',
                            '[/I]' => '</em>',
                            '[u]' => '<u>',
                            '[U]' => '<u>',
                            '[/u]' => '</u>',
                            '[/U]' => '</u>'
                           )
                     );
       // end function
      }

    // end class
   }
?>