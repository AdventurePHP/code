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
use APF\core\pagecontroller\Document;

/**
 * Represents a tag that evaluates dynamic expressions used in templates to ease
 * displaying content and have at least a bit of template logic.
 * <p/>
 * Tag can be both used literally and implicitly. See <em>Document::extractExpressionTags()</em>
 * for details on implicit usage. Besides, the tag can be used explicitly within all APF
 * templates as follows:
 * <code>
 * <core:addtaglib class="APF\expression\taglib\ExpressionEvaluationTag" prefix="dyn" name="expr" />
 * <dyn:expr expression="model[0]->getFoo()" />
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
class ExpressionEvaluationTag extends Document {

   /**
    * The expression attribute name.
    *
    * @var string EXPRESSION
    */
   const EXPRESSION = 'expression';

   public function transform() {
      return ExpressionEvaluator::evaluate($this->getParentObject(), $this->getAttribute(self::EXPRESSION));
   }

} 