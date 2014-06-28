<?php
namespace APF\core\errorhandler;

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
 * Implements a live error handler, that logs the occurred error and redirects to
 * a statically configured page to hide the error from the customer (e.g. for
 * security reasons).
 * <p/>
 * In order to use the error handler, please add the following code to your
 * bootstrap file:
 * <code>
 * use APF\core\errorhandler\ProductionErrorHandler;
 * GlobalErrorHandler::registerErrorHandler(
 *    new ProductionErrorHandler('/pages/global-error')
 * );
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2010<br />
 */
class ProductionErrorHandler extends DefaultErrorHandler {

   /**
    * The url the user is redirected to in case of errors.
    *
    * @var string $errorRedirectUrl
    */
   private $errorRedirectUrl;

   /**
    * Let's you define the page/url the user is redirected to in case of errors.
    *
    * @param string $errorRedirectUrl The error page url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public function __construct($errorRedirectUrl) {
      $this->errorRedirectUrl = $errorRedirectUrl;
   }

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine) {

      // fill attributes
      $this->errorNumber = $errorNumber;
      $this->errorMessage = $errorMessage;
      $this->errorFile = $errorFile;
      $this->errorLine = $errorLine;

      // log error
      $this->logError();

      // redirect to configured page
      header('Location: ' . $this->errorRedirectUrl, null, 302);
      exit(0);

   }

}
