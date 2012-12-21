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
 * @package tools::form::taglib
 * @class SelectBoxGroupTag
 *
 * Represents a select option group of an APF select field.
 *
 * @author Christian Achatz
 * @version
 * Version 0.3, 13.02.2010<br />
 */
class SelectBoxGroupTag extends AbstractFormControl {

   public function SelectBoxGroupTag() {
      $this->__TagLibs[] = new TagLib('tools::form::taglib', 'SelectBoxOptionTag', 'group', 'option');
      $this->attributeWhiteList[] = 'label';
      $this->attributeWhiteList[] = 'disabled';
   }

   /**
    * @protected
    *
    * Overwrites the <em>onParseTime()</em> method, because we have to parse
    * the options included in this group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
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
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function addOption($displayName, $value, $preSelected = false) {

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
    * Pre-selects an option by a given display name or value.
    *
    * @param string $displayNameOrValue The display name or the value of the option to pre-select.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<<br />
    */
   public function setOption2Selected($displayNameOrValue) {
      foreach ($this->__Children as $objectId => $DUMMY) {
         if ($this->__Children[$objectId]->getAttribute('value') == $displayNameOrValue
               || $this->__Children[$objectId]->getContent() == $displayNameOrValue
         ) {
            $this->__Children[$objectId]->setAttribute('selected', 'selected');
         }
      }
   }

   /**
    * @public
    *
    * Returns the selected options.
    *
    * @return SelectBoxOptionTag[] The selected options.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2010<br />
    */
   public function &getSelectedOption() {

      $selectedOption = null;

      foreach ($this->__Children as $objectId => $DUMMY) {
         if ($this->__Children[$objectId]->getAttribute('selected') === 'selected') {
            $selectedOption = &$this->__Children[$objectId];
            break;
         }
      }

      return $selectedOption;
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
   public function &getSelectedOptions() {

      $selectedOptions = array();

      foreach ($this->__Children as $objectId => $DUMMY) {
         if ($this->__Children[$objectId]->getAttribute('selected') === 'selected') {
            $selectedOptions[] = &$this->__Children[$objectId];
         }
      }

      return $selectedOptions;
   }

   /**
    * @public
    *
    * Returns the HTML code of the option group.
    *
    * @return string The HTML source code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.3, 13.02.2010<br />
    */
   public function transform() {
      $html = '<optgroup ' . $this->getSanitizedAttributesAsString($this->__Attributes) . '>';
      foreach ($this->__Children as $objectId => $DUMMY) {
         $html .= $this->__Children[$objectId]->transform();
      }
      return $html . '</optgroup>';
   }

}