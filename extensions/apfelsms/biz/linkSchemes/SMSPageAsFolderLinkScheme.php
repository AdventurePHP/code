<?php
namespace APF\extensions\apfelsms\biz\linkSchemes;

use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 *
 * @package APF\APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (11.08.12)
 *             v0.2 (18.08.12) Fully refactored the inclusion of the old path
 *             v0.3 (23.08.12) Bug removed: pageParam "folder" was included in path and therefore contained in returned url multiple times
 */
class SMSPageAsFolderLinkScheme extends DefaultLinkScheme {


   protected static $pageParamName = 'page';


   public function formatLink(Url $url) {

      $page = $url->getQueryParameter(self::$pageParamName);

      if (!empty($page)) {

         $oldPath = $url->getPath();

         ////
         // get path without filename

         if (substr($oldPath, -1, 1) == '/') { // path don't contains filename
            $path = $oldPath;
         } else if ($lastDelimiterPos = strrpos($oldPath, '/')) { // any path (with folder) given
            $path = substr($oldPath, 0, $lastDelimiterPos + 1); // omit filename, include last delimiter
         } else {
            $path = '/';
         }

         ////
         // omit page param "folder" at last position

         $paramLength = strlen(self::$pageParamName . '/');
         if (substr($path, -1 * $paramLength) == self::$pageParamName . '/') {
            $pathLength = strlen($path);
            $path = substr($path, 0, $pathLength - $paramLength);
         }


         // put page parameter at end of path
         $path .= self::$pageParamName . '/' . $page;

         $url->setPath($path);
         $url->setQueryParameter(self::$pageParamName, null);

      }

      return parent::formatLink($url);
   }


}

