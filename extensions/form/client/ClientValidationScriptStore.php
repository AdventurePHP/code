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
namespace APF\extensions\form\client;

use APF\core\pagecontroller\APFObject;

/**
 * A store which contains all necessary information for client validators.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.03.2010<br />
 */
class ClientValidationScriptStore extends APFObject {

   /**
    * Contains all validators which need to be added.
    *
    * @var array $scriptStore
    */
   protected $scriptStore = [];
   /**
    * Contains all valmarkerclasses
    *
    * @var array $valmarkerclassStore
    */
   protected $valmarkerclassStore = [];
   /**
    * Contains all options for the controls
    *
    * @var array $optionsStore
    */
   protected $optionsStore = [];

   /**
    * Adds a validator snipped to the script store, and adds validator file to file store if necessary.
    *
    * @param string $class The validator class.
    * @param string $button The button's name.
    * @param array $controls An array with all controls the validator has to validate.
    * @param array $options The options for the validator.
    * @param bool $onblur If set to true, the validator will be also added as blur event. Default: false
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function addClientValidator($class, $button, $controls, $options = null, $onblur = false) {

      // add each control which needs this validator to scriptStore
      foreach ($controls as $control => $DUMMY) {
         $this->scriptStore[] = [
               'class'   => $class,
               'button'  => $button,
               'control' => $control,
               'onblur'  => $onblur
         ];

         if (!isset($this->optionsStore[$control])) {
            $this->optionsStore[$control] = $options[$control];
         }
         if (!isset($this->valmarkerclassStore[$control]) && ($DUMMY['valmarkerclass'] !== 'apf-form-error')) {
            $this->valmarkerclassStore[$control] = $DUMMY['valmarkerclass'];
         }
      }

   }

   /**
    * Returns the script store
    *
    * @return array The script store.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function getScriptStore() {
      return $this->scriptStore;
   }

   /**
    * Returns the valmarkerclass Store
    *
    * @return array The valmarkerclass store.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function getValmarkerclassStore() {
      return $this->valmarkerclassStore;
   }

   /**
    * Returns the options store.
    *
    * @return array The options store.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function getOptionsStore() {
      return $this->optionsStore;
   }

   /**
    * Cleans all stores.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function clean() {
      $this->scriptStore = [];
      $this->valmarkerclassStore = [];
      $this->optionsStore = [];
   }
}
