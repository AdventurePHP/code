<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

// <html:placeholder name="..."/>
$search = '#<html:placeholder([ |\n|\r\n|\r]+)name ?= ?"(.+)"([ |\n|\r\n|\r]*)/>#mU';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace($search, '${\\2}', $content);

   file_put_contents($file, $content);
}
