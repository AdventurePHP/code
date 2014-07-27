<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

$search = '#<html:placeholder([ |\n|\r\n|\r]+)name ?= ?"(.+)"([ |\n|\r\n|\r]*)/>#U';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // migrate tag-style notation to extended templating syntax
   $content = preg_replace($search, '${\\2}', $content);

   file_put_contents($file, $content);
}
