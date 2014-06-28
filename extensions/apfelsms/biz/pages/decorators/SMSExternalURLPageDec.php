<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\tools\link\Url;

/**
 * Returns an external URL as page URL.
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (19.08.12)
 */
class SMSExternalURLPageDec extends SMSAbstractPageDec {


   /**
    * @var string $externalURL
    */
   protected $externalURL = '';


   public static $mapVars = array(
         'externalURL' => null
   );


   /**
    * @param string $externalURL
    */
   public function setExternalURL($externalURL) {


      $this->externalURL = $externalURL;
   }


   /**
    * @return string
    */
   public function getExternalURL() {


      return $this->externalURL;
   }


   /**
    * @param Url $url (Unused)
    *
    * @return string
    */
   public function getLink(Url $url) {


      return $this->getExternalURL();
   }

}
