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
 * @package tools::form::filter
 * @class AbstractFormFilter
 *
 * Defines the base class for all form filters. In case you want to implement your
 * own form filter, derive from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.07.2009 (Implemented a form filter base class because of the PHP bug 48804)<br />
 * Version 0.2, 25.08.2009 (Refactored due to new form taglib concept)<br />
 */
abstract class AbstractFormFilter extends AbstractFilter {

   /**
    * Includes a reference on the control to filter.
    * @var AbstractFormControl The control to filter.
    */
   protected $__Control;

   /**
    * Includes a reference on the button of the form,
    * that initiates the validation event.
    * @var AbstractFormControl The button that triggers the event.
    */
   protected $__Button;

   /**
    * @public
    *
    * Injects the control to validate and the trigger button into the filter.
    *
    * @param AbstractFormControl $control The control, that should be validated.
    * @param AbstractFormControl $button The button, that triggers the validate event.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function __construct(AbstractFormControl &$control, AbstractFormControl &$button) {
      $this->__Control = &$control;
      $this->__Button = &$button;
   }

   /**
    * @public
    *
    * Indicates, whether the control should be filtered or not.
    *
    * @return boolean True, in case the filter should be executes, in all other cases: false.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function isActive() {
      return $this->__Button->isSent();
   }

   /**
    * @protected
    * @since 1.14
    *
    * Evaluates the regular expression that is used for filtering the
    * control's input. In case the developer has defined a custom
    * regexp it is returned instead of the default value.
    *
    * @param String $default The default filter expression.
    * @return string The effective filter expression.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2011<br />
    */
   protected function getFilterExpression($default) {
      $expr = $this->__Control->getAttribute('filter-expr');
      return $expr === null ? $default : $expr;
   }

}
