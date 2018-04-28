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
namespace APF\tools\form;

/**
 * Declares the interface of a form marker to add dynamic form content (re-usable DOM object
 * displaying dynamic content).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014 (ID#198: Introduced interface to allow custom implementations)<br />
 */
interface FormMarker {

   /**
    * Allows you to add content (e.g. HTML, text) AFTER the form marker.
    *
    * @param string $content The content to add.
    *
    * @return FormMarker This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function addContentAfter($content);

   /**
    * Allows you to add content (e.g. HTML, text) BEFORE the form marker.
    *
    * @param string $content The content to add.
    *
    * @return FormMarker This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function addContentBefore($content);
}
