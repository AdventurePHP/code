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
namespace APF\tools\form\provider\csrf;

/**
 * Defines the structure of a csrf hash provider.
 *
 * @author Daniel Seemaier
 * @version
 * Version 0.1, 29.10.2010
 * Version 0.2, 06.11.2010 (Added the $salt parameter).
 */
interface CSRFHashProvider {

   /**
    * This function is intended to deliver the form security token.
    *
    * @param string $salt The salt that is used to generate the security token.
    *
    * @return string The form security token.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 06.11.2010
    */
   public function generateHash($salt);

}
