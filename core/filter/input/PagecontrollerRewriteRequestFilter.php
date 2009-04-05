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

   import('core::filter::input','AbstractRequestFilter');


   /**
   *  @namespace core::filter::input
   *  @class PagecontrollerRewriteRequestFilter
   *
   *  Implements the URL filter for the page controller in rewrite url mode.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 02.06.2007<br />
   */
   class PagecontrollerRewriteRequestFilter extends AbstractRequestFilter
   {

      /**
      *  @protected
      *  Defines the URL rewrite param-to-value delimiter.
      */
      protected $__RewriteURLDelimiter = '/';


      function PagecontrollerRewriteRequestFilter(){
      }


      /**
      *  @public
      *
      *  Implements the rewrite url input filter for the page controller.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (Renamed to "filter()")<br />
      *  Version 0.3, 16.06.2007 (Changed URL rewriting behavior, so that mixed param sets are allowed)<br />
      *  Version 0.4, 29.09.2007 (Filtr is only active, if the $_REQUEST['query'] is set)<br />
      *  Version 0.5, 12.12.2008 (Rewrited some code, added documentation in englisch)<br />
      *  Version 0.6, 13.12.2008 (Removed the benchmarker)<br />
      */
      function filter(){

         // backup PHPSESSID if applicable
         $PHPSESSID = (string)'';
         $sessionName = ini_get('session.name');

         if(isset($_REQUEST[$sessionName])){
            $PHPSESSID = $_REQUEST[$sessionName];
          // end if
         }

         // filter query
         if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){

            // read the query string presented to the
            // bootstrap file by apache's mod_rewrite
            $query = $_REQUEST['query'];

            // delete the rewite param indicator
            unset($_REQUEST['query']);

            // backup the request array
            $requestBackup = $_REQUEST;

            // reset the request array
            $_REQUEST = array();

            // recreate the request array using the uri informations within the query param
            $_REQUEST = $this->__createRequestArray($query);

            // merge backup into the request array
            $_REQUEST = array_merge($_REQUEST,$requestBackup);
            unset($requestBackup);

            // merge port params into the request again
            $_REQUEST = array_merge($_REQUEST,$_POST);

            // reinsert the PHPSESSID value into the request array
            if(!empty($PHPSESSID)){
               $_REQUEST[$sessionName] = $PHPSESSID;
             // end if
            }

            // filter request array
            $this->__filterRequestArray();

          // end if
         }

       // end function
      }

    // end class
   }
?>