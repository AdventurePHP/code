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

   import('tools::html::taglib','ui_getstring');


   /**
   *  @namespace tools::html::taglib
   *  @class template_taglib_getstring
   *
   *  Implementiert die TagLib für den Tag "<template:getstring />".<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.04.2006<br />
   *  Version 0.2, 10.11.2008 (Removed the onParseTime() method, because the registerTagLibModule() function now is obsolete)<br />
   */
   class template_taglib_getstring extends ui_getstring
   {

      function template_taglib_getstring(){
      }

    // end class
   }
?>