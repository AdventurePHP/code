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
namespace APF\core\expression\taglib;

use APF\core\expression\TemplateCondition;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\PlaceHolder;
use APF\core\registry\Registry;

/**
 * Implements a conditional place holder tag that only displays itself in case the
 * content matches the given condition.
 * <p/>
 * Example:
 * <code>
 * <cond:placeholder name="" [condition=""]>
 *    <h3 class="...">${content}</h3>
 * </cond:placeholder>
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.02.2016 (ID#283: added first version of tag implementation)<br />
 * Version 0.2, 12.03.2016 (ID#287: update to new place holder scheme)<br />
 */
class ConditionalPlaceHolderTag extends Document implements PlaceHolder {

   const PLACE_HOLDER_NAME = 'content';

   public function transform() {

      // The condition defines whether or not the content is displayed. Can be overwritten
      // by the "condition" attribute according to the TemplateCondition capabilities.
      $condition = $this->getAttribute('condition', 'notEmpty()');

      // ID#301: un-escape HTML entities such as double quotes to allow passing regular expressions with double quotes
      $condition = html_entity_decode($condition, ENT_COMPAT | ENT_HTML5, Registry::retrieve('APF\core', 'Charset'));

      $content = $this->getParent()->getPlaceHolder($this->getAttribute('name'));

      if (!TemplateCondition::applies($condition, $content)) {
         return '';
      }

      // inject place holder content into data attribute to make it accessible within the tag for displaying
      if (is_string($content)) {
         // Automatically replace simple place holders.
         // Allow default content output by omitting place holder in case not necessary.
         $this->setPlaceHolder(self::PLACE_HOLDER_NAME, $content);
      } elseif (is_array($content) || is_object($content)) {
         // inject content into a data attribute for further access
         $this->setData(self::PLACE_HOLDER_NAME, $content);
      }

      return $this->transformChildrenAndPreserveContent();
   }

}
