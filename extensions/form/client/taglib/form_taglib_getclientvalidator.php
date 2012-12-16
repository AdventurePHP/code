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
 * @class form_taglib_getclientvalidator
 *
 *  This taglib generates and renders all information for client validation in the html.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.03.2010<br />
 */
class form_taglib_getclientvalidator extends AbstractFormControl {

   // Cache all button names/control names which already have an onClick/onBlur event
   private $__buttonEventCache = array();
   private $__controlEventCache = array();

   private $__optionsStore = null;

   /**
    * Overwrite the parent's method and inject the form id, if necessary.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function onParseTime() {
      // inject form id to append validators
      $this->__ParentObject->setAttribute('id', $this->getFormId());
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
      $formId = $this->__ParentObject->getAttribute('id');
      if ($formId === null) {
         $formId = 'apf-form-' . $this->__ParentObject->getObjectId();
         $this->__ParentObject->setAttribute('id', $formId);
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
      // seperate index, in order to avoid multiple selecting of the form.
      $javascript = array(
         'general' => '',
         'form' => '$(\'form[id=' . $this->getFormId() . ']\')'
      );


      /* @var $CVSS ClientValidationScriptStore */
      $CVSS = & $this->getServiceObject('extensions::form::client', 'ClientValidationScriptStore', APFService::SERVICE_TYPE_SINGLETON);

      $scriptStore = $CVSS->getScriptStore();
      $valmarkerclassStore = $CVSS->getValmarkerclassStore();
      $this->__optionsStore = $CVSS->getOptionsStore();

      $CVSS->clean();
      unset($CVSS);

      // Check if we need any clientside validator
      if (count($scriptStore) > 0) {
         foreach ($scriptStore as $validatorDefinition) {
            $def = $this->__generateJsDefinition($validatorDefinition);
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
    * @return string The generated javascript.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   protected function __generateJsDefinition($definition) {

      // We handle all js-functions which need to be called on the form element in a
      // seperate index, in order to avoid multiple selecing of the form.
      $output = array(
         'general' => '',
         'form' => ''
      );

      // Check if we already set an event on this button
      if (!isset($this->__buttonEventCache[$definition['button']])) {
         $output['general'] .= ' $(\'form[id=' . $this->getFormId() . ']\').find(\'input[name=' . $definition['button'] . ']\').click(' .
               'function(event) {' .
               'if(!$(\'form[id=' . $this->getFormId() . ']\').validate($(this).attr(\'name\'))){' .
               'event.stopImmediatePropagation();' .
               'return false;' .
               '}' .
               '}' .
               ');';
         $this->__buttonEventCache[$definition['button']] = true;
      }

      // Check type of control, and generate jQuery selector
      /* @var $parent HtmlFormTag */
      $parent = $this->__ParentObject;
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
         if (!isset($this->__controlEventCache[$definition['control']])) {
            $output['general'] .= ' $("form[id=\'' . $this->getFormId() . '\'] ' . $jQSelector . '").live("blur",' .
                  'function() {' .
                  '$(\'form[id=' . $this->getFormId() . ']\').validateControl(\'' . $definition['button'] . '\', $(this));' .
                  '}' .
                  ');';
            $this->__controlEventCache[$definition['control']] = true;
         }
      }

      // Create js which adds the validator
      $opt = '{}';
      if (isset($this->__optionsStore[$definition['control']])) {
         $opt = $this->jsonEncodeAsObject($this->__optionsStore[$definition['control']]);
      }
      $output['form'] .= '.addValidator(\'' . $definition['button'] . '\', \'' . $definition['control'] . '\', \'' . $definition['class'] . '\', ' . $opt . ')';

      return $output;

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

   /**
    * Encode an array as json-object. Uses JSON_FORCE_OBJECT, if php-version >= 5.3.0
    *
    * @param array $obj The Array which should be encoded
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

}
