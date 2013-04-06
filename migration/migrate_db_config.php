<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*connections.ini');

$search = '#DB.Type ?= ?"([A-Za-z]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);
   $content = preg_replace_callback($search, function ($matches) {
      return 'DB.Type = "APF\core\database\\' . $matches[1] . 'Handler"';
   }, $content);
   file_put_contents($file, $content);
}
