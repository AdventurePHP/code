<?php
namespace APF\extensions\apfelsms\pres\documentcontroller;

use APF\core\pagecontroller\BaseDocumentController;
use APF\tools\link\Url;


/**
 * 
 * @package APF\extensions\apfelsms
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (15.05.13) Introducted class to add support to keep certain request parameters in url
 * 
 */
abstract class SMSBaseNavTagController extends BaseDocumentController {


   /**
    * @const string
    * @desc Key for "keepRequestParams"-Parameter, which keeps all parameters
    */
   const KEEPALLPARAMSKEY = '__SMS-ALL__';
   
   
   
   /**
    * @return Url A prepared url as prototype for page links (clone it!)
    */
   protected function getUrlPrototype() {


      $url = Url::fromCurrent();
      $keepRequestParams = $this->getDocument()->getAttribute('SMSBaseNavKeepRequestParams');


      // keep all parameters (leave url unchanged)
      if($keepRequestParams == self::KEEPALLPARAMSKEY) {
         return $url;
      }

      // delete all params
      if(empty($keepRequestParams)) {
         return $url->resetQuery();
      }

      // explode comma-seperated list of parameters
      $keepParams = explode(',', $keepRequestParams);
      $keepedParams = array();

      // uugh, this is a bad case which normaly never should happen...
      // if array is empty, also delete all params
      if(count($keepParams) < 1) {
         return $url->resetQuery();
      }

      // save parameters
      foreach ($keepParams AS $keepParam) {
         $keepedParams[$keepParam] = $url->getQueryParameter($keepParam);
      }

      // delete all
      $url->resetQuery();
      // restore saved params
      $url->setQuery($keepedParams);

      return $url;

   }
   
}
