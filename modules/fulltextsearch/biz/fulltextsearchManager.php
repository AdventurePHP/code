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

   import('modules::pager::biz','pagerManager');
   import('modules::fulltextsearch::biz','searchResult');
   import('modules::fulltextsearch::data','fulltextsearchMapper');
   import('core::logging','Logger');


   /**
   *  @namespace modules::fulltextsearch::biz
   *  @module fulltextsearchManager
   *
   *  Implementiert die Business-Schicht für die Volltextsuche.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 10.03.2008<br />
   */
   class fulltextsearchManager extends coreObject
   {

      function fulltextsearchManager(){
      }


      /**
      *  @module loadSearchResult()
      *  @public
      *
      *  Läd Ergebnis-Objekte gemäß einem Suchwort.<br />
      *
      *  @param string $SearchString; Suchwort, oder mehrere Wörter per Space getrennt
      *  @return array $SearchResults; Liste von Such-Ergebnis-Objekten
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.03.2008<br />
      */
      function loadSearchResult($SearchString){

         // Mapper laden
         $M = &$this->__getServiceObject('modules::fulltextsearch::data','fulltextsearchMapper');

         // Suchwort protokollieren
         $L = &Singleton::getInstance('Logger');
         $L->logEntry('searchlog','SearchString: "'.$SearchString.'"','LOG');

         // Ergebnisse laden
         return $M->loadSearchResult($SearchString);

       // end function
      }


      /**
      *  @module loadPages()
      *  @public
      *
      *  Läd eine Liste der in der Seite vorhandenen Seiten.<br />
      *
      *  @return array $Pages; Liste von Seiten-Objekten
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.03.2008<br />
      */
      function loadPages(){

         // Mapper laden
         $M = &$this->__getServiceObject('modules::fulltextsearch::data','fulltextsearchMapper');

         // Suchwort protokollieren
         $L = &Singleton::getInstance('Logger');
         $L->logEntry('sitemap','Sitemap in language '.$this->__Language.' displayed!','LOG');

         // Ergebnisse laden
         return $M->loadPages($this->__Language);

       // end function
      }


      /**
      *  @module getPageTags()
      *  @public
      *
      *  Läd eine Liste der in der Seite vorhandenen Tags.<br />
      *
      *  @return array $Tags; Liste von Tags
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.06.2008<br />
      */
      function getPageTags(){

         // Model beziehen
         $Model = &$this->__getServiceObject('sites::demosite::biz','DemositeModel');

         // Model-Werte auslesen
         $ReqParamName = $Model->getAttribute('ReqParamName');
         $DefaultPageName = $Model->getAttribute('DefaultPageName');
         $RequestParameter = $ReqParamName[$this->__Language];
         $DefaultValue = $DefaultPageName[$this->__Language];
         $_LOCALS = variablenHandler::registerLocal(array($RequestParameter,$DefaultValue));

         // Mapper laden
         $M = &$this->__getServiceObject('modules::fulltextsearch::data','fulltextsearchMapper');

         // Ergebnisse laden
         return $M->getPageTags($_LOCALS[$RequestParameter]);

       // end function
      }

    // end class
   }
?>