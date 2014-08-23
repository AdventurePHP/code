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
namespace APF\tools\validation;

/**
 * Defines the structure of a generic validator.
 * <p/>
 * Implementations are e.g. used in the APF form implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014<br />
 */
interface Validator {

   /**
    * Validates a given subject (any kind of data type according to the validator implementation).
    *
    * @param mixed $subject The subject to validate.
    *
    * @return bool True in case the applied argument is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function validate($subject);

}
