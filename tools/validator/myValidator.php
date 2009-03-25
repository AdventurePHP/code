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
   *  @namespace tools::validator
   *  @class myValidator
   *
   *  Stellt Methoden zur Validierung von Strings zu Verf�gung.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 00.00.2006<br />
   *  Version 0.2, 12.07.2007<br />
   *  Version 0.3, 27.03.2007 (Veraltete Methoden bereinigt)<br />
   */
   class myValidator
   {

      function myValidator(){
      }


      /**
      *  @public
      *  @static
      *
      *  Validiert einen Text.<br />
      *
      *  @param string $String; String der gepr�ft werden soll
      *  @return true|false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 16.06.2007 (Strings < 3 Zeichen werden als "false" gewertet)<br />
      */
      function validateText($String){

         if(!empty($String) && strlen($String) >= 3){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validiert eine E-Mail-Adresse.<br />
      *
      *  @param string $String; String der gepr�ft werden soll
      *  @return true|false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function validateEMail($String){

         if(!empty($String) && ereg("^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$",$String)){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validiert eine Telefon-Nummer.<br />
      *
      *  @param string $String; String der gepr�ft werden soll
      *  @return true|false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function validateTelefon($String){

         if(preg_match("/^[0-9\-\+\(\)\/ ]{6,}+$/",trim($String))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validiert eine Fax-Nummer.<br />
      *
      *  @param string $String; String der gepr�ft werden soll
      *  @return true|false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function validateFax($String){

         if(preg_match("/^[0-9\-\+\(\)\/ ]{6,}+$/",trim($String))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validates, if given data is a number.
      *
      *  @param string $String data to validate
      *  @return $isValid true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 15.08.2008 (Changed due to feature change request)<br />
      */
      function validateNumber($String){

         if(is_numeric(trim($String))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validiert einen Ordner-Namen.<br />
      *
      *  @param string $String; String der gepr�ft werden soll
      *  @return true|false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function validateFolder($String){

         if(preg_match("/^[a-zA-Z0-9\-\_]+$/",trim($String))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Validates a given string with the regular expression offered.
      *
      *  @param string $String string to test
      *  @param string $RegExp regular expression
      *  @return true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, ??.??.????<br />
      *  Version 0.2, 03.02.2006 (Removed typo)<br />
      *  Version 0.3, 12.01.2007 (Only boolean values are returned now)<br />
      *  Version 0.4, 21.08.2008 (Removed trim()s due to validation errors with blanks)<br />
      */
      function validateRegExp($String,$RegExp){

         if(preg_match($RegExp,$String)){
           return true;
          // end if
         }
         else{
            return false;
          // end if
         }

       // end function
      }

    // end class
   }
?>