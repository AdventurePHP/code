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
namespace APF\core\exceptionhandler;

use APF\core\http\mixins\GetRequestResponse;
use APF\core\registry\Registry;
use Throwable;

/**
 * Implements a live exception handler, that logs the occurred exception and redirects
 * to a statically configured page to hide the exception from the customer (e.g. for
 * security reasons).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2010<br />
 */
class ProductionExceptionHandler extends DefaultExceptionHandler {

   use GetRequestResponse;

   public function handleException(Throwable $exception) {

      // fill attributes
      $this->exceptionNumber = $exception->getCode();
      $this->exceptionMessage = $exception->getMessage();
      $this->exceptionFile = $exception->getFile();
      $this->exceptionLine = $exception->getLine();
      $this->exceptionTrace = $exception->getTrace();
      $this->exceptionType = get_class($exception);

      // log exception
      $this->logException();

      // redirect to configured page
      $response = $this->getResponse();
      $response->forward($this->getRedirectPage());

   }

   /**
    * @return string The redirect page serving as an error page (to hide internal information of the application for security reasons).
    */
   protected function getRedirectPage() {
      return Registry::retrieve('APF\core\exceptionhandler', 'ProductionExceptionRedirectUrl', '/');
   }

}
