<?php
namespace APF\tools\form\filter;

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
use APF\tools\form\filter\AbstractFormFilter;

/**
 * Implements a filter that can be used to convert text input of floating numbers
 * into a float representation that matches the PHP syntax of floats.
 *
 * @author Lutz Mahlstedt
 * @version
 * Version 0.1, 13.05.2009<br />
 * Version 0.2, 06.06.2012 (Added check if something was typed in formfield)<br />
 */
class FloatFilter extends AbstractFormFilter {

   public function filter($input) {
      if ($input == null) {
         return null;
      } else {
         return floatval(str_replace(',', '.', str_replace(' ', '', $input)));
      }

   }

}
