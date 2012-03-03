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
 * @class ConfigurableErrorHandler
 *
 * Implements a configurable error handler, that ignores errors convering to the configured
 * error level. This handler can be used, if you want to ignore certain errors due to
 * whatever reason.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2010<br />
 */
class ConfigurableErrorHandler extends DefaultErrorHandler {

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext) {

      // ignore errors, that have been excluded by configuration
      $errorReporting = Registry::retrieve(
         'apf::core::errorhandler',
         'ConfigurableErrorReportingLevel',
         error_reporting()); // retrieve the error level from the PHP config for convenience

      if (($errorReporting & $errorNumber) == 0) {
         return;
      }

      // otherwise, handle the error as intended by the default error handler
      parent::handleError($errorNumber, $errorMessage, $errorFile, $errorLine, $errorContext);

   }

}
