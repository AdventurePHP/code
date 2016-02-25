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
namespace APF\core\expression\taglib;

use APF\core\expression\ExpressionEvaluator;
use APF\core\expression\TemplateCondition;
use APF\core\pagecontroller\TemplateTag;

/**
 * - data      : evaluate whats in ${content}
 * - expression: evaluate data to match against condition
 * - condition : condition to match the result of the expression evaluation against
 *
 * In case result of expression has been validated successfully against the given condition,
 * the template is displayed. Otherwise, no output is generated.
 *
 * Setup:
 * - Controller sets $this->getDocument()->setData('content', new TestModel());
 * - Template decides whether of not and if yes how the content is displayed.
 *
 * Example:
 * <cond:template
 *    [name="expr-template"]
 *    expression="content->getFoo()"
 *    condition="notEmpty()"
 *    [content-mapping=""]>
 *       <p style="border: 1px solid red;">
 *          content->displayIt() == true
 *          <br />
 *          content->getFoo(): ${this->getParentObject()->getData('content')->getFoo()}
 *          <br />
 *          Dynamic place holder: ${dyn-place-holder}
 *       </p>
 * </cond:template>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.02.2016 (ID#283: added first version of tag implementation)<br />
 */
class ConditionalTemplateTag extends TemplateTag {

   public function transform() {

      // Re-map desired model to content data attribute to ease expression and template definition.
      // Most likely, the model will be a parent document's data attribute.
      $model = ExpressionEvaluator::evaluate(
            $this->getParentObject(),
            $this->getRequiredAttribute('content-mapping')
      );

      $this->setData('content', $model);

      // Calculate result to match against condition
      $result = ExpressionEvaluator::evaluate(
            $this,
            $this->getRequiredAttribute('expression')
      );

      // The condition defines whether or not the content is displayed. Can be overwritten
      // by the "condition" attribute according to the TemplateCondition capabilities.
      $condition = $this->getAttribute('condition', 'notEmpty()');

      if (!TemplateCondition::applies($condition, $result)) {
         return '';
      }

      // Mark template for creating output directly as condition matches.
      $this->transformOnPlace();

      return parent::transform();
   }
}
