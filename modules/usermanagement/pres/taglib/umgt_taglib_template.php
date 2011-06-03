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
 * @package modules::usermanagement::pres::taglib
 * @class umgt_taglib_template
 *
 * Enables you to display template content based on various conditions registered within
 * the <em>UserDependentContentConditionSet</em>.
 * <p/>
 * The tag features the following configuration attributes:
 * <pre>
 * &lt;umgt:template condition="..." [options=""]&gt;
 *    ...
 * &lt;/umgt:template/gt;
 * </pre>
 * The functionality of the tag is very close to the <em>&lt;html:template/&gt;</em> tag but
 * displays it's content only in case a condition matches.
 * <p/>
 * The content of the tag can be influenced by a document controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class umgt_taglib_template extends html_taglib_template {

   public function onParseTime() {
      $this->transformOnPlace = $this->getAttribute('transform-on-place', 'false') === 'true' ? true : false;
      parent::onParseTime();
   }

   public function transform() {
      $condSet = &$this->getServiceObject('modules::usermanagement::pres::condition', 'UserDependentContentConditionSet');
      /* @var $condSet UserDependentContentConditionSet */

      if ($condSet->conditionMatches($this->getAttribute('condition'), $this->getAttribute('options'))) {
         return parent::transform();
      }
      return '';
   }

}

?>