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
namespace APF\tools\html;

/**
 * Declares the interface of an iterator implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.08.2014 (ID#231: Introduced interface to allow custom implementations)<br />
 */
interface Iterator {

   /**
    * This method allows you to fill the data container. Arrays with associative
    * keys are allowed as well as lists of objects (arrays with numeric offsets).
    *
    * @param array $data List of objects of an associative array.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function fillDataContainer($data);

} 