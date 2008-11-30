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

   import('tools::variablen','variablenHandler');


   /**
   *  @namespace tools::html::taglib::doc
   *  @class doc_taglib_createobject
   *
   *  Implementiert die TagLib f�r den Tag "doc:createobject".<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 04.01.2006<br />
   *  Version 0.2, 29.09.2007 (TagLib in doc_taglib_createobject umbenannt)<br />
   */
   class doc_taglib_createobject extends Document
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Ruft den Konstruktor der Eltern-Klasse auf.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 29.09.2007 (Methode in doc_taglib_createobject umbenannt)<br />
      */
      function doc_taglib_createobject(){
         parent::Document();
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode onParseTime().<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function onParseTime(){

         // Attribute auslesen
         $RequestParameter = $this->__Attributes['requestparam'];
         $DefaultValue = $this->__Attributes['defaultvalue'];

         // Parameter �ber variablenHandler initialisieren
         $_LOCALS = variablenHandler::registerLocal(array($RequestParameter => $DefaultValue));

         // Aktuellen Parameter auslesen
         $CurrentRequestParameter = $_LOCALS[$RequestParameter];

         // Content des Objekts setzen
         $this->__Content = $this->__getContent($CurrentRequestParameter);

         // Tags extrahieren
         $this->__extractTagLibTags();

         // DocumentController extrahieren
         $this->__extractDocumentController();

       // end function
      }


      /**
      *  @private
      *
      *  Liest den Inhalt einer Seite aus der zugeh�rigen Datei aus. Im Fehler-<br />
      *  Fall wird eine 404-Seite angezeigt.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 30.05.2006<br />
      *  Version 0.2, 31.05.2006 (Content aus Namespace /apps/sites in Webseiten-Repository (frontend/content) verschoben)<br />
      *  Version 0.3, 29.09.2007 (Sprachabh�ngige Seiten eingef�hrt)<br />
      */
      function __getContent($Seite){

         $Datei = './frontend/content/c_'.$this->__Language.'_'.strtolower($Seite).'.html';

         if(!file_exists($Datei)){
            $Datei = './frontend/content/c_'.$this->__Language.'_404.html';
          // end else
         }

         return file_get_contents($Datei);

       // end function
      }

    // end class
   }
?>