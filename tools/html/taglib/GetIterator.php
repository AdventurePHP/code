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
namespace APF\tools\html\taglib;

use APF\core\pagecontroller\DomNode;
use APF\tools\html\Iterator;

/**
 * Implements a finder that returns an iterator instance within an
 * HtmlIteratorTag and HtmlIteratorItemTag.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.03.2015 (ID#246: added iterator stacking support)<br />
 */
trait GetIterator {

   /**
    * Returns an iterator instance by a given name.
    *
    * @param string $name The name of the iterator to return.
    *
    * @return Iterator
    */
   public function getIterator(string $name) {
      /* @var $this DomNode */
      return $this->getChildNode('name', $name, Iterator::class);
   }

}
