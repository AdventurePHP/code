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
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\sites\SMSSite;
use APF\tools\string\StringAssistant;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (08.08.12)
 *
 */
class SMSTitleTag extends Document {


   /**
    * @var string $titleTemplate
    */
   protected static $titleTemplate = '<title>{PAGETITLE} - {SITETITLE}</title>';


   /**
    * @return string
    */
   public function transform() {


      /** @var $site SMSSite */
      $site = $this->getDIServiceObject('APF\extensions\apfelsms', 'Site');

      $siteTitle = StringAssistant::escapeSpecialCharacters($site->getWebsiteTitle());

      $currentPage = $site->getCurrentPage();

      if ($currentPage === null) {
         $pageTitle = $siteTitle;
      } else {
         /** @var $currentPage SMSPage */
         $pageTitle = $currentPage->getNavTitle();
      }

      $pageTitle = StringAssistant::escapeSpecialCharacters($pageTitle);

      return str_replace(
            ['{PAGETITLE}', '{SITETITLE}'],
            [$pageTitle, $siteTitle],
            self::$titleTemplate
      );

   }

}
