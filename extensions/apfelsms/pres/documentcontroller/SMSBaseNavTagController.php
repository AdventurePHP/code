<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\extensions\apfelsms\pres\documentcontroller;

use APF\core\pagecontroller\BaseDocumentController;
use APF\tools\link\Url;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (15.05.13) Introducted class to add support to keep certain request parameters in url
 *
 */
abstract class SMSBaseNavTagController extends BaseDocumentController {

   /**
    * Key for "keepRequestParams"-Parameter, which keeps all parameters
    *
    * @var string KEEP_ALL_PARAMS_KEY
    */
   const KEEP_ALL_PARAMS_KEY = '__SMS-ALL__';


   /**
    * @return Url A prepared url as prototype for page links (clone it!)
    * @throws \APF\tools\link\UrlFormatException
    */
   protected function getUrlPrototype() {


      $url = Url::fromCurrent();
      $keepRequestParams = $this->getDocument()->getAttribute('SMSBaseNavKeepRequestParams');


      // keep all parameters (leave url unchanged)
      if ($keepRequestParams == self::KEEP_ALL_PARAMS_KEY) {
         return $url;
      }

      // delete all params
      if (empty($keepRequestParams)) {
         return $url->resetQuery();
      }

      // explode comma-seperated list of parameters
      $keepParams = explode(',', $keepRequestParams);
      $keepedParams = [];

      // uugh, this is a bad case which normaly never should happen...
      // if array is empty, also delete all params
      if (count($keepParams) < 1) {
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
