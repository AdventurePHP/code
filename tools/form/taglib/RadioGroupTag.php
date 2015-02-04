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

use APF\tools\form\FormControl;
use APF\tools\form\FormElementGroup;
use APF\tools\form\mixin\FormControlFinder;

/**
 * Represents a list of radio buttons and adds convenience methods to ease
 * handling within document controllers.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.02.2015 (ID#48)<br />
 */
class RadioGroupTag extends AbstractFormControl implements FormElementGroup {

   use FormControlFinder;

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * Returns the list of radio buttons defined for the current group.
    *
    * @return RadioButtonTag[] The list of radio buttons defined within the group.
    */
   public function &getButtons() {
      return $this->getFormElementsByTagName('form:radio');
   }

   /**
    * @param string $id The id of the button to return.
    *
    * @return RadioButtonTag
    */
   public function &getButtonById($id) {
      return $this->getFormElementByID($id);
   }

   /**
    * Returns the instance of the checked button or null otherwise.
    *
    * @return RadioButtonTag|null The checked button instance of null.
    */
   public function &getCheckedButton() {

      $checkedButton = null;

      $buttons = $this->getButtons();
      foreach ($buttons as $button) {
         if ($button->isChecked()) {
            $checkedButton = $button;
            break;
         }
      }

      return $checkedButton;
   }

   /**
    * Allows you to determine, whether at least one button is checked.
    *
    * @return bool <em>True</em> in case at least one radio button is checked <em>false</em> otherwise.
    */
   public function isChecked() {
      return $this->getCheckedButton() !== null;
   }

   /**
    * Returns the value of the checked button.
    *
    * @return string|null The value of the selected radio button or <em>null</em> in case no selection.
    */
   public function getValue() {
      $checkedButton = $this->getCheckedButton();

      return $checkedButton === null ? null : $checkedButton->getValue();
   }

   // TODO concept to be created how to implement setValue()

   // TODO extract hide(), reset(), and show() to trade and import here as well as in FormGroupTag

   public function &hide() {
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof FormControl) {
            $this->children[$objectId]->hide();
         }
      }
      $this->isVisible = false;

      return $this;
   }

   public function reset() {
      foreach ($this->children as $objectId => $DUMMY) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($this->children[$objectId] instanceof FormControl) {
            $this->children[$objectId]->reset();
         }
      }
   }

   public function &show() {
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof FormControl) {
            $this->children[$objectId]->show();
         }
      }
      $this->isVisible = true;

      return $this;
   }

   public function transform() {
      if ($this->isVisible) {
         return $this->transformChildrenAndPreserveContent();
      }

      return '';
   }

}
