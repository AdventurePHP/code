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

/**
 * @package core::errorhandler
 * @class ProductionErrorHandler
 *
 * Implements a live error handler, that logs the occured error and redirects to
 * a statically configured page to hide the error from the customer (e.g. for
 * security reasons).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2010<br />
 */
class ProductionErrorHandler extends DefaultErrorHandler {

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext) {

      // fill attributes
      $this->errorNumber = $errorNumber;
      $this->errorMessage = $errorMessage;
      $this->errorFile = $errorFile;
      $this->errorLine = $errorLine;
      $this->errorContext = $errorContext;

      // log error
      $this->logError();

      // redirect to configured page
      $url = Registry::retrieve('apf::core::errorhandler', 'ProductionErrorRedirectUrl', '/');
      header('Location: ' . $url, null, 302);
      exit(0);

   }

}
