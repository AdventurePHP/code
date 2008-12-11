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

   import('core::filter','AbstractRequestFilter');


   /**
   *  @namespace core::filter
   *  @class standardRequestFilter
   *
   *  Implementiert den URL-Filter für den PageController ohne URL-Rewrite-Modus.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 02.06.2007<br />
   */
   class standardRequestFilter extends AbstractRequestFilter
   {

      function standardRequestFilter(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Filter-Funktion aus "abstractRequestFilter".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.06.2007<br />
      */
      function filter(){
         $this->__filterRequestArray();
       // end function
      }

    // end class
   }
?>