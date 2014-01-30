<?php
namespace APF\tests\suites\core\expression;

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