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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

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
      *  Version 0.1, 28.10.2008<br />
      *  Version 0.2, 29.10.2008 (Changed font tags to span tags)<br />
      */
      function getOutput($string){

         // get configuration
         $config = &$this->__getConfiguration('tools::string::bbcpprovider','fontcolor');
         $colors = $config->getSection('Colors');

         // parse text
         foreach($colors as $key => $value){
            $string = strtr($string,array('['.$key.']' => '<span style="color: '.$value.';">', '[/'.$key.']' => '</span>'));
          // end forech
         }

         // return parsed text
         return $string;

       // end function
      }

    // end class
   }
?>