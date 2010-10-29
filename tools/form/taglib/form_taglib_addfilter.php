<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package tools::form::filter
    * @class form_taglib_addfilter
    *
    * Implements the taglib, that lets you add a form element filter to
    * the desired element.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   class form_taglib_addfilter extends form_control_observer {

      /**
       * @public
       *
       * Prefills the namespace attribute, to be able to leave it out
       * while declaring an form:addfilter tag and using packaged
       * validators.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function __construct(){
         $this->setAttribute('namespace','tools::form::filter');
      }

      /**
       * @public
       *
       * Adds the desired filter to the given form element.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.08.2009<br />
       */
      public function onAfterAppend(){
         $this->__addObserver('addFilter');
      }

    // end class
   }
?>