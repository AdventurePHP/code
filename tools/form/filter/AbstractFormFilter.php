<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\form\filter;

use APF\core\pagecontroller\APFObject;
use APF\tools\form\FormControl;

/**
 * Defines the base class for all form filters. In case you want to implement your
 * own form filter, derive from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.07.2009 (Implemented a form filter base class because of the PHP bug 48804)<br />
 * Version 0.2, 25.08.2009 (Refactored due to new form taglib concept)<br />
 * Version 0.3, 06.11.2015 (ID#273: introduced interface)<br />
 */
abstract class AbstractFormFilter extends APFObject implements FormFilter {

   /**
    * Includes a reference on the control to filter.
    *
    * @var FormControl $control
    */
   protected $control;

   /**
    * Includes a reference on the button of the form,
    * that initiates the validation event.
    *
    * @var FormControl $button
    */
   protected $button;

   public function __construct(FormControl $control, FormControl $button) {
      $this->control = $control;
      $this->button = $button;
   }

   public function isActive() {
      return $this->button->isSent();
   }

   /**
    * Evaluates the regular expression that is used for filtering the
    * control's input. In case the developer has defined a custom
    * regexp it is returned instead of the default value.
    *
    * @param String $default The default filter expression.
    *
    * @return string The effective filter expression.
    *
    * @since 1.14
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2011<br />
    */
   protected function getFilterExpression($default) {
      $expr = $this->control->getAttribute('filter-expr');

      return $expr === null ? $default : $expr;
   }

}
