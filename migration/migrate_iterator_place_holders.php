<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

$search = '#<html:placeholder([ |\n|\r\n|\r]+)getter ?= ?"(.+)"([ |\n|\r\n|\r]+)/>#U';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace($search, '${item->\\2()}', $content);

   file_put_contents($file, $content);
}
