<?php
namespace APF\core\pagecontroller;

class PlaceHolderExpressionCreator implements ExpressionCreator {

   public static function applies($token) {
      return preg_match('#^[A-Za-z\-0-9_]+$#', $token);
   }

   public static function getDocument($token) {
      $placeHolder = new PlaceHolderTag();
      $placeHolder->setAttribute('name', $token);

      return $placeHolder;
   }

} 