<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

$redundantAddTagLibTagPrefixes = array(
      'template',
      'form',
      'error',
      'listener',
      'success',
      'iterator',
      'item',
      'fallback',
      'addtitle'
);

$redundantGetStringPrefixes = array(
      'template',
      'form',
      'error',
      'listener',
      'success',
      'iterator',
      'item',
      'fallback',
      'addtitle',
      'message'
);

$redundantPlaceHolderPrefixes = array(
      'template',
      'form',
      'error',
      'listener',
      'success',
      'iterator',
      'item',
      'fallback'
);

foreach ($files as $file) {
   $content = file_get_contents($file);

   // <*:addtaglib /> ...
   foreach ($redundantAddTagLibTagPrefixes as $prefix) {
      $content = str_replace('<' . $prefix . ':addtaglib', '<core:addtaglib', $content);
   }

   // <*:getstring /> ...
   foreach ($redundantGetStringPrefixes as $prefix) {
      $content = str_replace('<' . $prefix . ':getstring', '<html:getstring', $content);
   }

   // <*:placeholder /> ...
   foreach ($redundantPlaceHolderPrefixes as $prefix) {
      $content = str_replace('<' . $prefix . ':placeholder', '<html:placeholder', $content);
   }

   file_put_contents($file, $content);
}
