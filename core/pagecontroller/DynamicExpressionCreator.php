<?php
namespace APF\core\pagecontroller;

use APF\core\expression\taglib\ExpressionEvaluationTag;

class DynamicExpressionCreator implements ExpressionCreator {

   public static function applies($token) {
      return strpos($token, '->') !== false || strpos($token, '[') !== false;
   }

   public static function getDocument($token) {
      $expressionTag = new ExpressionEvaluationTag();
      $expressionTag->setAttribute(ExpressionEvaluationTag::EXPRESSION, $token);

      return $expressionTag;
   }

} 