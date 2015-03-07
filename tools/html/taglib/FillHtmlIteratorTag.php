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
namespace APF\tools\html\taglib;

use APF\core\expression\ExpressionEvaluator;
use APF\core\pagecontroller\Document;

/**
 * Allows to initialize an HtmlIteratorTag used within an HtmlIteratorItemTag with the
 * content of the current loop run.
 * <p/>
 * In order to initialize a child iterator tag instance, please use the following
 * template code:
 * <code>
 * <html:iterator name="outer">
 *    <iterator:item>
 *       <item:fill-iterator name="inner" data="item['list']"/>
 *       <html:iterator name="inner" transform-on-place="true">
 *          <iterator:item>
 *             ...
 *          </iterator:item>
 *       </html:iterator>
 *    </iterator:item>
 * </html:iterator>
 * </code>
 * The <em>data</em> attribute specifies the extended template expression that is used
 * to evaluate the date entries for the child iterator instance. The above template code
 * is meant as an example. You may use any kind of extended templating expressions that
 * are supported by the APF or by your custom implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.03.2015 (ID#118: added iterator stacking support)<br />
 */
class FillHtmlIteratorTag extends Document {

   public function transform() {

      $name = $this->getRequiredAttribute('name');

      /* @var $iterator HtmlIteratorTag */
      $iterator = $this->getParentObject()->getChildNode('name', $name, 'APF\tools\html\Iterator');

      $expression = $this->getRequiredAttribute('data');
      $data = ExpressionEvaluator::evaluate($this->getParentObject(), $expression);

      $iterator->fillDataContainer($data);

      // reset iteration number as we are using one single iterator instance within a loop
      $iterator->setIterationNumber(1);

      return '';

   }

}
