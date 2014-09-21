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
use APF\extensions\apfelsms\biz\SMSManager;

/**
 * An alias page decorator uses the template name (and title/navTitle if own are not defined) of a referenced page
 * There is no impact on the link (url returned by getLink()) or parents and children
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.1 (06.06.12)
 *             v0.2 (15.03.13) fixed bug in method getNavTitle(), which returned title instead of navTitle
 */
class SMSAliasPageDec extends SMSAbstractPageDec {


   /**
    * @var SMSPage $referencedPage
    */
   protected $referencedPage;


   /**
    * @var string $referencedPageId
    */
   protected $referencedPageId;


   public static $mapVars = array(
         'referencedPageId' => null
   );


   /**
    * @return SMSPage
    */
   public function getReferencedPage() {


      if (!($this->referencedPage instanceof SMSPage)) {

         /** @var SMSManager $SMSM */
         $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

         $this->referencedPage = $SMSM->getPage($this->referencedPageId);

      }

      return $this->referencedPage;

   }


   /**
    * @return string
    */
   public function getTemplateName() {


      return $this->getReferencedPage()->getTemplateName();
   }


   /**
    * @return string
    */
   public function getTitle() {


      // alias pages may have their own title (or must, but thats a question of data storage layout)
      $title = $this->SMSPage->getTitle();

      if (empty($title)) {
         return $this->getReferencedPage()->getTitle();
      }

      return $title;

   }


   /**
    * @return string
    */
   public function getNavTitle() {


      $navTitle = $this->SMSPage->getNavTitle();

      if (empty($navTitle)) {
         return $this->getReferencedPage()->getNavTitle();
      }

      return $navTitle;
   }


   /**
    * @return bool
    */
   public function isAccessProtected() {


      return $this->getReferencedPage()->isAccessProtected();
   }


   /**
    * @return bool
    */
   public function isReference() {


      return true;
   }

}
