<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*cacheconfig.ini');

foreach ($files as $file) {
   $content = file_get_contents($file);
   $content = preg_replace('#^Cache\.#m', '', $content);
   file_put_contents($file, $content);
}
