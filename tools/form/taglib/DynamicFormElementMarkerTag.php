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
namespace APF\tools\form\taglib;

use APF\tools\form\FormMarker;

/**
 * Represents the <form:marker /> tag, that can be used to dynamically create forms. Please
 * have a look at the API documentation of the HtmlFormTag class for details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.09.2008<br />
 * Version 0.2, 23.08.2014 (ID#198: added interface to re-use HtmlForm::getMarker() method with custom form implementations)<br />
 */
class DynamicFormElementMarkerTag extends AbstractFormControl implements FormMarker {

   public function onParseTime() {
   }

   public function transform() {
      return '';
   }

   public function addContentBefore(string $content) {
      $objectId = $this->getObjectId();
      $this->getParent()->setContent(str_replace(
                  '<' . $objectId . ' />',
                  $content . '<' . $objectId . ' />',
                  $this->getParent()->getContent())
      );

      return $this;
   }

   public function addContentAfter(string $content) {
      $objectId = $this->getObjectId();
      $this->getParent()->setContent(str_replace(
                  '<' . $objectId . ' />',
                  '<' . $objectId . ' />' . $content,
                  $this->getParent()->getContent())
      );

      return $this;
   }

   public function reset() {
      // nothing to do as markers don't generate output
      return $this;
   }

}
