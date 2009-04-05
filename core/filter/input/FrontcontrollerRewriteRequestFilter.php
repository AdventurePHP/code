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

   import('core::filter::input','AbstractRequestFilter');


   /**
   *  @namespace core::filter::input
   *  @class FrontcontrollerRewriteRequestFilter
   *
   *  Input filter for the front controller in combination with rewritten URLs.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   */
   class FrontcontrollerRewriteRequestFilter extends AbstractRequestFilter
   {

      /**
      *  @protected
      *  Defines the global URL rewriting delimiter.
      */
      protected $__RewriteURLDelimiter = '/';


      /**
      *  @protected
      *  Delimiter between params and action strings.
      */
      protected $__ActionDelimiter = '/~/';


      /**
      *  @protected
      *  Defines the action keyword.
      */
      protected $__FrontcontrollerActionKeyword;


      function FrontcontrollerRewriteRequestFilter(){
      }


      /**
      *  @public
      *
      *  Filters a rewritten url for the front controller. Apply action definitions to the front
      *  controller to be executed.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (Renamed to "filter()")<br />
      *  Version 0.3, 17.06.2007 (Added stripslashes and htmlentities filter)<br />
      *  Version 0.4, 08.09.2007 (Now, the existance of the action keyword indicates, that an action is included. Before, only the action keyword in combination with the action delimiter was used as an action indicator)<br />
      *  Version 0.5, 29.09.2007 (Now, $_REQUEST['query'] is cleared)<br />
      *  Version 0.6, 13.12.2008 (Removed the benchmarker)<br />
      */
      function filter(){

         // get the front controller and initialize the action keyword
         $fC = &Singleton::getInstance('Frontcontroller');
         $this->__FrontcontrollerActionKeyword = $fC->get('NamespaceKeywordDelimiter').$fC->get('ActionKeyword');

         // extracte the PHPSESSID from $_REQUEST if existent
         $PHPSESSID = (string)'';
         $SessionName = ini_get('session.name');

         if(isset($_REQUEST[$SessionName])){
            $PHPSESSID = $_REQUEST[$SessionName];
          // end if
         }

         // delete the rewite param indicator
         unset($_REQUEST['query']);

         // Request-URI in Array extrahieren
         //
         // BETA (08.09.2007): Es wird nun mit
         //   substr_count($_SERVER['REQUEST_URI'],$this->__FrontcontrollerActionKeyword.'/') > 0
         // auch auf das vorkommen eines ActionKeywords geprüft - ohne Delimiter. Bei Verwendung des
         // frontcontrollerLinkHandlers ist das zwar nicht notwendig, bei manuellem Erstellen des
         // FC-Links schon. Sollte es Probleme damit geben wird das Verhalten im folgenden Release
         // wieder entfernt.
         if(substr_count($_SERVER['REQUEST_URI'],$this->__ActionDelimiter) > 0 || substr_count($_SERVER['REQUEST_URI'],$this->__FrontcontrollerActionKeyword.'/') > 0){

            // URL nach Delimiter trennen
            $RequestURLParts = explode($this->__ActionDelimiter,$_SERVER['REQUEST_URI']);

            for($i = 0; $i < count($RequestURLParts); $i++){

               // Slashed am Anfang entfernen
               $RequestURLParts[$i] = $this->__deleteTrailingSlash($RequestURLParts[$i]);

               // Frontcontroller-Action enthalten
               if(substr_count($RequestURLParts[$i],$this->__FrontcontrollerActionKeyword) > 0){

                  // String zerlegen
                  $RequestArray = explode($this->__RewriteURLDelimiter,$RequestURLParts[$i]);

                  if(isset($RequestArray[1])){

                     // Action-Parameter erzeugen
                     $ActionNamespace = str_replace($this->__FrontcontrollerActionKeyword,'',$RequestArray[0]);
                     $ActionName = $RequestArray[1];
                     $ActionParams = array_slice($RequestArray,2);

                     // Action-Parameter-Array erzeugen
                     $ActionParamsArray = array();

                     if(count($ActionParams) > 0){

                        $x = 0;

                        while($x <= (count($ActionParams) - 1)){

                           if(isset($ActionParams[$x + 1])){
                              $ActionParamsArray[$ActionParams[$x]] = $ActionParams[$x + 1];
                            // end if
                           }

                           // Offset-Zähler um 2 erhöhen
                           $x = $x + 2;

                         // end while
                        }

                      // end if
                     }

                     // Action zum Frontcontroller hinzufügen
                     $fC->addAction($ActionNamespace,$ActionName,$ActionParamsArray);

                   // end if
                  }

                // end if
               }
               else{

                  $ParamArray = $this->__createRequestArray($RequestURLParts[$i]);
                  $_REQUEST = array_merge($_REQUEST,$ParamArray);

                // end else
               }

             // end for
            }

          // end if
         }
         else{

            // Standard-Rewrite wie PageController URL-Rewriting
            $ParamArray = $this->__createRequestArray($_SERVER['REQUEST_URI']);
            $_REQUEST = array_merge($_REQUEST,$ParamArray);

          // end if
         }

         // readd POST params
         $_REQUEST = array_merge($_REQUEST,$_POST);

         // add PHPSESSID to the request again
         if(!empty($PHPSESSID)){
            $_REQUEST[$SessionName] = $PHPSESSID;
          // end if
         }

         // filter request array
         $this->__filterRequestArray();

       // end function
      }

    // end class
   }
?>