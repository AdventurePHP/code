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
namespace APF\core\filter;

/**
 * This interface describes a generic filter.
 * <p/>
 * Will be used to describe input and output filters as well as form control filters.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.11.2015 (ID#273: introduced interface)<br />
 */
interface Filter {

   /**
    * Filters a given input (mixed) and returns the result.
    *
    * @param mixed $input the input of the filter.
    *
    * @return mixed The output of the filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.11.2015<br />
    */
   public function filter($input);

}
