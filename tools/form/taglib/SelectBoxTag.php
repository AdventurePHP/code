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
namespace APF\tools\form\taglib;

use APF\tools\form\mixin\AddSelectBoxEntry;
use APF\tools\form\validator\FormValidator;

/**
 * Represents an APF select field.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 07.01.2007<br />
 * Version 0.2, 12.01.2007 (Renamed to "SelectBoxTag")<br />
 * Version 0.3, 15.02.2010 (Added option groups)<br />
 */
class SelectBoxTag extends AbstractFormControl {

   use AddSelectBoxEntry;

   /**
    * Marks the field as dynamic to do special presetting on transformation time.
    *
    * @var boolean $isDynamicField
    */
   protected $isDynamicField = false;

   /**
    * Initializes the allowed child tags.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 03.03.2007 (Removed "&" before "new")<br />
    */
   public function __construct() {
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'size';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'onchange';
   }

   /**
    * Parses the options and initializes the select field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 03.08.2016 (ID#303: allow hiding via template definition)<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
      $value = $this->getRequestValue();
      if ($value !== null) {
         $this->setOption2Selected($value);
      }

      // ID#303: allow to hide form element by default within a template
      if ($this->getAttribute('hidden', 'false') === 'true') {
         $this->hide();
      }
   }

   /**
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

      $subMarkerStart = '[';
      $subMarkerEnd = ']';

      $request = $this->getRequest();

      // analyze sub-elements by the start marker bracket
      if (substr_count($name, $subMarkerStart) > 0) {
         $startBracketPos = strpos($name, $subMarkerStart);
         $endBracketPos = strpos($name, $subMarkerEnd);
         $mainName = substr($name, 0, $startBracketPos);
         $subName = substr($name, $startBracketPos + 1,
               $endBracketPos - $startBracketPos - strlen($subMarkerEnd)
         );

         $value = $request->getParameter($mainName);
         if ($value !== null && isset($value[$subName])) {
            $value = $value[$subName];
         } else {
            $value = null;
         }
      } else {
         $value = $request->getParameter($name);
      }

      return $value;
   }

   /**
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
      foreach ($this->children as &$child) {

         // treat groups as a special case, because a group has more options in it!
         if ($child instanceof SelectBoxGroupTag) {
            $child->setOption2Selected($displayNameOrValue);
         } else {
            // bug 981: introduced string-based comparison to avoid pre-select issues with "0".
            if ($child->getAttribute('value') == (string) $displayNameOrValue
                  || $child->getContent() == (string) $displayNameOrValue
            ) {
               $child->setAttribute('selected', 'selected');
               $selectedObjectId = $child->getObjectId();
            }
         }
      }

      $this->removeSelectedOptions($selectedObjectId);
   }

   /**
    * Un-selects all other option to do not have interference with the currently selected option!
    * This is only necessary within the simple select field - not multi select.
    *
    * @param string $selectedObjectId The objectId of the selected option.
    *
    * @author Daniel Basedow
    * @version
    * Version 0.1, 22.03.2013<br />
    */
   protected function removeSelectedOptions($selectedObjectId) {
      if ($selectedObjectId !== null) {
         foreach ($this->children as &$child) {
            if ($child->getObjectId() != $selectedObjectId) {
               $child->deleteAttribute('selected');
            }
         }
      }
   }

   /**
    * Adds an option to the select field.
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
      $tag = new SelectBoxOptionTag();
      $tag->setContent($displayName);
      $tag->setAttribute('value', $value);
      if ($preSelected == true) {
         $tag->setAttribute('selected', 'selected');
      }
      $this->addOptionTag($tag);
   }

   /**
    * Adds an option to the select field (OO style).
    *
    * @param SelectBoxOptionTag $tag The option to add to the select box.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 07.01.2014<br />
    */
   public function addOptionTag(SelectBoxOptionTag $tag) {

      // mark as dynamic field
      $this->isDynamicField = true;

      $this->addEntry($tag);
   }

   /**
    * Adds a group to an existing select box (OO style).
    *
    * @param SelectBoxGroupTag $tag The group to add to the select box.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.10.2015<br />
    */
   public function addGroupTag(SelectBoxGroupTag $tag) {

      // mark as dynamic field
      $this->isDynamicField = true;

      $this->addEntry($tag);
   }

