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
namespace APF\core\exceptionhandler;

use Error;
use Exception;
use Throwable;

/**
 * Describes the signature of any APF exception handler.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
interface ExceptionHandler {

   /**
    * This method is intended to take the exception's information and processes it.
    *
    * @param Throwable|Exception|Error $exception The current exception.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    * Version 0.2, 14.08.2017 (ID#316: improved PHP7 compatibility)<br />
    * Version 0.3, 03.03.2018 (Re-introduced strict interface definition.)<br />
    */
   public function handleException(Throwable $exception);

}
