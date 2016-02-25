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
namespace APF\tests\suites\core\expression\taglib;

use APF\tests\suites\core\expression\LinkModel;

/**
 * Model to test ConditionalTemplateTag's capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.02.2016<br />
 */
class TeaserModel {

   /**
    * @var bool
    */
   public $visible = true;

   /**
    * @var LinkModel
    */
   private $moreLink;
   private $headline = '+++ Important news +++';
   private $subHeadline;
   private $text = 'This is breaking news! Just got notice that something really awesome happened!';

   public function __construct($visible, $subHeadline, $moreLink) {
      $this->visible = $visible;

      if ($subHeadline === true) {
         $this->subHeadline = 'Breaking news';
      }

      if ($moreLink === true) {
         $this->moreLink = new LinkModel();
      }
   }

   /**
    * @return string
    */
   public function getHeadline() {
      return $this->headline;
   }

   /**
    * @return string
    */
   public function getSubHeadline() {
      return $this->subHeadline;
   }

   /**
    * @return string
    */
   public function getText() {
      return $this->text;
   }

   /**
    * @return LinkModel
    */
   public function getMoreLink() {
      return $this->moreLink;
   }

   public function displayIt() {
      return $this->visible;
   }

}
