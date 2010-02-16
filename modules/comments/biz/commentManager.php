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
   import('tools::link','FrontcontrollerLinkHandler');
   import('tools::string','stringAssistant');
   import('core::session','SessionManager');

   /**
   *  @package modules::comments::biz
   *  @class commentManager
   *
   *  Implementiert die Business-Schicht des Comments-Moduls.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.08.2007<br />
   *  Version 0.2, 28.12.2007 (Captcha-Unterst�tzung eingef�hrt)<br />
   */
   class commentManager extends APFObject {

      /**
      *  @protected
      *  Schl�ssel f�r die auszuliefernde Kategorie.
      */
      protected $__CategoryKey;

      /**
      *  @protected
      *  Captcha String zur Pr�fung der Eingabe.
      */
      protected $__CaptchaString = null;

      function commentManager(){
      }

      /**
      *  @public
      *
      *  Implementierung der abstrakte "init()"-Methode.<br />
      *
      *  @param string $initParam Kategorie-Schl�ssel
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 28.12.2007 (Captcha-Unterst�tzung hinzugef�gt)<br />
      */
      function init($initParam){

         $this->__CategoryKey = $initParam;

         // Captcha String initialisieren
         if($this->__CaptchaString == null){
            $sessMgr = new SessionManager('modules::comment');
            $this->__CaptchaString = $sessMgr->loadSessionData('CAPTCHA_STRING');
            $sessMgr->saveSessionData('CAPTCHA_STRING',stringAssistant::generateCaptchaString(5));
          // end if
         }

       // end function
      }

      /**
      *  @public
      *
      *  L�d eine Liste von Kommentaren.<br />
      *
      *  @return Array $Entries Liste von ArticleComment-Objekten
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 01.09.2007 (Verwendung des PagerManagers auf loadEntriesByAppDataComponent() umgestellt)<br />
      */
      function loadEntries(){

         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('ArticleComments');

         $M = &$this->__getServiceObject('modules::comments::data','commentMapper');
         return $pM->loadEntriesByAppDataComponent($M,'loadArticleCommentByID',array('CategoryKey' => $this->__CategoryKey));

       // end function
      }

      /**
      *  @public
      *
      *  Gibt die HTML-Ausgabe des Pagers zur�ck.<br />
      *
      *  @param string $anchorName the desired anchor name (optional)
      *  @return string $pagerOutput the HTML code of the pager
      *
      *  @author Christian Sch�fer
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
      *  Gibt die URL-Parameter des Pagers zur�ck.<br />
      *
      *  @return array $URLParameter Pager-URL-Parameter
      *
      *  @author Christian Sch�fer
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
      *  @param ArticleComment $articleComment ArticleComment-Objekt
      *  @param bool $ajax Indiziert, ob die Methode im AJAX-Style verwendet wird
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      *  Version 0.2, 28.12.2007 (Captcha eingef�hrt)<br />
      *  Version 0.3, 02.02.2008 (AJAX-Support hinzugef�gt)<br />
      */
      function saveEntry($articleComment,$ajax = false){

         $M = &$this->__getServiceObject('modules::comments::data','commentMapper');

         $articleComment->set('CategoryKey',$this->__CategoryKey);
         $M->saveArticleComment($articleComment);

         // redirect to further view, if not in AJAX mode
         if($ajax == false){

            // delete captcha session entry
            $sessMgr = new SessionManager('modules::comment');
            $sessMgr->deleteSessionData('CAPTCHA_STRING');

            $Link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('coview' => 'listing'));
            header('Location: '.$Link.'#comments');

          // end if
         }

       // end function
      }

    // end class
   }
?>