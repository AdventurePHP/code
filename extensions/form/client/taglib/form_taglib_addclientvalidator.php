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
 * @package extensions::form::client
 * @class form_taglib_addclientvalidator
 *
 *  This taglib adds validators to one or more controls.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 *  Version 1.0, 18.03.2010<br />
 */
class form_taglib_addclientvalidator extends form_control {

   // prevent missing name error
   public function onParseTime() {
   }

   /**
    * @public
    *
    * Adds a validator to one or more controls
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function transform() {

      $class = $this->getAttribute('class');
      $controlsTmp = explode('|', $this->getAttribute('control'));
      $button = $this->getAttribute('button');
      $onblur = false;
      $options = null;
      $controls = array();
      $namespace = $this->getAttribute('namespace');

      if ($this->getAttribute('onblur') === 'true') {
         $onblur = true;
      }

      // Configure referenced controls
      foreach ($controlsTmp as $control) {
         if (($ref = $this->__ParentObject->getFormElementByName($control)->getAttribute('ref')) !== NULL) {
            $controlsTmp[] = $ref;
            try {
               $refField = $this->__ParentObject->getFormElementByName($ref);
            } catch (FormException $e) {
               throw new FormException('[form_taglib_addclientvalidator::transform()]
                        No form element with name "' . $ref . '" found!
                        Check attribute "ref" of element "' . $control . '"!');
            }
            $refField->setAttribute('ref', $control);
            unset($refField);
         }
      }

      $options = array();
      foreach ($controlsTmp as $control) {
         //Get valmarkerclass of each control
         $valmarkerclass = 'apf-form-error';

         if (($val = $this->__ParentObject->getFormElementByName($control)->getAttribute('valmarkerclass')) !== NULL) {
            $valmarkerclass = $val;
            unset($val);
         }
         $controls[$control] = array('control' => $control, 'valmarkerclass' => $valmarkerclass);
         unset($valmarkerclass);

         //Get parameters of each control
         $rawOptions = $this->__ParentObject->getFormElementByName($control)->getAttributes();
         unset($rawOptions['name']);
         unset($rawOptions['valmarkerclass']);
         $options[$control] = $rawOptions;
      }

      $CVSS = &$this->getServiceObject('extensions::form::client', 'ClientValidationScriptStore');
      $CVSS->addClientValidator($class, $button, $controls, $options, $onblur, $namespace);
   }

   /**
    * @public
    *
    * Overwrite the parent's method, because there's nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function onAfterAppend() {
   }

}
