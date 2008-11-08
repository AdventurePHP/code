<?php
   /**
   *  @package sites::apfdocupage::biz
   *  @class searchResult
   *
   *  This class represents the domain object of the fulltextsearch functionality.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.03.2008<br />
   *  Version 0.2, 02.10.2008<br />
   */
   class searchResult extends coreObject
   {

      /**
      *  @private
      *  name of the content file.
      */
      var $__FileName;


      /**
      *  @private
      *  the page's title.
      */
      var $__Title;


      /**
      *  @private
      *  language of the page.
      */
      var $__Language;


      /**
      *  @private
      *  url name of the page.
      */
      var $__URLName;


      /**
      *  @private
      *  url id of the page.
      */
      var $__PageID;


      /**
      *  @private
      *  date of last modification.
      */
      var $__LastMod;


      /**
      *  @private
      *  word count in index.
      */
      var $__WordCount;


      /**
      *  @private
      *  which word was found.
      */
      var $__IndexWord;


      function searchResult(){
      }

    // end class
   }
?>