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
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\Document;
use APF\extensions\apfelsms\biz\SMSManager;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (08.08.12)
 *             v0.2 (30.10.12) Removed extension appending
 */
class SMSCSSIncludesTag extends Document {


   /**
    * HTML-Template for CSS includes
    *
    * @var string $CSSIncludeTemplate
    */
   protected static $CSSIncludeTemplate = '<link rel="stylesheet" type="text/css" media="{MEDIA}" href="{URL}" />';


   /**
    * @var string $newLine
    */
   protected static $newLine = "\n";


   /**
    * @inheritdoc
    *
    * @return string
    */
   public function transform() {


      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      $currentPage = $SMSM->getSite()->getCurrentPage();


      if ($currentPage === null) { // this is no normal operation, but ...
         return ''; // be quiet
      }

      $cssArray = $currentPage->getCSS();

      if (count($cssArray) < 1) {
         return ''; // no styles to include
      }


      $stringBuffer = '';

      foreach ($cssArray AS $media => $urlReplacer) {

         $mediaReplacer = 'all';
         if (is_string($media)) {
            $mediaReplacer = $media;
         }


         $stringBuffer .= str_replace(
               array('{MEDIA}', '{URL}'),
               array($mediaReplacer, $urlReplacer),
               self::$CSSIncludeTemplate
         );

         $stringBuffer .= self::$newLine;

      }

      return $stringBuffer;

   }

}
