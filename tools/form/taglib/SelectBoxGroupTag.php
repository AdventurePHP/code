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

/**
 * Represents a select option group of an APF select field.
 *
 * @author Christian Achatz
 * @version
 * Version 0.3, 13.02.2010<br />
 */
class SelectBoxGroupTag extends AbstractFormControl {

   use AddSelectBoxEntry;

   public function __construct() {
      $this->attributeWhiteList[] = 'label';
      $this->attributeWhiteList[] = 'disabled';
   }

   /**
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
    * Adds an option to the select field.
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

      $option = new SelectBoxOptionTag();
      $option->setContent($displayName);
      $option->setAttribute('value', $value);

      if ($preSelected == true) {
         $option->setAttribute('selected', 'selected');
      }

      $this->addOptionTag($option);
   }

   /**
    * Adds an option to the select field (OO style).
    *
    * @param SelectBoxOptionTag $option The option to add.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 07.01.2014<br />
    */
   public function addOptionTag(SelectBoxOptionTag $option) {
      $this->addEntry($option);
   }

   /**
    * Pre-selects an option by a given display name or value.
    *
    * @param string $displayNameOrValue The display name or the value of the option to pre-select.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function setOption2Selected($displayNameOrValue) {
      foreach ($this->children as &$child) {
         if ($child->getAttribute('value') == $displayNameOrValue
               || $child->getContent() == $displayNameOrValue
         ) {
            $child->setAttribute('selected', 'selected');
         }
      }
   }

   /**
    * Returns the selected options.
    *
    * @return SelectBoxOptionTag The selected options.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2010<br />
    */
   public function &getSelectedOption() {

      $selectedOption = null;

      foreach ($this->children as &$child) {
         if ($child->getAttribute('selected') === 'selected') {
            $selectedOption = &$child;
            break;
         }
      }

      return $selectedOption;
   }

   /**
    * Returns the selected option.
    *
    * @return SelectBoxOptionTag[] The selected option.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.02.2010<br />
    */
   public function &getSelectedOptions() {

      $selectedOptions = [];

      foreach ($this->children as &$child) {
         if ($child->getAttribute('selected') === 'selected') {
            $selectedOptions[] = &$child;
         }
      }

      return $selectedOptions;
   }

   /**
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
      foreach ($this->children as &$child) {
         $html .= $child->transform();
      }

      return $html . '</optgroup>';
   }

   public function reset() {
      foreach ($this->children as &$child) {
         $child->deleteAttribute('selected');
      }
   }

}