   /**
    * Adds an option to a group specified by the applied label.
    *
    * @param string $groupLabel The name of the group's label.
    * @param string $displayName The display text of the option.
    * @param string $value The option's value.
    * @param boolean $preSelected True in case, the option should be selected, false otherwise.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function addGroupOption($groupLabel, $displayName, $value, $preSelected = false) {

      // mark as dynamic field
      $this->isDynamicField = true;

      // retrieve or lazily create group
      $group = $this->getOrCreateGroup($groupLabel);

      // add option to group
      $group->addOption($displayName, $value, $preSelected);
   }

   /**
    * Returns - or lazily creates - a desired option group.
    *
    * @param string $groupLabel The name of the group.
    *
    * @return SelectBoxGroupTag The option group.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 07.01.2014<br />
    */
   protected function &getOrCreateGroup($groupLabel) {
      $group = $this->getGroup($groupLabel);

      // lazily create group for convenience reason
      if ($group === null) {
         $tag = new SelectBoxGroupTag();
         $tag->setAttribute('label', $groupLabel);
         $group = $this->addEntry($tag);
      }

      return $group;
   }

   /**
    * Returns the desired group by a given group label.
    *
    * @param string $label The label of the group to return.
    *
    * @return SelectBoxGroupTag The desired group or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function &getGroup($label) {

      $group = null;

      foreach ($this->children as &$child) {
         if ($child->getAttribute('label') == $label) {
            $group = &$child;
            break;
         }
      }

      return $group;
   }

   /**
    * Adds an option to a group specified by the applied label (OO-style).
    *
    * @param string $groupLabel The name of the group.
    * @param SelectBoxOptionTag $option The option to add.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 07.01.2014<br />
    */
   public function addGroupOptionTag($groupLabel, SelectBoxOptionTag $option) {
      // mark as dynamic field
      $this->isDynamicField = true;

      // retrieve or lazily create group
      $group = $this->getOrCreateGroup($groupLabel);

      $group->addOptionTag($option);
   }

   /**
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
         $select = (string) '';
         $select .= '<select ' . $this->getSanitizedAttributesAsString($this->attributes) . '>';

         $this->transformChildren();

         return $select . $this->content . '</select>';
      }

      return '';
   }

   /**
    * Re-implements the addValidator() method for select fields.
    *
    * @param FormValidator $validator The desired validator.
    *
    * @since 1.11
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    * Version 0.2, 05.09.2014 (ID#233: Added support to omit validators for hidden fields)<br />
    */
   public function addValidator(FormValidator &$validator) {

      // ID#166: register validator for further usage.
      $this->validators[] = $validator;

      // Directly execute validator to allow adding validators within tags and
      // document controllers for both static and dynamic form controls.
      $value = $this->getValue();

      // Check both for validator being active and for mandatory fields to allow optional
      // validation (means: field has a registered validator but is sent with empty value).
      // ID#233: add/execute validators only in case the control is visible. Otherwise, this
      // may break the user flow with hidden mandatory fields and users end up in an endless loop.
      if ($validator->isActive() && $this->isMandatoryForValidation($value) && $this->isVisible()) {
         $option = $this->getSelectedOption();
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
    * Returns the selected option.
    *
    * @return SelectBoxOptionTag|null The selected option.
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
      foreach ($this->children as &$child) {

         if ($child instanceof SelectBoxGroupTag) {
            $selectedOption = $child->getSelectedOption();

            // Bug-436: exit at the first hit to not overwrite this hit with another miss!
            if ($selectedOption !== null) {
               break;
            }
         } else {
            if ($child->getAttribute('selected') === 'selected') {
               $selectedOption = &$child;
               break;
            }
         }
      }

      return $selectedOption;
   }

   /**
    * Re-implements the setting of values for select controls
    *
    * @param string $value The display name or the value of the option to pre-select.
    *
    * @return SelectBoxTag
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function &setValue($value) {
      $this->setOption2Selected($value);

      return $this;
   }

   /**
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

   public function reset() {
      foreach ($this->children as &$child) {
         // treat groups as a special case, because a group has more options in it!
         if ($child instanceof SelectBoxGroupTag) {
            $child->reset();
         } else {
            $child->deleteAttribute('selected');
         }
      }
   }

}
