<?php
   /**
   *  @package tools::validator
   *  @class myValidator
   *
   *  Stellt Methoden zur Validierung von Strings zu Verfügung.<br />
   *
   *  @author Christian Schäfer
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
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
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
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
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
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
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
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
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
      *  Validiert eine Zahl.<br />
      *
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function validateNumber($String){

         if(preg_match("/^[0-9]{1,}+$/",trim($String))){
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
      *  @param string $String; String der geprüft werden soll
      *  @return true|false
      *
      *  @author Christian Schäfer
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
      *  Validiert einen String gegen eine übergebene RegExp.<br />
      *
      *  @param string $String; String der geprüft werden soll
      *  @param string $RegExp; Regulärer Ausdruck
      *  @return true|false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, ??.??.????<br />
      *  Version 0.2, 03.02.2006 (Tippfehler in Überprüfung behoben)<br />
      *  Version 0.3, 12.01.2007 (Es werden nun bool'sche Werte zurückgegeben)<br />
      */
      function validateRegExp($String,$RegExp){

         if(!empty($String) && preg_match(trim($RegExp),trim($String))){
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