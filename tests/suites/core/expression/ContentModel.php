<?php
namespace APF\tests\suites\core\expression;

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

/**
 * @package APF\tests\suites\core\expression
 * @class ContentModel
 *
 * Model to test Document's data attribute mechanism.
 *
 * <section class="${css-class}">
 * <h3>${headline}</h3>
 * <p>
 * ${intro-text}
 * </p>
 * <p>
 * ${detail-text}
 * </p>
 * <p>
 * <a href="${more-link}">
 * ${more-label}
 * </a>
 * </p>
 * </section>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.01.2014<br />
 */
class ContentModel {

   private $cssClass = 'hint';
   private $headline = 'Great news!';
   private $introText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';
   private $detailText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

   /**
    * @return string
    */
   public function getCssClass() {
      return $this->cssClass;
   }

   /**
    * @return string
    */
   public function getDetailText() {
      return $this->detailText;
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
   public function getIntroText() {
      return $this->introText;
   }

   /**
    * @return LinkModel
    */
   public function getMoreLinkModel() {
      return new LinkModel();
   }

}