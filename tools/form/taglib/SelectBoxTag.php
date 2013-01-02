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
import('tools::form::taglib', 'SelectBoxOptionTag');
import('tools::form::taglib', 'SelectBoxGroupTag');

/**
 * @package tools::form::taglib
 * @class SelectBoxTag
 *
 * Represents an APF select field.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 07.01.2007<br />
 * Version 0.2, 12.01.2007 (Renamed to "SelectBoxTag")<br />
 * Version 0.3, 15.02.2010 (Added option groups)<br />
 */
class SelectBoxTag extends AbstractFormControl {

   /**
    * @var boolean Marks the field as dynamic to do special presetting on transformation time.
    */
   protected $isDynamicField = false;

   /**
    * @public
    *
    * Initializes the child taglibs.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 03.03.2007 (Removed "&" before "new")<br />
    */
   public function __construct() {
      $this->__TagLibs[] = new TagLib('tools::form::taglib', 'SelectBoxOptionTag', 'select', 'option');
      $this->__TagLibs[] = new TagLib('tools::form::taglib', 'SelectBoxGroupTag', 'select', 'group');
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'size';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'onchange';
   }

   /**
    * @public
    *
    * Parses the options and initializes the select field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
      $value = $this->getRequestValue();
      if ($value !== null) {
         $this->setOption2Selected($value);
      }
   }

   /**
    * @public
    *
    * Adds an option to the select field
    *
    * @param string $displayName The display text of the option.
    * @param string $value The option's value.
    * @param boolean $preSelected True in case, the option should be selected, false otherwise.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 07.06.2008 (objectId is now set to the added option)<br />
    */
   public function addOption($displayName, $value, $preSelected = false) {

      // mark as dynamic field
      $this->isDynamicField = true;

      $objectId = XmlParser::generateUniqID();
      $this->__Children[$objectId] = new SelectBoxOptionTag();

      $this->__Children[$objectId]->setObjectId($objectId);
      $this->__Children[$objectId]->setContent($displayName);
      $this->__Children[$objectId]->setAttribute('value', $value);

      if ($preSelected == true) {
         $this->__Children[$objectId]->setAttribute('selected', 'selected');
      }
      $this->__Children[$objectId]->setLanguage($this->__Language);
      $this->__Children[$objectId]->setContext($this->__Context);
      $this->__Children[$objectId]->onParseTime();

      // inject parent object (=this) to guarantee native DOM tree environment
      $this->__Children[$objectId]->setParentObject($this);
      $this->__Children[$objectId]->onAfterAppend();

      // add xml marker, necessary for transformation
      $this->__Content .= '<' . $objectId . ' />';
   }

   /**
    * @public
    *
    * Adds an option to a group specified by the applied label.
    *
    * @param string $groupLabel The name of the group's label.
    * @param string $displayName The display text of the option.
    * @param string $value The option's value.
    * @param boolean $preSelected True in case, the option should be selected, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<<br />
    */
   public function addGroupOption($groupLabel, $displayName, $value, $preSelected = false) {

      // mark as dynamic field
      $this->isDynamicField = true;

      // retrieve or lazily create group
      $group = & $this->getGroup($groupLabel);
      if ($group === null) {

         $objectId = XmlParser::generateUniqID();
         $this->__Children[$objectId] = new SelectBoxGroupTag();
         $this->__Children[$objectId]->setObjectId($objectId);
         $this->__Children[$objectId]->setAttribute('label', $groupLabel);

         $this->__Children[$objectId]->setLanguage($this->__Language);
         $this->__Children[$objectId]->setContext($this->__Context);
         $this->__Children[$objectId]->onParseTime();

         // inject parent object (=this) to guarantee native DOM tree environment
         $this->__Children[$objectId]->setParentObject($this);
         $this->__Children[$objectId]->onAfterAppend();

         // add xml marker, necessary for transformation
         $this->__Content .= '<' . $objectId . ' />';

         // make group available for the subsequent call
         $group = & $this->__Children[$objectId];
      }

      // add option to group
      $group->addOption($displayName, $value, $preSelected);
   }

   /**
    * @public
    *
    * Returns the desired group by a given group label.
    *
    * @param string $label The label of the group to return.
    * @return SelectBoxGroupTag The desired group or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<<br />
    */
   public function &getGroup($label) {

      $group = null;

      foreach ($this->__Children as $objectId => $DUMMY) {
         if ($this->__Children[$objectId]->getAttribute('label') == $label) {
            $group = & $this->__Children[$objectId];
            break;
         }
      }

      return $group;
   }

