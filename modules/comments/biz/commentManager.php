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

   import('modules::comments::data','commentMapper');
   import('modules::pager::biz','pagerManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('tools::string','stringAssistant');
   import('core::session','sessionManager');


   /**
   *  @namespace modules::comments::biz
   *  @class commentManager
   *
   *  Implementiert die Business-Schicht des Comments-Moduls.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.08.2007<br />
   *  Version 0.2, 28.12.2007 (Captcha-Unterstützung eingeführt)<br />
   */
   class commentManager extends coreObject
   {

      /**
      *  @protected
      *  Schlüssel für die auszuliefernde Kategorie.
      */
      protected $__CategoryKey;


      /**
      *  @protected
      *  Captcha String zur Prüfung der Eingabe.
      */
      protected $__CaptchaString = null;


      function commentManager(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakte "init()"-Methode.<br />
      *
      *  @param string $CategoryKey Kategorie-Schlüssel
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 28.12.2007 (Captcha-Unterstützung hinzugefügt)<br />
      */
      function init($CategoryKey){

         // Kathegorie-Schlüssel initialisieren
         $this->__CategoryKey = $CategoryKey;

         // Captcha String initialisieren
         if($this->__CaptchaString == null){

            // sessionManager erzeugen
            $sessMgr = new sessionManager('modules::comment');

            // Aktuellen String im Manager merken
            $this->__CaptchaString = $sessMgr->loadSessionData('CAPTCHA_STRING');

            // Captcha String generieren und in Session speichern
            $sessMgr->saveSessionData('CAPTCHA_STRING',stringAssistant::generateCaptchaString(5));

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Läd eine Liste von Kommentaren.<br />
      *
      *  @return Array $Entries Liste von ArticleComment-Objekten
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 01.09.2007 (Verwendung des PagerManagers auf loadEntriesByAppDataComponent() umgestellt)<br />
      */
      function loadEntries(){

         // pagerManager holen
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments');

         // Kommentare laden
         $M = &$this->__getServiceObject('modules::comments::data','commentMapper');
         return $pM->loadEntriesByAppDataComponent($M,'loadArticleCommentByID',array('CategoryKey' => $this->__CategoryKey));

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die HTML-Ausgabe des Pagers zurück.<br />
      *
      *  @param string $anchorName the desired anchor name (optional)
      *  @return string $pagerOutput the HTML code of the pager
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 29.08.2007 (Added the anchor name)<br />
      *  Version 0.3, 24.01.2009 (Introduced the $anchorName parameter)<br />
      */
      function getPager($anchorName = null){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments');
         $pM->setAnchorName($anchorName);
         return $pM->getPager(array('CategoryKey' => $this->__CategoryKey));
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die URL-Parameter des Pagers zurück.<br />
      *
      *  @return array $URLParameter Pager-URL-Parameter
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      */
      function getURLParameter(){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments');
         return $pM->getPagerURLParameters();
       // end function
      }


      /**
      *  @public
      *
      *  Speichert einen Kommentar-Eintrag.<br />
      *
      *  @param ArticleComment $ArticleComment ArticleComment-Objekt
      *  @param bool $AJAX Indiziert, ob die Methode im AJAX-Style verwendet wird
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 28.12.2007 (Captcha eingeführt)<br />
      *  Version 0.3, 02.02.2008 (AJAX-Support hinzugefügt)<br />
      */
      function saveEntry($ArticleComment,$AJAX = false){

         // Mapper holen
         $M = &$this->__getServiceObject('modules::comments::data','commentMapper');

         // Artikel speichern
         $ArticleComment->set('CategoryKey',$this->__CategoryKey);
         $M->saveArticleComment($ArticleComment);

         // Weiterleitung auf anderen View nur bei normaler Anwendung
         if($AJAX == false){

            // Captcha-Session-Eintrag löschen
            $sessMgr = new sessionManager('modules::comment');
            $sessMgr->deleteSessionData('CAPTCHA_STRING');

            // Auf die Ausgabe weiterleiten
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('coview' => 'listing'));
            header('Location: '.$Link.'#comments');

          // end if
         }

       // end function
      }

    // end class
   }
?>