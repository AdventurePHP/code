<?php
namespace APF\core\pagecontroller;

interface ExpressionCreator {

   /**
    * @param $token
    *
    * @return bool
    */
   public static function applies($token);

   /**
    * @param string $token
    *
    * @return Document
    */
   public static function getDocument($token);

} 