<?php
   /**
   *  @class FontSizeProvider
   *
   *  Implements the font size parser.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class FontSizeProvider extends BBCodeParserProvider
   {

      function FontSizeProvider(){
      }


      /**
      *  @public
      *
      *  Implements the getOutput() method of the abstract BBCodeParserProvider. Parses font size
      *  definitions provided th the "fontsize" configuration file under the tools::string::bbcpprovider
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
         $config = &$this->__getConfiguration('tools::string::bbcpprovider','fontsize');
         $sizes = $config->getSection('Sizes');

         // parse text
         foreach($sizes as $key => $value){
            $string = strtr($string,array('['.$key.']' => '<font style="font-size: '.$value.';">', '[/'.$key.']' => '</font>'));
          // end forech
         }

         // return parsed text
         return $string;

       // end function
      }

    // end class
   }
?>