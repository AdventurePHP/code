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
use APF\core\pagecontroller\TagLib;
use APF\core\pagecontroller\XmlParser;

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
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\SelectBoxOptionTag', 'group', 'option');
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
      $this->children[$objectId] = new SelectBoxOptionTag();

      $this->children[$objectId]->setObjectId($objectId);
      $this->children[$objectId]->setContent($displayName);
      $this->children[$objectId]->setAttribute('value', $value);

      if ($preSelected == true) {
         $this->children[$objectId]->setAttribute('selected', 'selected');
      }
      $this->children[$objectId]->setLanguage($this->language);
      $this->children[$objectId]->setContext($this->context);
      $this->children[$objectId]->onParseTime();

      // inject parent object (=this) to guarantee native DOM tree environment
      $this->children[$objectId]->setParentObject($this);
      $this->children[$objectId]->onAfterAppend();

      // add xml marker, necessary for transformation
      $this->content .= '<' . $objectId . ' />';

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
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId]->getAttribute('value') == $displayNameOrValue
               || $this->children[$objectId]->getContent() == $displayNameOrValue
         ) {
            $this->children[$objectId]->setAttribute('selected', 'selected');
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

      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId]->getAttribute('selected') === 'selected') {
            $selectedOption = & $this->children[$objectId];
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

      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId]->getAttribute('selected') === 'selected') {
            $selectedOptions[] = & $this->children[$objectId];
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
      $html = '<optgroup ' . $this->getSanitizedAttributesAsString($this->attributes) . '>';
      foreach ($this->children as $objectId => $DUMMY) {
         $html .= $this->children[$objectId]->transform();
      }
      return $html . '</optgroup>';
   }

}