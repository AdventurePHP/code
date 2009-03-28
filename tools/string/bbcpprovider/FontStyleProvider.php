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