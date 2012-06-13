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
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

/**
 * @package tools::form::taglib
 * @class form_taglib_addtaglib
 *
 *  Implements the &lt;form:addtaglib /&gt; tag.
 *
 * @author Christian Achatz
 * @version
 *  Version 0.1, 11.07.2008<br />
 */
class form_taglib_addtaglib extends core_taglib_addtaglib {

   public function __construct() {
   }

   /**
    * @public
    *
    * Implements the isValid() method for the addtaglib tag. Due
    * to the fact, that this taglib has nothing to do with validation,
    * true is returned in all cases.
    *
    * @return boolean Always true.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function isValid() {
      return true;
   }

   /**
    * @public
    *
    * Implements the isSent() method for the addtaglib tag. Due
    * to the fact, that this taglib has nothing to do with validation,
    * false (=not sent) is returned in all cases.
    *
    * @return boolean Always true.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function isSent() {
      return false;
   }

}