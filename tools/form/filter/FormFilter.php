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

   import('tools::form::filter','AbstractFormFilter');

   /**
    * @namespace tools::form::filter
    * @class FormFilter
    *
    * Implements a default filter for textual form elements.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.12.2008<br />
    */
   class FormFilter extends AbstractFormFilter {

      public function FormFilter(){
      }

      /**
       * @public
       *
       * Implements the filter() method. Wrapps the included filter instruction methods.
       *
       * @param string $filterInstruction the filter instruction
       * @param string $input the filter's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       * Version 0.2, 17.07.2009 (Applied the filter refactoring to the form filters)<br />
       */
      public function filter($input){
         return $this->{'__'.$this->__Instruction}($input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that turns all capitals to small letters.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __string2Lower($input){
         return strtolower($input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that turns all small letters to capitals.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __string2Upper($input){
         return strtoupper($input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that strips all tags.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __stripTags($input){
         return strip_tags($input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that removes all special characters.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __noSpecialCharacters($input){
         return preg_replace('/[^0-9A-Za-z-_\.& ]/i','',$input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that only accepts numbers.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __onlyNumbers($input){
         return preg_replace('/[^0-9\-\.,]/i','',$input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that only accepts integers.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __onlyInteger($input){
         return (int)$this->onlyNumbers($input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that only accepts letters.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __onlyLetters($input){
         return preg_replace('/[^A-Za-z& ]/i','',$input);
       // end function
      }


      /**
       * @protected
       *
       * Implements the filter method, that transcodes the input to it's HTML entities.
       *
       * @param string $input the filter method's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.12.2008<br />
       */
      protected function __onlyHTMLEntities($input){
         return htmlentities(str_replace('&amp;','&',$input));
       // end function
      }

    // end function
   }
?>