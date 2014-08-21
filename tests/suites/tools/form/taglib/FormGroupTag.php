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
use APF\tools\form\mixin\FormControlFinder as FormControlFinderImpl;

/**
 * Represents a form element group that can be used to show/hide form markup along with form fields.
 * <p/>
 * Acts as a simple proxy for inner form elements and generates no output. Can be addressed with name/id
 * as usual for APF form elements.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.08.2014 (ID#198)<br />
 */
class FormGroupTag extends AbstractFormControl implements FormElementGroup {

   use FormControlFinderImpl;

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   public function isValid() {
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof FormControl) {
            if ($this->children[$objectId]->isValid() === false) {
               return false;
            }
         }
      }

      return true;
   }

   public function isSent() {
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof FormControl) {
            if ($this->children[$objectId]->isSent() === true) {
               return true;
            }
         }
      }

      return false;
   }

   public function &hide() {
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof FormControl) {
            $this->children[$objectId]->hide();
         }
      }
      $this->isVisible = false;

      return $this;
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
