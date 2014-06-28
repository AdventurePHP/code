<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\tools\link\Url;

/**
 * Adds an anchor to page URL
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (22.11.2013)
 */
class SMSAnchorPageDec extends SMSAbstractPageDec {


   /**
    * The anchor
    *
    * @var string $anchor
    */
   protected $anchor = '';


   public static $mapVars = array(
         'anchor' => ''
   );


   /**
    * @param Url $url
    *
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
