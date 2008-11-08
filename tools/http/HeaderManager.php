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
      *  @param string $targetURL the target URL
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function forward($targetURL){
         header('Location: '.str_replace('&amp;','&',$targetURL));
       // end function
      }


      /**
      *  @public
      *  @static
      *  @see http://www.faqs.org/rfcs/rfc2616
      *
      *  Redirects to a given target.
      *
      *  @param string $targetURL the target URL
      *  @param bool $permanent indicates, if the redirect is permanent (true) or not (false)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function redirect($targetURL,$permanent = false){

         if($permanent === true){
            $statusCode = 301;
          // end if
         }
         else{
            $statusCode = 302;
          // end else
         }

         header('Location: '.str_replace('&amp;','&',$targetURL),false,$statusCode);

       // end function
      }


      /**
      *  @public
      *  @static
      *  @see http://www.faqs.org/rfcs/rfc2616
      *
      *  Sends a generic header.
      *
      *  @param string $content the content of the header
      *  @param bool $replacePrevHeaders indicates, if previous headers should be overwritten
      *  @param int $HTTPStatus the HTTP status code
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.10.2008<br />
      */
      function send($content,$replacePrevHeaders = false,$HTTPStatus = false){

         if($HTTPStatus === false){
            header($content,$replacePrevHeaders);
          // end if
         }
         else{
            header($content,$replacePrevHeaders,$HTTPStatus);
          // end else
         }

       // end function
      }

    // end class
   }
?>