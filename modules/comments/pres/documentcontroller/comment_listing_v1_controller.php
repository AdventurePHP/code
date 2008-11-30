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

   import('modules::comments::pres::documentcontroller','commentBaseController');
   import('tools::datetime','dateTimeManager');
   import('tools::string','bbCodeParser');
   import('tools::link','frontcontrollerLinkHandler');


   /**
   *  @namespace modules::comments::pres::documentcontroller
   *  @class comment_listing_v1_controller
   *
   *  Implementiert den DocumentController für ds Template 'listing.html'.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.08.2007<br />
   *  Version 0.2, 20.04.2008 (AllowedServers angepasst)<br />
   *  Version 0.3, 12.06.2008 (Anzeige-Quickhack entfernt)<br />
   */
   class comment_listing_v1_controller extends commentBaseController
   {

      function comment_listing_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent()".<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2007<br />
      *  Version 0.2, 02.09.2007 (Funktion auf anderen Hosts deaktiviert, dass bei Demopackages keine Fehler auftreten)<br />
      *  Version 0.3, 09.03.2008 (Deaktivierung wegen Indexer verändert)<br />
      *  Version 0.4, 12.06.2008 (Anzeige-Quickhack entfernt)<br />
      */
      function transformContent(){

         // Kategorie-Schlüssel laden
         $this->__loadCategoryKey();

         // Mapper holen
         $M = &$this->__getAndInitServiceObject('modules::comments::biz','commentManager',$this->__CategoryKey);

         // Einträge laden
         $Entries = $M->loadEntries();

         $Buffer = (string)'';
         $Template__ArticleComment = &$this->__getTemplate('ArticleComment');
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');

         for($i = 0; $i < count($Entries); $i++){

            // Werte setzen
            $Template__ArticleComment->setPlaceHolder('Number',$i + 1);
            $Template__ArticleComment->setPlaceHolder('Name',$Entries[$i]->get('Name'));
            $Template__ArticleComment->setPlaceHolder('Date',dateTimeManager::convertDate2Normal($Entries[$i]->get('Date')));
            $Template__ArticleComment->setPlaceHolder('Time',$Entries[$i]->get('Time'));
            $Template__ArticleComment->setPlaceHolder('Comment',$bbCP->parseText($Entries[$i]->get('Comment')));

            // Template transformieren und zum Puffer hinzufügen
            $Buffer .= $Template__ArticleComment->transformTemplate();

          // end for
         }

         // Falls keine Artikel vorliegen, Hinweis anzeigen
         if(count($Entries) < 1){
            $Template__NoEntries = &$this->__getTemplate('NoEntries');
            $Buffer = $Template__NoEntries->transformTemplate();
          // end if
         }

         // Ausgabe in Puffer setzen
         $this->setPlaceHolder('Content',$Buffer);

         // Pager einsetzen
         $this->setPlaceHolder('Pager',$M->getPager('comments'));

         // Hinzufüge-Link erstellen
         $URLParameter = $M->getURLParameter();

         // Link per frontcontrollerLinkHandler generieren
         $this->setPlaceHolder(
                               'Link',
                               frontcontrollerLinkHandler::generateLink(
                                                                        $_SERVER['REQUEST_URI'],
                                                                        array(
                                                                              $URLParameter['StartName'] => '',
                                                                              $URLParameter['CountName'] => '',
                                                                              'coview' => 'form'
                                                                        )
                               )
         );

       // end function
      }

    // end function
   }
?>