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
   *  @namespace tools::html::taglib::documentcontroller
   *  @class iteratorBaseController
   *
   *  Implementiert den Basis-DocumentController für die Verwendung des Iterator-Tags. Konkrete<br/>
   *  DocumentController müssen von diesem Controller erben.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.06.2008<br />
   */
   class iteratorBaseController extends baseController
   {

      function iteratorBaseController(){
      }


      /**
      *  @private
      *
      *  Gibt die Referenz auf ein Iterator-Objekt zurück.<br />
      *
      *  @param string $Name; Name des Iterators.
      *  @return html_taglib_iterator $Iterator; Referenz auf den Iterator
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.06.2008<br />
      */
      function &__getIterator($Name){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'html_taglib_iterator';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('['.get_class($this).'::__getIteratorTemplate()] TagLib module "'.$TagLibModule.'" is not loaded!',E_USER_ERROR);
          // end if
         }


         // Prüfen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Templates aus dem aktuellen Document bereitstellen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s als Referenz zurückgeben
               if(get_class($Child) == $TagLibModule){

                  // Prüfen, ob das gefundene Template $Name heißt.
                  if($Child->getAttribute('name') == $Name){
                     return $this->__Document->__Children[$ObjectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('['.get_class($this).'::__getIteratorTemplate()] No iterator object with name "'.$Name.'" composed in current document for document controller "'.get_class($this).'"! Perhaps tag library html:iterator is not loaded in current template!',E_USER_ERROR);
            exit();

          // end else
         }


         // Falls das Template nicht gefunden werden kann -> Fehler!
         trigger_error('['.get_class($this).'::__getIteratorTemplate()] Iterator with name "'.$Name.'" cannot be found!',E_USER_ERROR);
         exit();

       // end function
      }

    // end class
   }
?>