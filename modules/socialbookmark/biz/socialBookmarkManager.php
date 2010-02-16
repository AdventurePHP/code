<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::link','FrontcontrollerLinkHandler');
   import('tools::link','LinkHandler');
   import('modules::socialbookmark::biz','bookmarkEntry');

   /**
    * @package modules::socialbookmark::biz
    * @class socialBookmarkManager
    *
    * Generiert einen Bookmark-HTML-Code. Um die angezeigten Services erweitern zu k�nnen die
    * Methode addBookmarkService() verwendet werden. Muss �ber den ServiceManager instanziiert
    * werden.
    * <p />
    * Erwartet eine Konfiguration mit dem Namen "{ENVIRONMENT}_bookmarkservices.ini" unter
    * dem Namespace "/config/modules/socialbookmark/{Context}/" mit jeweils einer Sektion f�r einen
    * Bookmarkservice. Diese muss wie folgt aufgebaut sein (Beispiel f�r del.icio.us):
    * <pre>
    * [del.icio.us]
    * BookmarkService.BaseURL = "http://del.icio.us/post"
    * BookmarkService.Param.URL = "url"
    * BookmarkService.Param.Title = "title"
    * BookmarkService.Display.Title = "Bookmark &#64; del.icio.us"
    * BookmarkService.Display.Image = "bookmark_del_icio_us"
    * BookmarkService.Display.ImageExt = "png"
    * </pre>
    * Dar�ber hinaus muss f�r die FrontController-basierte Ausgabe der Bilder eine
    * Action-Konfiguration unter "/config/modules/socialbookmark/actions/{Context}/" angelegt sein
    * und die Action f�r das Anzeigen der Bilder definieren. Diese muss folgende Werte haben:
    * <pre>
    * [showImage]
    * FC.ActionNamespace = "modules::socialbookmark::biz::actions"
    * FC.ActionFile = "ShowImageAction"
    * FC.ActionClass = "ShowImageAction"
    * FC.InputFile = "ShowImageInput"
    * FC.InputClass = "ShowImageInput"
    * FC.InputParams = "img:bookmark_del_icio_us|imgext:png"
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.06.2007<br />
    * Version 0.2, 07.09.2007<br />
    */
   class socialBookmarkManager extends APFObject {

      /**
       * @protected
       * @var string Linkziel der Bookmark-Eintr�ge.
       */
      protected $__Target = '_blank';

      /**
       * @protected
       * @var string URL der Seite, die gebookmarkt werden soll.
       */
      protected $__URL = '';

      /**
       * @protected
       * @var string Titel der Seite, die gebookmarkt werden soll.
       */
      protected $__Title = '';

      /**
       * @protected
       * @var string Breite des Bookmark-Icons.
       */
      protected $__Width = '20';

      /**
       * @protected
       * @var string H�he des Bookmark-Icons.
       */
      protected $__Height = '20';

      /**
       * @protected
       * @var bookmarkEntry[] Liste der Bookmark-Services.
       */
      protected $__BookmarkServices = array();

      function socialBookmarkManager(){
      }

      /**
       * @public
       *
       * F�gt einen Bookmark-Service hinzu.<br />
       *
       * @param bookmarkEntry $service The bookmark entry to add.
       *
       * @author Christian W. Sch�fer
       * @version
       * Version 0.1, 07.09.2007<br />
       */
      function addBookmarkService($service){
         $this->__BookmarkServices[] = $service;
       // end function
      }

      /**
       * @public
       *
       * Generiert einen Bookmark-HTML-Code und gibt diesen zur�ck.
       *
       * @return string HTML-Code der konfigurierten Bookmarks.
       *
       * @author Christian W. Sch�fer
       * @version
       * Version 0.1, 02.06.2007<br />
       * Version 0.2, 07.09.2007<br />
       * Version 0.3, 08.09.2007 (Profiling hinzugef�gt)<br />
       */
      function getBookmarkCode(){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $ID = 'socialBookmarkManager::getBookmarkCode()';
         $t->start($ID);

         // generate the current's page url, if no url was set
         if($this->__URL == ''){
            $reg = &Singleton::getInstance('Registry');
            $this->__URL = $reg->retrieve('apf::core','CurrentRequestURL');
          // end if
         }

         // get services from config file
         $config = &$this->__getConfiguration('modules::socialbookmark','bookmarkservices');
         $services = $config->getConfiguration();

         if($services != null){

            foreach($services as $service){

               $this->__BookmarkServices[] =
                  new bookmarkEntry(
                                    $service['BookmarkService.BaseURL'],
                                    $service['BookmarkService.Param.URL'],
                                    $service['BookmarkService.Param.Title'],
                                    $service['BookmarkService.Display.Title'],
                                    $service['BookmarkService.Display.Image'],
                                    $service['BookmarkService.Display.ImageExt']
                  );

             // end foreach
            }

          // end if
         }
         else{
            trigger_error('[socialBookmarkManager::getBookmarkCode()] Configuration does not '
               .'contain a valid bookmark service!',E_USER_WARNING);
          // end if
         }

         $output = (string)'';
         
         for($i = 0; $i < count($this->__BookmarkServices); $i++){
            $output .= $this->__generateBookmarkEntry($this->__BookmarkServices[$i]);
            $output .= PHP_EOL;
          // end for
         }

         $t->stop($ID);
         return $output;

       // end function
      }


      /**
       * @protected
       *
       * Generiert einen Bookmark-HTML-Code aus einem BookmarkEntry.
       *
       * @param BookmarkEntry $bookmarkEntry BookmarkEntry-Objekt
       * @return string HTML-Code des Bookmarks.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 02.06.2007<br />
       * Version 0.2, 07.09.2007<br />
       * Version 0.3, 08.09.2007 (Profiling hinzugef�gt)<br />
       * Version 0.4, 15.04.2008 (URL-Rewriting beachtet)<br />
       * Version 0.5, 25.05.2008 (Page-Title wird nun �bergeben)<br />
       * Version 0.6, 21.06.2008 (Replaced APPS__URL_REWRITING with a value from the registry)<br />
       */
      protected function __generateBookmarkEntry($bookmarkEntry){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $ID = 'socialBookmarkManager::__generateBookmarkEntry('.$bookmarkEntry->get('BookmarkService.Display.Title').')';
         $t->start($ID);

         // Retrieve some parameters from the registry
         $reg = &Singleton::getInstance('Registry');
         $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
         $urlBasePath = $reg->retrieve('apf::core','URLBasePath');

         $code = (string)'';
         $code = $code .= '<a rel="nofollow" href="';
         $code .=  LinkHandler::generateLink($bookmarkEntry->get('ServiceBaseURL'),
                                             array(
                                                   $bookmarkEntry->get('ServiceParams_URL') => $this->__URL,
                                                   $bookmarkEntry->get('ServiceParams_Title') => $this->__Title
                                                   ),
                                             false
                                            );
         $code .= '" title="';
         $code .= $bookmarkEntry->get('Title');
         $code .= '" linkrewrite="false"><img src="';

         if($urlRewriting == true){
            $code .= FrontcontrollerLinkHandler::generateLink($urlBasePath,array('modules_socialbookmark-action' => 'showImage/imgext/'.$bookmarkEntry->get('ImageExt').'/img/'.$bookmarkEntry->get('ImageURL')));
          // end if
         }
         else{
            $code .= FrontcontrollerLinkHandler::generateLink($urlBasePath,array('modules_socialbookmark-action:showImage' => 'imgext:'.$bookmarkEntry->get('ImageExt').'|img:'.$bookmarkEntry->get('ImageURL')));
          // end else
         }

         $code .= '" alt="';
         $code .= $bookmarkEntry->get('Title');
         $code .= '" border="0" style="width: ';
         $code .= $this->__Width.'px; height: ';
         $code .= $this->__Height.'px;" /></a>'.PHP_EOL;

         $t->stop($ID);
         return $code;

       // end function
      }

    // end class
   }
?>