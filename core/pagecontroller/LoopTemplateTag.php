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
namespace APF\core\pagecontroller;

use APF\core\expression\ExpressionEvaluator;

/**
 * The <em>&lt;loop:template /&gt;</em> represents a reusable html fragment (template) within a
 * template file that can be used to display repetitive content.
 * <p/>
 * Use <em>setPlaceHolder()</em> to inject static content (same content for every loop) or
 * <em>setData()</em> to define the list of entries (array!) to display within your controller. Example:
 * <code>
 * $this->setData('foo', [new Model(), new Model()]);
 * </code>
 * <p/>
 * Attribute <em>content-mapping</em> allows to define the content to display. You can define arbitrary
 * template expressions to evaluate the list of entries (e.g. simple data attribute access or
 * calling APF API methods such as <em>getParentObject()</em>).
 * <p/>
 * Template content is displayed in case <em>transformOnPlace()</em> is called or attribute
 * <em>transform-on-place</em> is set to <em>true</em>. See <em>TemplateTag</em> for details
 * on general template behaviour.
 * <p/>
 * Usage example:
 * <code>
 * <loop:template content-mapping="" [name="loop-template"][transform-on-place="true|false"]>
 *       <p>
 *          Method expressions: ${content->getFoo()}
 *          <br />
 *          Array expressions: ${content['bar']}
 *          <br />
 *          Static place holder: ${static-place-holder}
 *       </p>
 * </loop:template>
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.04.2016<br />
 */
class LoopTemplateTag extends TemplateTag {

   const CONTENT_MAPPING_ATTRIBUTE = 'content-mapping';

   public function onParseTime() {

      // ID#transform-on-place: allow activation of direct output generation within templates directly.
      if ($this->getAttribute('transform-on-place', 'false') === 'true') {
         $this->transformOnPlace();
      }

      parent::onParseTime();
   }

   public function transformTemplate() {

      // Re-map desired content to content data attribute to ease expression and template definition.
      // Most likely, the "model" will be a parent document's data attribute.
      /* @var $list array|null */
      $list = ExpressionEvaluator::evaluate(
            $this->getParentObject(),
            $this->getRequiredAttribute(self::CONTENT_MAPPING_ATTRIBUTE)
      );

      // return empty content in case list is empty.
      if (empty($list) || !is_array($list)) {
         return '';
      }

      // loop through list and use template content to generate output
      $html = '';
      foreach ($list as $entry) {
         $this->setData('content', $entry);
         $html .= $this->transformChildrenAndPreserveContent();
      }

      return $html;
   }

}
