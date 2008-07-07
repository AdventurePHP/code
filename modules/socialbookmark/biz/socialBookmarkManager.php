<?php
   import('tools::link','frontcontrollerLinkHandler');
   import('tools::link','linkHandler');
   import('modules::socialbookmark::biz','bookmarkEntry');


   /**
   *  @package modules::socialbookmark::biz
   *  @class socialBookmarkManager
   *
   *  Generiert einen Bookmark-HTML-Code.<br />
   *  Um die angezeigten Services erweitern zu können die Methode addBookmarkService()<br />
   *  verwendet werden. Muss über den ServiceManager instanziiert werden.<br />
   *  <br />
   *  Erwartet eine Konfiguration mit dem Namen "{ENVIRONMENT}_bookmarkservices.ini" unter<br />
   *  dem Namespace "/config/modules/socialbookmark/{Context}/" mit jeweils einer Sektion für einen<br />
   *  Bookmarkservice. Diese muss wie folgt aufgebaut sein (Beispiel für del.icio.us):<br />
   *  <br />
   *  [del.icio.us]<br />
   *  BookmarkService.BaseURL = "http://del.icio.us/post"<br />
   *  BookmarkService.Param.URL = "url"<br />
   *  BookmarkService.Param.Title = "title"<br />
   *  BookmarkService.Display.Title = "Bookmark &#64; del.icio.us"<br />
   *  BookmarkService.Display.Image = "bookmark_del_icio_us"<br />
   *  BookmarkService.Display.ImageExt = "png"<br />
   *  <br />
   *  Darüber hinaus muss für die FrontController-basierte Ausgabe der Bilder eine Action-Konfiguration<br />
   *  unter "/config/modules/socialbookmark/actions/{Context}/" angelegt sein und die Action für das<br />
   *  Anzeigen der Bilder definieren. Diese muss folgende Werte haben:<br />
   *  <br />
   *  [showImage]<br />
   *  FC.ActionNamespace = "modules::socialbookmark::biz::actions"<br />
   *  FC.ActionFile = "ShowImageAction"<br />
   *  FC.ActionClass = "ShowImageAction"<br />
   *  FC.InputFile = "ShowImageInput"<br />
   *  FC.InputClass = "ShowImageInput"<br />
   *  FC.InputParams = "img:bookmark_del_icio_us|imgext:png"<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.06.2007<br />
   *  Version 0.2, 07.09.2007<br />
   */
   class socialBookmarkManager extends coreObject
   {


      /**
      *  @private
      *  Linkziel der Bookmark-Einträge.
      */
      var $__Target = '_blank';


      /**
      *  @private
      *  URL der Seite, die gebookmarkt werden soll.
      */
      var $__URL = '';


      /**
      *  @private
      *  Titel der Seite, die gebookmarkt werden soll.
      */
      var $__Title = '';


      /**
      *  @private
      *  Breite des Bookmark-Icons.
      */
      var $__Width = '20';


      /**
      *  @private
      *  Höhe des Bookmark-Icons.
      */
      var $__Height = '20';


      /**
      *  @private
      *  Liste der Bookmark-Services.
      */
      var $__BookmarkServices = array();


      function socialBookmarkManager(){
      }


      /**
      *  @public
      *
      *  Fügt einen Bookmark-Service hinzu.<br />
      *
      *  @param bookmarkEntry $Service; bookmarkEntry Objekt
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 07.09.2007<br />
      */
      function addBookmarkService($Service){
         $this->__BookmarkServices[] = $Service;
       // end function
      }


      /**
      *  @public
      *
      *  Generiert einen Bookmark-HTML-Code und gibt diesen zurück.<br />
      *
      *  @return string $BookmarkCode; HTML-Code der konfigurierten Bookmarks
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 07.09.2007<br />
      *  Version 0.3, 08.09.2007 (Profiling hinzugefügt)<br />
      */
      function getBookmarkCode(){

         // Timer starten
         $T = &Singleton::getInstance('BenchmarkTimer');
         $ID = 'socialBookmarkManager::getBookmarkCode()';
         $T->start($ID);


         // Aktuelle URL erzeugen, falls nicht gegeben
         if($this->__URL == ''){
            $this->__URL = $this->__getCurrentURL();

          // end if
         }


         // Konfiguration einlesen
         $CfgObj = &$this->__getConfiguration('modules::socialbookmark','bookmarkservices');
         $Services = $CfgObj->getConfiguration();

         if($Services != null){

            foreach($Services as $Service){

               $this->__BookmarkServices[] = new bookmarkEntry(
                                                               $Service['BookmarkService.BaseURL'],
                                                               $Service['BookmarkService.Param.URL'],
                                                               $Service['BookmarkService.Param.Title'],
                                                               $Service['BookmarkService.Display.Title'],
                                                               $Service['BookmarkService.Display.Image'],
                                                               $Service['BookmarkService.Display.ImageExt']
                                                               );

             // end foreach
            }

          // end if
         }
         else{
            trigger_error('[socialBookmarkManager::getBookmarkCode()] Configuration does not contain a valid bookmark service!',E_USER_WARNING);
          // end if
         }


         // Services generieren
         $HTML = (string)'';

         for($i = 0; $i < count($this->__BookmarkServices); $i++){
            $HTML .= $this->__generateBookmarkEntry($this->__BookmarkServices[$i]);
            $HTML .= PHP_EOL;
          // end for
         }


         // Timer stoppen
         $T->stop($ID);


         // Services zurückgeben
         return $HTML;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert einen Bookmark-HTML-Code aus einem BookmarkEntry.<br />
      *
      *  @param BookmarkEntry $BookmarkEntry; BookmarkEntry-Objekt
      *  @return string $BookmarkCode; HTML-Code des Bookmarks
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 07.09.2007<br />
      *  Version 0.3, 08.09.2007 (Profiling hinzugefügt)<br />
      *  Version 0.4, 15.04.2008 (URL-Rewriting beachtet)<br />
      *  Version 0.5, 25.05.2008 (Page-Title wird nun übergeben)<br />
      *  Version 0.6, 21.06.2008 (Replaced APPS__URL_REWRITING with a value from the registry)<br />
      */
      function __generateBookmarkEntry($BookmarkEntry){

         // Timer starten
         $T = &Singleton::getInstance('BenchmarkTimer');
         $ID = 'socialBookmarkManager::__generateBookmarkEntry('.$BookmarkEntry->get('BookmarkService.Display.Title').')';
         $T->start($ID);


         // Retrieve some parameters from the registry
         $Reg = &Singleton::getInstance('Registry');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');


         // Code generieren
         $Code = (string)'';
         $Code = $Code .= '<a href="';
         $Code .=  linkHandler::generateLink($BookmarkEntry->get('ServiceBaseURL'),
                                             array(
                                                   $BookmarkEntry->get('ServiceParams_URL') => $this->__URL,
                                                   $BookmarkEntry->get('ServiceParams_Title') => $this->__Title
                                                   ),
                                             false
                                            );
         $Code .= '" target="_blank" title="';
         $Code .= $BookmarkEntry->get('Title');
         $Code .= '" linkrewrite="false"><img src="';

         if($URLRewriting == true){
            $Code .= frontcontrollerLinkHandler::generateLink($URLBasePath,array('modules_socialbookmark-action' => 'showImage/imgext/'.$BookmarkEntry->get('ImageExt').'/img/'.$BookmarkEntry->get('ImageURL')));
          // end if
         }
         else{
            $Code .= frontcontrollerLinkHandler::generateLink($URLBasePath,array('modules_socialbookmark-action:showImage' => 'imgext:'.$BookmarkEntry->get('ImageExt').'|img:'.$BookmarkEntry->get('ImageURL')));
          // end else
         }

         $Code .= '" alt="';
         $Code .= $BookmarkEntry->get('Title');
         $Code .= '" border="0" align="absmiddle" style="width: ';
         $Code .= $this->__Width.'px; height: ';
         $Code .= $this->__Height.'px;" /></a>'.PHP_EOL;


         // Timer stoppen
         $T->stop($ID);


         // Code zurückgeben
         return $Code;

       // end function
      }


      /**
      *  @private
      *
      *  Gibt die URL der aktuellen Seite zurück.<br />
      *
      *  @return string $URL; Aktuelle URL
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      */
      function __getCurrentURL(){

         // Rückgabe-Variable initialisieren
         $Link = (string)'';


         // Protokoll setzen
         if($_SERVER['REMOTE_PORT'] == '443'){
            $Link = 'https://';
          // end if
         }
         else{
            $Link = 'http://';
          // end else
         }


         // Link zusammenbauen
         $Link .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];


         // Link zurückgeben
         return $Link;

       // end function
      }

    // end class
   }
?>