<?php
namespace APF\tools\form\taglib;

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
use APF\tools\form\FormException;
use APF\tools\form\provider\csrf\CSRFHashProvider;
use APF\tools\form\validator\CSRFHashValidator;

/**
 * Generates a hidden input field with a hash to prevent the form
 * from csrf attacks.
 *
 * @author Daniel Seemaier
 * @version
 * Version 0.1, 06.11.2010
 */
class CsrfProtectionHashTag extends AbstractFormControl {

   /**
    * @var string The generated hash.
    */
   protected $hash;

   /**
    * Sets the default namespace and provider class name.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function __construct() {
      $this->setAttribute('class', 'APF\tools\form\provider\csrf\EncryptedSIDHashProvider');
   }

   /**
    * Generates the hash.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function onParseTime() {

      $class = $this->getAttribute('class');
      $salt = $this->getAttribute('salt');

      if ($salt === null) {
         throw new FormException('[CsrfProtectionHashTag::onParseTime()] The salt attribute is '
               . 'not present. Please refer to the documentation concerning the setup of the '
               . '<form:csrfhash /> tag!');
      }

      /* @var $provider CSRFHashProvider */
      $provider = & $this->getServiceObject($class);
      $this->hash = $provider->generateHash($salt);

      // preset the value to make it available for the validator
      parent::onParseTime();

      // add the csrfhash validator for every button
      /* @var $form HtmlFormTag */
      $form = $this->getParentObject();
      $buttons = $form->getFormElementsByTagName('form:button');
      foreach ($buttons as $offset => $DUMMY) {
         $this->addValidator(new CSRFHashValidator($this, $buttons[$offset]));
      }

   }

   /**
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
