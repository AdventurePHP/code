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
namespace APF\tools\form\mapping;

use APF\tools\form\FormControl;
use APF\tools\form\ModelToFormControlMapper;
use APF\tools\form\taglib\CheckBoxTag;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\form\taglib\RadioButtonTag;
use APF\tools\form\taglib\SelectBoxTag;

/**
 * Evaluates real model values for all text field based form controls and all controls that
 * implement setValue() returning the desired value format (e.g. DateSelectorTag, TimeSelectorTag).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.06.2016 (ID#279: introduced value data mappers to be able to customize model to form mappings)<br />
 */
class StandardModelToFormControlMapper implements ModelToFormControlMapper {

   public static function applies(FormControl $control) {
      return !($control instanceof SelectBoxTag
            || $control instanceof MultiSelectBoxTag
            || $control instanceof RadioButtonTag
            || $control instanceof CheckBoxTag);
   }

   public static function setValue(FormControl &$control, $value) {
      return $control->setValue($value);
   }

}
