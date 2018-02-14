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
namespace APF\core\pagecontroller;

/**
 * Defines the way a TemplateTag instance can be cleared using it within a loop
 * before displaying the next item.
 * <p/>
 * This is especially necessary, in case place holders for the next line of a
 * repeating output (e.g. lines of a table) should be cleared to conditionally
 * contain no content.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2013<br />
 */
interface TemplateTagClearApproach {

   /**
    * Clears the applied TemplateTag according to the implementation.
    *
    * @param TemplateTag $node The current TemplateTag node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.05.2013<br />
    */
   public function clear(TemplateTag &$node);

}
