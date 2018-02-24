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
namespace APF\tools\form\expression;

use APF\core\expression\ArgumentParser;
use APF\core\pagecontroller\TemplateExpression;
use APF\tools\form\taglib\ValidationListenerTag;
use APF\tools\form\validator\AbstractFormValidator;
use InvalidArgumentException;

/**
 * Custom template expression to support additional validation.
 * <code>
 * <div class="${validationMarker(foo, 'my-css-class')}">
 *    <form:label for="foo">...</form:label>
 *    <form:text name="foo" id="foo" />
 * </div>
 * </code>
 * <p/>
 * Tag signature:
 * <code>
 * validationMarker(<form control name> [, 'content'])
 * </code>
 * Expression generates a <em>&lt;form:listener /&gt;</em> tag to display validation content when respective form
 * control has been marked invalid. In case content has been specified, the respective content is displayed (i.e. CSS
 * class). In case no content has been specified, the default APF CSS validation marker CSS class is displayed.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.02.2018<br/>
 */
class ValidationMarkerTemplateExpression implements TemplateExpression {

   const START_TOKEN = 'validationMarker(';
   const END_TOKEN = ')';

   public static function applies($token) {
      return strpos($token, self::START_TOKEN) !== false && strpos($token, self::END_TOKEN) !== false;
   }

   public static function getDocument($token) {

      $startTokenPos = strpos($token, self::START_TOKEN);
      $endTokenPos = strpos($token, self::END_TOKEN, $startTokenPos + 1);

      $arguments = ArgumentParser::getArguments(
            substr($token, $startTokenPos + 17, $endTokenPos - $startTokenPos - 17)
      );

      // validate expression definition to at least specify the name of the form control
      if (empty($arguments)) {
         throw new InvalidArgumentException(
               'First argument of template expression "validationMarker(control, [\'marker content\'])" '
               . 'is mandatory! Expression given: "' . $token . '".'
         );
      }

      // allow definition of custom validation marker CSS class
      if (isset($arguments[1])) {
         $content = $arguments[1];
      } else {
         $content = AbstractFormValidator::$DEFAULT_MARKER_CLASS;
      }

      $object = new ValidationListenerTag();
      $object->setAttribute('control', $arguments[0]);
      $object->setContent($content);

      return $object;
   }

}
