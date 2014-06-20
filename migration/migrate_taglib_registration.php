<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#\$this\->tagLibs\[\]([ |\n|\r\n]*)=([ |\n|\r\n]*)new TagLib\(([ |\n|\r\n]*)\'([A-Za-z0-9\\\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\'([ |\n|\r\n]*)\)#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'self::addTagLib(new TagLib(\'' . $matches[4] . '\', \'' . $matches[6] . '\', \'' . $matches[8] . '\'))';
   }, $content);

   file_put_contents($file, $content);
}
