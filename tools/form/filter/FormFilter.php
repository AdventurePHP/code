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
namespace APF\tools\form\filter;

use APF\core\filter\Filter;
use APF\core\pagecontroller\ApplicationContext;
use APF\tools\form\FormControl;

/**
 * Defines the structure of form filters.
 * <p/>
 * In case you want to implement your own form filter, ist is recommended to
 * derive from AbstractFormFilter class implementing this interface.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.11.2015 (ID#273: introduced interface)<br />
 */
interface FormFilter extends Filter, ApplicationContext {

   /**
    * Injects the control to validate and the trigger button into the filter.
    *
    * @param FormControl $control The control, that should be validated.
    * @param FormControl $button The button, that triggers the validate event.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function __construct(FormControl &$control, FormControl &$button);

   /**
    * Indicates, whether the control should be filtered or not.
    *
    * @return boolean True, in case the filter should be executes, in all other cases: false.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function isActive();

}
