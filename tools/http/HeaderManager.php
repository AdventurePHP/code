<?php
   /**
   *  @package tools::http
   *  @class HeaderManager
   *  @see http://forum.adventure-php-framework.org/de/viewtopic.php?p=243#p243
   *
   *  The HeaderManager implements a wrapper on PHP's header() function and let's
   *  you easily forward, relocate or send generic headers.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 09.10.2008<br />
   */
   class HeaderManager
   {

      function HeaderManager(){
      }


      /**
      *  @public
      *  @static
      *
      *  Forwards to a given target.
      *
      *  @param string $TargetURL the target URL
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function forward($TargetURL){
         header('Location: '.str_replace('&amp;','&',$TargetURL));
       // end function
      }


      /**
      *  @public
      *  @static
      *  @see http://www.faqs.org/rfcs/rfc2616
      *
      *  Redirects to a given target.
      *
      *  @param string $TargetURL the target URL
      *  @param bool $Permanent indicates, if the redirect is permanent (true) or not (false)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function redirect($TargetURL,$Permanent = false){

         if($Permanent === true){
            $StatusCode = 301;
          // end if
         }
         else{
            $StatusCode = 302;
          // end else
         }

         header('Location: '.str_replace('&amp;','&',$TargetURL),false,$StatusCode);

       // end function
      }


      /**
      *  @public
      *  @static
      *  @see http://www.faqs.org/rfcs/rfc2616
      *
      *  Sends a generic header.
      *
      *  @param string $Content the content of the header
      *  @param bool $ReplacePrevHeaders indicates, if previous headers should be overwritten
      *  @param int $HTTPStatus the HTTP status code
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function send($Content,$ReplacePrevHeaders = false,$HTTPStatus = false){

         if($HTTPStatus === false){
            header($Content,$ReplacePrevHeaders);
          // end if
         }
         else{
            header($Content,$ReplacePrevHeaders,$HTTPStatus);
          // end else
         }

       // end function
      }

    // end class
   }
?>