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
import('tools::form::validator', 'CSRFHashValidator');

/**
 * @package tools::form::taglib
 * @class form_taglib_csrfhash
 *
 * Generates a hidden input field with a hash to prevent the form
 * from csrf attacks.
 *
 * @author Daniel Seemaier
 * @version
 * Version 0.1, 06.11.2010
 */
class form_taglib_csrfhash extends form_control {

   /**
    * @protected
    * @var string The generated hash.
    */
   protected $hash;

   /**
    * @public
    *
    * Sets the default namespace and provider class name.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function __construct() {
      $this->setAttribute('namespace', 'tools::form::provider::csrf');
      $this->setAttribute('class', 'EncryptedSIDHashProvider');
   }

   /**
    * @public
    *
    * Generates the hash.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function onParseTime() {

      $namespace = $this->getAttribute('namespace');
      $class = $this->getAttribute('class');
      $salt = $this->getAttribute('salt');

      if ($salt === null) {
         throw new FormException('[form_taglib_csrfhash::onParseTime()] The salt attribute is '
               . 'not present. Please refer to the documentation concerning the setup of the '
               . '<form:csrfhash /> tag!');
      }

      /* @var $provider CSRFHashProvider */
      $provider = &$this->getServiceObject($namespace, $class);
      $this->hash = $provider->generateHash($salt);

      // preset the value to make it available for the validator
      parent::onParseTime();

      // add the csrfhash validator for every button
      $buttons = $this->getParentObject()->getFormElementsByTagName('form:button');
      foreach ($buttons as $offset => $DUMMY) {
         $this->addValidator(new CSRFHashValidator($this, $buttons[$offset]));
      }

   }

   /**
    * @public
    *
    * Returns the HTML code of the csrf hash field.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function transform() {
      return '<input type="hidden" name="' . $this->getAttribute('name') . '" value="' . $this->hash . '" />';
   }

}
