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
import('tools::link', 'LinkGenerator');
import('modules::socialbookmark::biz', 'bookmarkEntry');
import('tools::media::taglib', 'ui_mediastream');

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
    * @var string Url of the page to bookmark.
    */
   private $url = '';

   /**
    * @var string Title of the page to bookmark.
    */
   private $title = '';

   /**
    * @var string Width of the bookmark icons.
    */
   private $imageWidth = '20';

   /**
    * @var string Height of the bookmark icons.
    */
   private $imageHeight = '20';

   /**
    * @var bookmarkEntry[] The configured bookmark services.
    */
   private $bookmarkServices = array();

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
   public function addBookmarkService($service) {
      $this->bookmarkServices[] = $service;
   }

   public function setUrl($url) {
      $this->url = $url;
   }

   public function setTitle($title) {
      $this->title = $title;
   }

   public function setImageWidth($imageWidth) {
      $this->imageWidth = $imageWidth;
   }

   public function setImageHeight($imageHeight) {
      $this->imageHeight = $imageHeight;
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
   public function getBookmarkCode() {

      $t = &Singleton::getInstance('BenchmarkTimer');
      $id = 'socialBookmarkManager::getBookmarkCode()';
      $t->start($id);

      // generate the current's page url, if no url was set
      if (empty($this->url)) {
         $this->url = Registry::retrieve('apf::core', 'CurrentRequestURL');
      }

      // get services from config file
      $services = $this->getConfiguration('modules::socialbookmark', 'bookmarkservices');

      foreach ($services->getSectionNames() as $serviceName) {

         $service = $services->getSection($serviceName);
         $this->bookmarkServices[] =
               new bookmarkEntry(
                  $service->getValue('BookmarkService.BaseURL'),
                  $service->getValue('BookmarkService.Param.URL'),
                  $service->getValue('BookmarkService.Param.Title'),
                  $service->getValue('BookmarkService.Display.Title'),
                  $service->getValue('BookmarkService.Display.Image'),
                  $service->getValue('BookmarkService.Display.ImageExt')
               );
      }

      $output = (string)'';

      for ($i = 0; $i < count($this->bookmarkServices); $i++) {
         $output .= $this->generateBookmarkEntry($this->bookmarkServices[$i]);
         $output .= PHP_EOL;
      }

      $t->stop($id);
      return $output;
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
   protected function generateBookmarkEntry(bookmarkEntry $bookmarkEntry) {

      $code = '<a rel="nofollow" href="';
      $code .= LinkGenerator::generateUrl(
         Url::fromString($bookmarkEntry->getServiceBaseUrl())->mergeQuery(array(
            $bookmarkEntry->getUrlParamName() => $this->url,
            $bookmarkEntry->getTitleParamName() => $this->title
         )), new DefaultLinkScheme(true));
      $code .= '" title="';
      $code .= $bookmarkEntry->getTitle();
      $code .= '" linkrewrite="false"><img src="';
      $code .= $this->getMediaUrl($bookmarkEntry->getImageUrl() . '.' . $bookmarkEntry->getImageExt());
      $code .= '" alt="';
      $code .= $bookmarkEntry->getTitle();
      $code .= '" style="width: ';
      $code .= $this->imageWidth . 'px; height: ';
      $code .= $this->imageHeight . 'px;" /></a>' . PHP_EOL;

      return $code;
   }

   private function getMediaUrl($image) {
      $media = new ui_mediastream();
      $media->setAttribute('namespace', 'modules::socialbookmark::pres::image');
      $media->setAttribute('filename', $image);
      $media->setContent($this->getContext());
      $media->setLanguage($this->getLanguage());
      $media->onParseTime();
      return $media->transform();
   }

}
