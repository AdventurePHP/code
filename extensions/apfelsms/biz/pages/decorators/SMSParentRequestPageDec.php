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
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\tools\link\Url;

/**
 * Adds request params and uses template name of parent page
 * This is a combination of the request decorator and the alias decorator with reference to the parent
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (21.06.12)
 */
class SMSParentRequestPageDec extends SMSRequestPageDec {


   /**
    * @return SMSPage
    */
   protected function getOuterParent() {


      return $this->getOuterPage()->getParent();
   }


   /**
    * @param Url $url
    *
    * @return string
    */
   public function getLink(Url $url) {


      $url->mergeQuery($this->getRequestParams());

      $parent = $this->getOuterParent();

      return $parent->getLink($url);

   }


   /**
    * @return string
    */
   public function getTemplateName() {


      $parent = $this->getOuterParent();

      return $parent->getTemplateName();

   }


   /**
    * @return string
    */
   public function getTitle() {


      $title = $this->SMSPage->getTitle();

      if (empty($title)) {
         return $this->getOuterParent()->getTitle();
      }

      return $title;
   }


   /**
    * @return string
    */
   public function getNavTitle() {


      $navTitle = $this->SMSPage->getTitle();

      if (empty($navTitle)) {
         return $this->getOuterParent()->getNavTitle();
      }

      return $navTitle;
   }


   /**
    * @return bool
    */
   public function isAccessProtected() {


      return $this->getOuterParent()->isAccessProtected();
   }


   /**
    * @return bool
    */
   public function isReference() {


      return true;
   }

}
