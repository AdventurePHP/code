<?php
namespace APF\tests\suites\core\expression;

/**
 * @package APF\tests\suites\core\expression
 * @class LinkModel
 *
 * Model to test Document's data attribute mechanism.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.01.2014<br />
 */
class LinkModel {

   private $moreLink = 'http://adventure-php-framework.org';
   private $moreLabel = 'APF web site';

   /**
    * @return string
    */
   public function getMoreLabel() {
      return $this->moreLabel;
   }

   /**
    * @return string
    */
   public function getMoreLink() {
      return $this->moreLink;
   }


} 