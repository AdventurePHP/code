<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.html'));

$search = '#<@controller([ |\n|\r\n]+)namespace ?= ?"([A-Za-z0-9:\-]+)"([\*| |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"([ |\n|\r\n]+)@>#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return '<@controller' . $matches[1] . 'class="APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '"' . $matches[5] . '@>';
   }, $content);

   file_put_contents($file, $content);
}