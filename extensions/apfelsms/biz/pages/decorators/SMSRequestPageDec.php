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

use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (21.06.12)
 * @desc    : Adds request params to page URL
 *
 */
class SMSRequestPageDec extends SMSAbstractPageDec {


   /**
    * @var array Request parameter storage
    */
   protected $requestParams = array();


   public static $mapVars = array(
      'requestParam' => array()
   );


   /**
    * @param Url $url
    * @return string
    */
   public function getLink(Url $url) {


      $url->mergeQuery($this->getRequestParams());

      return $this->SMSPage->getLink($url);

   }


   /**
    * @return array
    */
   public function getRequestParams() {


      return $this->requestParams;
   }


   /**
    * Overwrites thw current parameter array with new values
    *
    * @param array $params
    * @return array The new request parameter array
    */
   public function setRequestParams(array $params) {


      return $this->requestParams = $params;
   }


   /**
    * Merge thw new parameter array with the current one
    *
    * @param array $params
    * @return array The new, merged parameter array
    */
   public function mergeRequestParams(array $params) {


      return $this->requestParams = array_merge($this->requestParams, $params);
   }


   /**
    * Remove a parameter. Returns null if parameter is not existing. Otherwise returns the value of the deleted parameter.
    *
    * @param string $paramName
    * @return string|null
    */
   public function removeRequestParam($paramName) {


      if (isset($this->requestParams[$paramName])) {

         $value = $this->requestParams[$paramName];
         unset($this->requestParams[$paramName]);

         return $value;

      }

      return null;

   }


   /**
    * Adds a parameter. To overwrite a existing parameter with same name, set $forceOverwrite to true. Returns the new parameter value.
    *
    * @param string $paramName
    * @param string $paramValue
    * @param bool $forceOverwrite
    * @return array|null
    */
   public function addRequestParam($paramName, $paramValue, $forceOverwrite = false) {


      // skip if param already set
      if (isset($this->requestParams[$paramName]) && !$forceOverwrite) {
         return null;
      }

      return $this->requestParams[$paramName] = $paramValue;

   }


   /**
    * Returns the value of request parameter with name $paramName or null, if parameter is not set.
    *
    * @param string $paramName
    * @return string|null
    */
   public function getRequestParam($paramName) {


      return isset($this->requestParams[$paramName]) ? $this->requestParams[$paramName] : null;
   }

}
