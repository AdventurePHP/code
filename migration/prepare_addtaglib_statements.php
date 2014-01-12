<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.html'));

// instead of having a lot of aftermath, let's prepare everything... :)
$prepareAddTaglib = '#<([A-Za-z0-9\-]+):addtaglib([\*| |\n|\r\n]+)(namespace|class|prefix|name|) ?= ?"(.+)"([\*| |\n|\r\n]+)(namespace|class|prefix|name|) ?= ?"(.+)"([\*| |\n|\r\n]+)(namespace|class|prefix|name|) ?= ?"(.+)"([\*| |\n|\r\n]+)(namespace|class|prefix|name|) ?= ?"(.+)"([\*| |\n|\r\n]*)/>#Us';

function getAttributeValue(array $matches, $name) {
   foreach ($matches as $key => $value) {
      if ($value == $name) {
         return $matches[intval($key) + 1];
      }
   }
   return '';
}

function getAttributeDelimiter(array $matches, $name) {
   foreach ($matches as $key => $value) {
      if ($value == $name) {
         return $matches[intval($key) - 1];
      }
   }
   return ' ';
}

foreach ($files as $file) {

   $content = file_get_contents($file);

   $content = preg_replace_callback($prepareAddTaglib, function ($matches) {
      return '<' . $matches[1] . ':addtaglib' . $matches[2]
      . 'namespace="' . getAttributeValue($matches, 'namespace') . '"' . getAttributeDelimiter($matches, 'namespace')
      . 'class="' . getAttributeValue($matches, 'class') . '"' . getAttributeDelimiter($matches, 'class')
      . 'prefix="' . getAttributeValue($matches, 'prefix') . '"' . getAttributeDelimiter($matches, 'prefix')
      . 'name="' . getAttributeValue($matches, 'name') . '"'
      . $matches[14] . '/>';
   }, $content);

   file_put_contents($file, $content);
}