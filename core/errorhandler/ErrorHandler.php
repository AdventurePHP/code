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
 * @package APF\core\errorhandler
 * @class ErrorHandler
 *
 * Describes the signature of any APF error handler.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
interface ErrorHandler {

   /**
    * @public
    *
    * This method is intended to take the error's information and processes it.
    *
    * @param int $errorNumber The error number.
    * @param string $errorMessage The error message.
    * @param string $errorFile The file the error occurred in.
    * @param int $errorLine The line the error occurred at.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine);

}
