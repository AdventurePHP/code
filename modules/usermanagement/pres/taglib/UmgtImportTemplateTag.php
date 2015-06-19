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
namespace APF\modules\usermanagement\pres\taglib;

use APF\core\pagecontroller\ImportTemplateTag;
use APF\modules\usermanagement\pres\condition\UserDependentContentConditionSet;

/**
 * Enables you to include templates based on various conditions registered within
 * the <em>UserDependentContentConditionSet</em>.
 * <p/>
 * The tag features the following configuration attributes:
 * <pre>
 * &lt;umgt:importdesign
 *    namespace="..."
 *    template="..."
 *    condition="..."
 *    [options=""]
 * /&gt;
 * </pre>
 * The functionality of the tag is very close to the <em>&lt;core:importdesign/&gt;</em>
 * tag but displays it's content only in case a condition matches.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.05.2011<br />
 */
class UmgtImportTemplateTag extends ImportTemplateTag {

   /**
    * Indicates, whether the referred template should be displayed or not.
    *
    * @var boolean $display
    */
   protected $display = false;

   public function onParseTime() {

      $condSet = & $this->getServiceObject('APF\modules\usermanagement\pres\condition\UserDependentContentConditionSet');
      /* @var $condSet UserDependentContentConditionSet */

      if ($condSet->conditionMatches($this->getContext(), $this->getAttribute('condition'), $this->getAttribute('options'))) {
         parent::onParseTime();
         $this->display = true;
      }
   }

   public function transform() {
      if ($this->display) {
         return parent::transform();
      }

      return '';
   }

}
