<?php
namespace APF\core\pagecontroller;

class GetStringExpressionCreator implements ExpressionCreator {

   const START_TOKEN = 'getString(';
   const END_TOKEN = ')';

   public static function applies($token) {
      return strpos($token, self::START_TOKEN) !== false && strpos($token, self::END_TOKEN) !== false;
   }

   public static function getDocument($token) {

      // $token = 'getString(APF\modules\guestbook, language.ini, my.key)';
      $startTokenPos = strpos($token, self::START_TOKEN);
      $endTokenPos = strpos($token, self::END_TOKEN, $startTokenPos + 1);

      $arguments = explode(',', substr($token, $startTokenPos + 10, $endTokenPos - $startTokenPos - 10));

      $object = new LanguageLabelTag();
      $object->setAttribute('namespace', trim($arguments[0]));
      $object->setAttribute('config', trim($arguments[1]));
      $object->setAttribute('entry', trim($arguments[2]));

      return $object;

   }

} 