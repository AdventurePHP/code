<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\decorators\SMSAbstractPageDec;
use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (22.11.2013)
 * @desc    : Adds an anchor to page URL
 *
 */
class SMSAnchorPageDec extends SMSAbstractPageDec {


   /**
    * @var string The anchor
    */
   protected $anchor = '';


   public static $mapVars = array(
      'anchor' => ''
   );


   /**
    * @param Url $url
    * @return string
    */
   public function getLink(Url $url) {


      $url->setAnchor($this->anchor);

      return $this->SMSPage->getLink($url);

   }


   /**
    * @param string $anchor
    */
   public function setAnchor($anchor) {

      $this->anchor = $anchor;
   }


   /**
    * @return string
    */
   public function getAnchor() {

      return $this->anchor;
   }

}
