<?php
namespace APF\tools\form\validator;

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
 * @package tools::form::validator
 * @class CSRFHashValidator
 *
 * Checks the csrf hash from the csrfhash field.
 *
 * @author Daniel Seemaier
 * @version
 * Version 0.1, 29.10.2010
 */
class CSRFHashValidator extends TextFieldValidator {

   /**
    * @public
    *
    * Validates the csrf hash.
    *
    * @param string $input The CSRF hash from the request.
    * @return string True, in case of the hash is valid, otherwise false.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 29.10.2010
    * Version 0.2, 06.11.2010 (Removed the csrf manager)
    */
   public function validate($input) {
      $namespace = $this->control->getAttribute('namespace');
      $class = $this->control->getAttribute('class');
      $salt = $this->control->getAttribute('salt');

      /* @var $provider CSRFHashProvider */
      $provider = & $this->getServiceObject($namespace, $class);
      $hash = $provider->generateHash($salt);

      return $hash === $input;
   }

   /**
    * @protected
    *
    * Overwrites the maker method to have no interference with the "class"
    * attribute when loading the hash provider.
    *
    * @param AbstractFormControl $control The control to mark.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.11.2010<br />
    */
   protected function markControl(AbstractFormControl &$control) {
   }

}
