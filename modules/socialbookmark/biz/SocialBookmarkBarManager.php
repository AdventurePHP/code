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
namespace APF\modules\socialbookmark\biz;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;
use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\media\taglib\MediaInclusionTag;

/**
 * @package APF\modules\socialbookmark\biz
 * @class SocialBookmarkBarManager
 *
 * Generates the HTML code of the bookmark bar. In order to add new services you can use the <em><addBookmarkService()/em>
 * method. This class must be instantiated via the ServiceManager.
 * <p/>
 * Expects a configuration file with name <em>{ENVIRONMENT}_bookmarkservices.ini</em> being present under the namespace
 * <em>APF\modules\socialbookmark</em> with one configuration section per bookmark service. E.g.:
 * <code>
 * [del.icio.us]
 * BookmarkService.BaseURL = "http://del.icio.us/post"
 * BookmarkService.Param.URL = "url"
 * BookmarkService.Param.Title = "title"
 * BookmarkService.Display.Title = "Bookmark &#64; del.icio.us"
 * BookmarkService.Display.Image = "bookmark_del_icio_us"
 * BookmarkService.Display.ImageExt = "png"
 * </code>
 * Besides, this component uses the <em>&lt;*mediastream /&gt;</em> tag that requires a front controller action to be
 * configured for the display of images. Please refer to the documentation for details on this.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.06.2007<br />
 * Version 0.2, 07.09.2007<br />
 */
class SocialBookmarkBarManager extends APFObject {

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
    * @var SocialBookmarkItem[] The configured bookmark services.
    */
   private $bookmarkServices = array();

   /**
    * @public
    *
    * Let's you add a bookmark service.
    *
    * @param SocialBookmarkItem $service The bookmark entry to add.
    *
    * @author Christian W. Schäfer
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
    * Generates the bookmark bar HTML code.
    *
    * @return string HTML code of the configured bookmarks.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 02.06.2007<br />
    * Version 0.2, 07.09.2007<br />
    * Version 0.3, 08.09.2007 (Added profiling)<br />
    */
   public function getBookmarkCode() {

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $id = 'SocialBookmarkBarManager::getBookmarkCode()';
      $t->start($id);

      // generate the current page's url, if no url was set
      if (empty($this->url)) {
         $this->url = LinkGenerator::generateUrl(Url::fromCurrent(true));
      }

      // get services from config file
      $services = $this->getConfiguration('APF\modules\socialbookmark', 'bookmarkservices.ini');

      foreach ($services->getSectionNames() as $serviceName) {

         $service = $services->getSection($serviceName);
         $this->bookmarkServices[] =
               new SocialBookmarkItem(
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
    * Generates the HTML code of a single bookmark entry.
    *
    * @param SocialBookmarkItem $bookmarkEntry Bookmark item.
    * @return string HTML code of the applied bookmark entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.06.2007<br />
    * Version 0.2, 07.09.2007<br />
    * Version 0.3, 08.09.2007 (Added profiling)<br />
    * Version 0.4, 15.04.2008 (Now URL rewriting is respected)<br />
    * Version 0.5, 25.05.2008 (Page title is now applied)<br />
    * Version 0.6, 21.06.2008 (Replaced APPS__URL_REWRITING with a value from the registry)<br />
    */
   protected function generateBookmarkEntry(SocialBookmarkItem $bookmarkEntry) {

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
      $media = new MediaInclusionTag();
      $media->setAttribute('namespace', 'APF\modules\socialbookmark\pres\image');
      $media->setAttribute('filename', $image);
      $media->setContent($this->getContext());
      $media->setLanguage($this->getLanguage());
      $media->onParseTime();
      return $media->transform();
   }

}
