<?php
namespace APF\modules\usermanagement\pres\validator;

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
use APF\tools\form\validator\TextFieldValidator;

/**
 * @package APF\modules\usermanagement\pres\validator
 * @class AppObjectIdValidator
 *
 * Validates the proxy id schema (alphanumeric since 1.17). See rules at
 * http://de.selfhtml.org/xml/dtd/bearbeitungsregeln.htm#namen.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.02.2013<br />
 */
class AppObjectIdValidator extends TextFieldValidator {

   public function validate($input) {
      return preg_match('/^([A-Za-z0-9_\-\.\:]+)$/i', $input);
   }

}
