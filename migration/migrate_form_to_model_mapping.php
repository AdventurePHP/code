<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

// replace methods in tags and document controller...
$addMethod = '::addFormValueMapper(';
$clearMethod = '::clearFormValueMappers(';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, $addMethod) === false || strpos($content, $clearMethod) === false) {
      continue;
   }

   $content = str_replace($addMethod, '::addFormControlToModelMapper(', $content);
   $content = str_replace($clearMethod, '::clearFormControlToModelMappers(', $content);

   file_put_contents($file, $content);

}
