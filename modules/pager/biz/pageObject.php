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
   *  @namespace modules::pager::biz
   *  @class pageObject
   *
   *  Repräsentiert das Business-Objekt 'pageObject'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   */
   class pageObject extends coreObject
   {

      var $__Page;
      var $__Link;
      var $__isSelected;
      var $__entriesCount;
      var $__pageCount;


      function pageObject(){

         $this->__Page = (string)'';
         $this->__Link = (string)'';
         $this->__isSelected = false;
         $this->__entriesCount = (int)0;
         $this->__pageCount = (int)0;

       // end function
      }

    // end class
   }
?>
