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

   import('core::filter','abstractRequestFilter');
   import('core::frontcontroller','Frontcontroller');


   /**
   *  @namespace core::request
   *  @class frontcontrollerRequestFilter
   *
   *  Implementiert den Request-URL-Filter für den Frontcontroller.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   */
   class frontcontrollerRequestFilter extends abstractRequestFilter
   {

      /**
      *  @private
      *  Action-Keyword.
      */
      var $__FrontcontrollerActionKeyword;


      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function frontcontrollerRequestFilter(){
         $fC = &Singleton::getInstance('Frontcontroller');
         $this->__FrontcontrollerActionKeyword = $fC->get('NamespaceKeywordDelimiter').$fC->get('ActionKeyword');
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Filter-Funktion aus "abstractFilter".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (In "filter()" umbenannt)<br />
      *  Version 0.3, 17.06.2007 (Stripslashes- und Htmlentities-Filter hinzugefügt)<br />
      *  Version 0.4, 09.10.2008 (Fixed bug, that an action call without params leads to an error)<br />
      */
      function filter(){

         // Timer einbinden
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('frontcontrollerRequestFilter::filter()');

         // Instanz des Frontcontrollers holen
         $fC = &Singleton::getInstance('Frontcontroller');

         // ConfigurationManager laden
         $CfgMgr = &Singleton::getInstance('configurationManager');

         // REQUEST-Array durchsuchen
         foreach($_REQUEST as $Key => $Value){

            $T->start('searchForActionKeyword("'.$Key.'")');

            if(substr_count($Key,$fC->get('NamespaceKeywordDelimiter').$fC->get('ActionKeyword').$fC->get('KeywordClassDelimiter')) > 0){

               // Namespace und Klasse aus REQUEST-Key auslesen
               $T->start('getActionNameAndNamespace()');
               $ActionName = substr($Key,strpos($Key,$fC->get('KeywordClassDelimiter')) + strlen($fC->get('KeywordClassDelimiter')));
               $ActionNamespace = substr($Key,0,strpos($Key,$fC->get('NamespaceKeywordDelimiter')));
               $T->stop('getActionNameAndNamespace()');

               // initialize the input params
               $InputParams = array();

               // create param array
               $ParamsArray = explode($fC->get('InputDelimiter'),$Value);

               for($i = 0; $i < count($ParamsArray); $i++){

                  $TmpAry = explode($fC->get('KeyValueDelimiter'),$ParamsArray[$i]);

                  if(isset($TmpAry[0]) && isset($TmpAry[1]) && !empty($TmpAry[0]) && !empty($TmpAry[1])){
                     $InputParams[$TmpAry[0]] = $TmpAry[1];
                   // end if
                  }

                // end foreach
               }


               // Action hinzufügen
               $fC->addAction($ActionNamespace,$ActionName,$InputParams);

               //echo printObject($InputParams);

             // end if
            }

            $T->stop('searchForActionKeyword("'.$Key.'")');

          // end foreach
         }

         // Request-Array filtern
         $this->__filterRequestArray();

         // Timer stoppen
         $T->stop('frontcontrollerRequestFilter::filter()');

       // end function
      }

    // end class
   }
?>