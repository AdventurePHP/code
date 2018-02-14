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
 * Implements the default clear mechanism that resets the content of the place holders
 * defines within the TemplateTag instance.
 * <p/>
 * Please note, that this implementation only covers instances of the PlaceHolderTag
 * class that is the standard place holder tag within the APF. In case you intend to
 * add your own implementation of place holders or elements that need to be cleared,
 * please implement your custom clear strategy and apply to the <em>TemplateTag::clear()</em>
 * method within the respective document controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2013<br />
 * Version 0.2, 12.03.2016 (ID#287: update to new place holder scheme)<br />
 */
class DefaultTemplateTagClearApproach implements TemplateTagClearApproach {

   public function clear(TemplateTag &$node) {
      // clear place holder for current document
      $node->clearPlaceHolders();

      // clear language label place holders
      foreach ($node->getChildren() as &$child) {
         if ($child instanceof LanguageLabelTag) {
            $child->clearPlaceHolders();
         }
      }
   }

}
