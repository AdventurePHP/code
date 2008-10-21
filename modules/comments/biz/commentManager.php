<?php
   import('modules::pager::biz','pagerManager');
   import('modules::comments::data','commentMapper');
   import('modules::pager::biz','pagerManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('tools::string','stringAssistant');
   import('core::session','sessionManager');


   /**
   *  @package modules::comments::biz
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
      *  @private
      *  Schlüssel für die auszuliefernde Kategorie.
      */
      var $__CategoryKey;


      /**
      *  @private
      *  Captcha String zur Prüfung der Eingabe.
      */
      var $__CaptchaString = null;


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
         $pMF = &$this->__getServiceObject('modules::pager::biz','pagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments',array('CategoryKey' => $this->__CategoryKey));

         // Kommentare laden
         $M = &$this->__getServiceObject('modules::comments::data','commentMapper');
         return $pM->loadEntriesByAppDataComponent($M,'loadArticleCommentByID');

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die HTML-Ausgabe des Pagers zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 29.08.2007 (Anker-Name mit eingebunden)<br />
      */
      function getPager($AnchorName = ''){

         // pagerManager holen
         $pMF = &$this->__getServiceObject('modules::pager::biz','pagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments',array('CategoryKey' => $this->__CategoryKey));

         // Anker setzen
         if($AnchorName != ''){
            $pM->setAnchorName($AnchorName);
          // end if
         }

         // Pager-Ausgabe hinzufügen
         return $pM->getPager();

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

         // pagerManager holen
         $pMF = &$this->__getServiceObject('modules::pager::biz','pagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments',array('CategoryKey' => $this->__CategoryKey));

         // Pager-Ausgabe hinzufügen
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