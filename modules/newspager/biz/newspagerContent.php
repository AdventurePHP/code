<?php
   /**
   *  @package modules::newspager::biz
   *  @class newspagerContent
   *
   *  Domain object class.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.20.2008<br />
   */
   class newspagerContent extends coreObject
   {

      /**
      *  @private
      *  Headline of a news page.
      */
      var $__Headline;


      /**
      *  @private
      *  Subheadline of a news page.
      */
      var $__Subheadline;


      /**
      *  @private
      *  Content of a news page.
      */
      var $__Content;


      /**
      *  @private
      *  Number of news pages.
      */
      var $__NewsCount;


      function newspagerContent(){
      }

    // end class
   }
?>