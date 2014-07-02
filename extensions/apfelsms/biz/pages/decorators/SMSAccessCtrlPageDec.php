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