   /**
    * @public
    *
    * Returns the selected option.
    *
    * @return SelectBoxOptionTag The selected option.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function &getSelectedOption() {

      // lazily do request presetting when not already done
      if ($this->isDynamicField === true) {
         $value = $this->getRequestValue();
         if ($value !== null) {
            $this->setOption2Selected($value);
         }
      }

      $selectedOption = null;
      foreach ($this->__Children as $objectId => $DUMMY) {

         if (get_class($this->__Children[$objectId]) == 'SelectBoxGroupTag') {
            $selectedOption = & $this->__Children[$objectId]->getSelectedOption();

            // Bug-436: exit at the first hit to not overwrite this hit with another miss!
            if ($selectedOption !== null) {
               break;
            }
         } else {
            if ($this->__Children[$objectId]->getAttribute('selected') === 'selected') {
               $selectedOption = & $this->__Children[$objectId];
               break;
            }
         }
      }

      return $selectedOption;
   }

   /**
    * @public
    *
    * Pre-selects an option by a given display name or value.
    *
    * @param string $displayNameOrValue The display name or the value of the option to pre-select.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    * Version 0.2, 17.06.2010 (Bug-fix: introduced un-setting for previously selected options)<br />
    */
   public function setOption2Selected($displayNameOrValue) {

      $this->isDynamicField = false;

      $selectedObjectId = null;
      foreach ($this->__Children as $objectId => $DUMMY) {

         // treat groups as a special case, because a group has more options in it!
         if (get_class($this->__Children[$objectId]) == 'SelectBoxGroupTag') {
            $this->__Children[$objectId]->setOption2Selected($displayNameOrValue);
         } else {
            // bug 981: introduced string-based comparison to avoid pre-select issues with "0".
            if ($this->__Children[$objectId]->getAttribute('value') == (string)$displayNameOrValue
                  || $this->__Children[$objectId]->getContent() == (string)$displayNameOrValue
            ) {
               $this->__Children[$objectId]->setAttribute('selected', 'selected');
               $selectedObjectId = $objectId;
            }
         }
      }

      // un-select all other option to do not have interference with the currently selected option!
      // this is only necessary within the simple select field - not multi select.
      if (get_class($this) == 'SelectBoxTag' && $selectedObjectId !== null) {
         foreach ($this->__Children as $objectId => $DUMMY) {
            if ($objectId != $selectedObjectId) {
               $this->__Children[$objectId]->deleteAttribute('selected');
            }
         }
      }
   }

   /**
    * @public
    *
    * Generates the HTML code of the select field.
    *
    * @return string The HTML code of the select field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.01.2007 (Removed typos)<br />
    * Version 0.3, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
    * Version 0.4, 13.08.2010 (Bug-fix: lazy dynamic presetting failed, when no value was sent)<br />
    * Version 0.5, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {

      // do lazy presetting, in case we are having a field with dynamic options
      if ($this->isDynamicField === true) {
         $value = $this->getRequestValue();
         if ($value !== null) {
            $this->setOption2Selected($value);
         }
      }

      // create html code
      if ($this->isVisible) {
         $select = (string)'';
         $select .= '<select ' . $this->getSanitizedAttributesAsString($this->__Attributes) . '>';

         $this->transformChildren();

         return $select . $this->__Content . '</select>';
      }
      return '';
   }

   /**
    * @public
    * @since 1.11
    *
    * Re-implements the addValidator() method for select fields.
    *
    * @param AbstractFormValidator $validator The desired validator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function addValidator(AbstractFormValidator &$validator) {

      if ($validator->isActive()) {
         $option = & $this->getSelectedOption();
         if ($option === null) {
            $value = null;
         } else {
            $value = $option->getAttribute('value');
         }

         if (!$validator->validate($value)) {
            $validator->notify();
         }
      }
   }

   /**
    * @protected
    *
    * Returns the value of the present form control from the request.
    * Enables sub-elements of form controls (date control!).
    *
    * @return string The form control value in request or null in case the form is not sent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   protected function getRequestValue() {

      $name = $this->getAttribute('name');
      $value = null;

      $subMarkerStart = '[';
      $subMarkerEnd = ']';

      // analyze sub-elements by the start marker bracket
      if (substr_count($name, $subMarkerStart) > 0) {
         $startBracketPos = strpos($name, $subMarkerStart);
         $endBracketPos = strpos($name, $subMarkerEnd);
         $mainName = substr($name, 0, $startBracketPos);
         $subName = substr($name, $startBracketPos + 1,
               $endBracketPos - $startBracketPos - strlen($subMarkerEnd)
         );
         if (isset($_REQUEST[$mainName][$subName])) {
            $value = $_REQUEST[$mainName][$subName];
         }
      } else {
         if (isset($_REQUEST[$name])) {
            $value = $_REQUEST[$name];
         }
      }

      return $value;
   }

   /**
    * @public
    *
    * Re-implements the retrieving of values for select controls
    *
    * @return SelectBoxOptionTag The selected option.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function getValue() {
      return $this->getSelectedOption();
   }

   /**
    * @public
    *
    * Re-implements the setting of values for select controls
    *
    * @param string $value The display name or the value of the option to pre-select.
    * @return SelectBoxTag
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function setValue($value) {
      $this->setOption2Selected($value);
      return $this;
   }

   /**
    * @public
    *
    * Let's check if something was selected in form:select.
    *
    * @return bool True in case the control is selected, false otherwise.
    * @since 1.15
    *
    * @author dave
    * @version
    * Version 0.1, 22.09.2011<br />
    * Version 0.2, 29.05.2012 (Bug-fix: isSelected was always true)<br />
    */
   public function isSelected() {
      if ($this->getSelectedOption()->getValue() == null) {
         return false;
      } else {
         return true;
      }
   }

}
