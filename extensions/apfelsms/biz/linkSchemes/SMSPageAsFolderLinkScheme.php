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
namespace APF\extensions\apfelsms\biz\linkSchemes;

use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
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
         } else {
            if ($lastDelimiterPos = strrpos($oldPath, '/')) { // any path (with folder) given
               $path = substr($oldPath, 0, $lastDelimiterPos + 1); // omit filename, include last delimiter
            } else {
               $path = '/';
            }
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

