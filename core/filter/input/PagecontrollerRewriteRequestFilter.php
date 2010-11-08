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
   import('core::filter::input','AbstractRequestFilter');

   /**
    * @package core::filter::input
    * @class PagecontrollerRewriteRequestFilter
    *
    * Implements the URL filter for the page controller in rewrite url mode.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 02.06.2007<br />
    */
   class PagecontrollerRewriteRequestFilter extends AbstractRequestFilter {

      /**
       * @protected
       * @var string Defines the URL rewrite param-to-value delimiter.
       */
      protected $__RewriteURLDelimiter = '/';

      /**
       * @public
       *
       * Implements the rewrite url input filter for the page controller.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 02.06.2007<br />
       * Version 0.2, 08.06.2007 (Renamed to "filter()")<br />
       * Version 0.3, 16.06.2007 (Changed URL rewriting behavior, so that mixed param sets are allowed)<br />
       * Version 0.4, 29.09.2007 (Filtr is only active, if the $_REQUEST['query'] is set)<br />
       * Version 0.5, 12.12.2008 (Rewrited some code, added documentation in englisch)<br />
       * Version 0.6, 13.12.2008 (Removed the benchmarker)<br />
       */
      public function filter($input){

         // backup PHPSESSID if applicable
         $PHPSESSID = (string)'';
         $sessionName = ini_get('session.name');

         if(isset($_REQUEST[$sessionName])){
            $PHPSESSID = $_REQUEST[$sessionName];
         }

         // filter query
         if(isset($_REQUEST[self::$REWRITE_QUERY_PARAM])
                 && !empty($_REQUEST[self::$REWRITE_QUERY_PARAM])){

            // read the query string presented to the
            // bootstrap file by apache's mod_rewrite
            $query = $_REQUEST[self::$REWRITE_QUERY_PARAM];

            // delete the rewite param indicator
            unset($_REQUEST[self::$REWRITE_QUERY_PARAM]);
            unset($_GET[self::$REWRITE_QUERY_PARAM]);

            // backup the request array
            $requestBackup = $_REQUEST;

            // reset the request array
            $_REQUEST = array();

            // recreate the request array using the uri informations within the query param
            $_REQUEST = $this->__createRequestArray($query);

            // merge backup into the request array
            $_REQUEST = array_merge($_REQUEST, $requestBackup);
            unset($requestBackup);

            // re-initialize GET params to support e.g. form submition
            $_GET = $_REQUEST;

            // merge port params into the request again
            $_REQUEST = array_merge($_REQUEST, $_POST);

            // reinsert the PHPSESSID value into the request array
            if (!empty($PHPSESSID)) {
               $_REQUEST[$sessionName] = $PHPSESSID;
            }

            // filter request array
            $this->__filterRequestArray();

         }

      }

    // end class
   }
?>