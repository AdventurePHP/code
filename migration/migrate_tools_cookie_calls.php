<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

foreach ($files as $file) {
   $content = file_get_contents($file);

   // rename class
   $content = str_replace('CookieManager', 'Cookie', $content);

   // rename methods
   $content = str_replace('->createCookie(', '->setValue(', $content);
   $content = str_replace('->updateCookie(', '->setValue(', $content);
   $content = str_replace('->readCookie(', '->getValue(', $content);
   $content = str_replace('->deleteCookie(', '->delete(', $content);

   file_put_contents($file, $content);
}
