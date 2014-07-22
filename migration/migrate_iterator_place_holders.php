<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

$search = '#<html:placeholder([ |\n|\r\n|\r]+)getter ?= ?"(.+)"([ |\n|\r\n|\r]*)/>#U';

$searchGenericGetter = '#<iterator:item([ |\n|\r\n|\r]+)getter ?= ?"(.+)">(.*)</iterator:item>#ms';
$searchGenericGetterPlaceHolder = '#<html:placeholder([ |\n|\r\n|\r]+)name ?= ?"(.+)"([ |\n|\r\n|\r]*)/>#U';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // migrate simple getter notation to extended templating syntax
   $content = preg_replace($search, '${item->\\2()}', $content);

   // migrate generic getter notation to extended templating syntax as it is no longer supported
   preg_match_all($searchGenericGetter, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      $getter = $match[2];
      $itemContent = $match[3];
      preg_match_all($searchGenericGetterPlaceHolder, $itemContent, $innerMatches, PREG_SET_ORDER);

      foreach ($innerMatches as $innerMatch) {
         $itemContent = str_replace($innerMatch[0], '${item->' . $getter . '(\'' . $innerMatch[2] . '\')}', $itemContent);
      }

      $content = str_replace($match[0], '<iterator:item>' . $itemContent . '</iterator:item>', $content);
   }

   file_put_contents($file, $content);
}
