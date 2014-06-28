<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\decorators\providers\SMSAccessCtrlProvider;
use APF\extensions\apfelsms\biz\SMSWrongDataException;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (30.09.12)
 *           v0.2 (19.01.13) Added permissionName parameter
 *           v0.3 (08.03.13) Added default for providerServiceNamespace
 *
 */
class SMSAccessCtrlPageDec extends SMSAbstractPageDec {


   /**
    * @var string $providerServiceNamespace
    */
   protected $providerServiceNamespace;


   /**
    * @var string $providerServiceName
    */
   protected $providerServiceName;


   /**
    * @var mixed $permissionName
    *
    * @since v0.2
    */
   protected $permissionName;


   /**
    * @var SMSAccessCtrlProvider $provider
    */
   protected $provider;


   /**
    * @var array $mapVars
    */
   public static $mapVars = array(
         'providerServiceNamespace' => 'APF\extensions\apfelsms\pages\decorators\provider',
         'providerServiceName'      => null,
         'permissionName'           => 'SMSViewPermission'
   );


   /**
    * @return bool
    */
   public function isAccessProtected() {


      return $this->getProvider()->isAccessProtected($this->getOuterPage(), $this->permissionName);

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
