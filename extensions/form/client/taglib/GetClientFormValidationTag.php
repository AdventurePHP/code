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
namespace APF\extensions\form\client\taglib;

use APF\core\service\APFService;
use APF\extensions\form\client\ClientValidationScriptStore;
use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\taglib\HtmlFormTag;

/**
 *  This taglib generates and renders all information for client validation in the html.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.03.2010<br />
 */
class GetClientFormValidationTag extends AbstractFormControl {

   // Cache all button names/control names which already have an onClick/onBlur event
   private $buttonEventCache = [];
   private $controlEventCache = [];

   private $optionsStore = null;

   /**
    * Overwrite the parent's method and inject the form id, if necessary.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function onParseTime() {
      // inject form id to append validators
      $this->getParentObject()->setAttribute('id', $this->getFormId());
   }

   /**
    * Loads the id of the <form />. If no id is set, it will inject one.
    *
    * @return string The id of the <form />
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   protected function getFormId() {
      $parent = $this->getParentObject();
      $formId = $parent->getAttribute('id');
      if ($formId === null) {
         $formId = 'apf-form-' . $parent->getObjectId();
         $parent->setAttribute('id', $formId);
      }

      return $formId;
   }

   /**
    * Generates and renders the javascript, which adds all validators, valmarkerclasses, events etc.
    *
    * @return string The generated output.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function transform() {

      // We handle all js-functions which need to be called on the form element in a
      // separate index, in order to avoid multiple selecting of the form.
      $javascript = [
            'general' => '',
            'form'    => '$(\'form[id=' . $this->getFormId() . ']\')'
      ];

      /* @var $CVSS ClientValidationScriptStore */
      $CVSS = $this->getServiceObject(ClientValidationScriptStore::class, [], APFService::SERVICE_TYPE_SINGLETON);

      $scriptStore = $CVSS->getScriptStore();
      $valmarkerclassStore = $CVSS->getValmarkerclassStore();
      $this->optionsStore = $CVSS->getOptionsStore();

      $CVSS->clean();
      unset($CVSS);

      // Check if we need any client side validator
      if (count($scriptStore) > 0) {
         foreach ($scriptStore as $validatorDefinition) {
            $def = $this->generateJsDefinition($validatorDefinition);
            $javascript['general'] .= $def['general'];
            $javascript['form'] .= $def['form'];
         }
      }

      // Create js which fills valmarkerclassstore
      $javascript['form'] .=
            '.addValmarkerclasses(' .
            $this->jsonEncodeAsObject($valmarkerclassStore) .
            ')';

      return '<script type="text/javascript">' . $javascript['general'] . $javascript['form'] . ';</script>';

   }

   /**
    * Generates the js-code for one validator on one control
    *
    * @param array $definition The definition from scriptStore.
    *
    * @return string The generated javascript.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   protected function generateJsDefinition($definition) {

      // We handle all js-functions which need to be called on the form element in a
      // separate index, in order to avoid multiple selecting of the form.
      $output = [
            'general' => '',
            'form'    => ''
      ];

      // Check if we already set an event on this button
      if (!isset($this->buttonEventCache[$definition['button']])) {
         $output['general'] .= ' $(\'form[id=' . $this->getFormId() . ']\').find(\'input[name=' . $definition['button'] . ']\').click(' .
               'function(event) {' .
               'if(!$(\'form[id=' . $this->getFormId() . ']\').validate($(this).attr(\'name\'))){' .
               'event.stopImmediatePropagation();' .
               'return false;' .
               '}' .
               '}' .
               ');';
         $this->buttonEventCache[$definition['button']] = true;
      }

      // Check type of control, and generate jQuery selector
      /* @var $parent HtmlFormTag */
      $parent = $this->getParentObject();
      switch (get_class($parent->getFormElementByName($definition['control']))) {
         case 'SelectBoxTag':
            $jQSelector = ':input[name=\'' . $definition['control'] . '\[\]\']';
            break;
         case 'DateSelectorTag':
            $jQSelector = 'span[id=\'' . $definition['control'] . '\']';
            break;
         default:
            $jQSelector = ':input[name=\'' . $definition['control'] . '\']';
      }

      // check if we need to set an onBlur event, and if we set it already on this control.
      if ($definition['onblur'] === true) {
         if (!isset($this->controlEventCache[$definition['control']])) {
            $output['general'] .= ' $("form[id=\'' . $this->getFormId() . '\'] ' . $jQSelector . '").live("blur",' .
                  'function() {' .
                  '$(\'form[id=' . $this->getFormId() . ']\').validateControl(\'' . $definition['button'] . '\', $(this));' .
                  '}' .
                  ');';
            $this->controlEventCache[$definition['control']] = true;
         }
      }

      // Create js which adds the validator
      $opt = '{}';
      if (isset($this->optionsStore[$definition['control']])) {
         $opt = $this->jsonEncodeAsObject($this->optionsStore[$definition['control']]);
      }
      $output['form'] .= '.addValidator(\'' . $definition['button'] . '\', \'' . $definition['control'] . '\', \'' . $definition['class'] . '\', ' . $opt . ')';

      return $output;

   }

   /**
    * Encode an array as json-object. Uses JSON_FORCE_OBJECT, if php-version >= 5.3.0
    *
    * @param array $obj The Array which should be encoded
    *
    * @return string The generated json.
    * @author Ralf Schubert
    * @version
    * Version 1.0, 14.04.2010<br />
    */
   protected function jsonEncodeAsObject(array $obj) {
      if (version_compare(phpversion(), '5.3.0', '<') === true) {
         return json_encode($obj);
      } else {
         return json_encode($obj, JSON_FORCE_OBJECT);
      }
   }

   /**
    * Overwrite the parent's method, because there's nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function onAfterAppend() {
   }

   public function reset() {
      // nothing to do as client validation rule generation takes no user input
   }

}
