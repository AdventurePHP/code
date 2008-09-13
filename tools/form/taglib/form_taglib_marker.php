<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_marker
   *
   *  Represents the <form:marker /> tag, that can be used to dynamically create forms. Please
   *  have a look at the API documentation of the html_taglib_form class for details.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 03.09.2008<br />
   */
   class form_taglib_marker extends Document
   {

      function form_taglib_marker(){
      }


      /**
      *  @public
      *
      *  Overwrites the onParseTime() method from the Document class, because here's nothing to do.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function onParseTime(){
      }


      /**
      *  @public
      *
      *  Overwrites the onAfterAppend() method from the Document class, because here's nothing to do.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function onAfterAppend(){
      }


      /**
      *  @public
      *
      *  Implements the transform() method. Returns an empty string.
      *
      *
      *  @return string $Content an empty string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function transform(){
         return (string)'';
       // end function
      }

    // end class
   }
?>