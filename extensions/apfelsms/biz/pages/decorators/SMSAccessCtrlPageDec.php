<?php

import('extensions::apfelsms::biz::pages::decorators', 'SMSAbstractPageDec');
import('extensions::apfelsms::biz::pages::decorators::providers', 'SMSAccessCtrlProviderInterface');

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (30.09.12)
 *
 */
class SMSAccessCtrlPageDec extends SMSAbstractPageDec {


   /**
    * @var string
    */
   protected $providerServiceNamespace;


   /**
    * @var string
    */
   protected $providerServiceName;


   /**
    * @var SMSAccessCtrlProvider
    */
   protected $provider;


   /**
    * @var array
    */
   public static $mapVars = array(
      'providerServiceNamespace' => null,
      'providerServiceName' => null
   );


   /**
    * @return bool
    */
   public function isAccessProtected() {

      return $this->getProvider()->isAccessProtected($this->getOuterPage());

   }


   /**
    * @return SMSAccessCtrlProvider
    * @throws SMSWrongDataException
    */
   public function getProvider() {

      if (!($this->provider instanceof SMSAccessCtrlProvider)) {

         $provider = $this->getDIServiceObject($this->providerServiceNamespace, $this->providerServiceName);

         if (!($provider instanceof SMSAccessCtrlProvider)) {
            throw new SMSWrongDataException('[SMSAccessCtrlPageDec::isAccessProtected()] Returned service object does not implement the SMSAccessCtrlProvider interface.', E_USER_ERROR);
         }

         $this->provider = $provider;

      }

      return $this->provider;
   }
}
