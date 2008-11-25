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
   *  @namespace sites::apfdocupage::biz
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