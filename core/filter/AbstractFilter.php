<?php
namespace APF\core\filter;

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
use APF\core\pagecontroller\APFObject;

/**
 * @package APF\core\filter
 * @class AbstractFilter
 * @abstract
 *
 * Abstract filter class.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 08.06.2007<br />
 */
// TODO extract interface...
abstract class AbstractFilter extends APFObject {

   /**
    * @public
    * @abstract
    *
    * Abstract filter method. Must be implemented by concrete filter implementations.
    *
    * @param string $input the input of the filter.
    * @return string The output of the filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.06.2007<br />
    * Version 0.2, 08.12.2008 (Added the $filterInstruction argument)<br />
    * Version 0.3, 18.07.2009 (Removed the $filterInstruction argument and refactored the filters)<br />
    */
   abstract public function filter($input);

}