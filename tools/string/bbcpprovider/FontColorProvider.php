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
   class FontColorProvider extends BBCodeParserProvider
   {

      function FontColorProvider(){
      }


      /**
      *  @public
      *
      *  Implements the getOutput() method of the abstract BBCodeParserProvider. Parses font color
      *  definitions provided th the "fontcolor" configuration file under the tools::string::bbcpprovider
      *  namespace. An configuration example can be found in the adventure-configpack-* release file.
      *
      *  @param string $string the content to parse
      *  @return string $parsedString the parsed content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function getOutput($string){

         // get configuration
         $config = &$this->__getConfiguration('tools::string::bbcpprovider','fontcolor');
         $colors = $config->getSection('Colors');

         // parse text
         foreach($colors as $key => $value){
            $string = strtr($string,array('['.$key.']' => '<font style="color: '.$value.';">', '[/'.$key.']' => '</font>'));
          // end forech
         }

         // return parsed text
         return $string;

       // end function
      }

    // end class
   }
?>