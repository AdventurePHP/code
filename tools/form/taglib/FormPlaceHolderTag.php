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
 * @package tools::form::taglib
 * @class FormPlaceHolderTag
 *
 * Represents a APF form place holder.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 12.01.2006 (Renamed to "FormPlaceHolderTag")<br />
 */
class FormPlaceHolderTag extends AbstractFormControl {

   public function __construct() {
   }

   /**
    * @public
    *
    * Re-implements the <em>transform()</em> method of the <em>Document</em> class
    * to not have the overhead of the transformation implemented in <em>Document::transform()</em>.
    * <p/>
    * As a side-effect, the benchmark points set in <em>HtmlFormTag::transformForm()</em>
    * and <em>Document::transform()</em> do not overlap!
    *
    * @return string The content of the place holder.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.04.2010<br />
    */
   public function transform() {
      return $this->content;
   }

}