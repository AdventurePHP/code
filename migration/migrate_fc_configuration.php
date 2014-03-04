<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*actionconfig.ini');

foreach ($files as $file) {
   $content = file_get_contents($file);

   // remove "FC." prefix from all entries
   $content = str_replace('FC.ActionClass', 'ActionClass', $content);
   $content = str_replace('FC.InputClass', 'InputClass', $content);
   $content = str_replace('FC.InputParams', 'InputParams', $content);
   $content = str_replace('FC.ActionServiceNamespace', 'ActionServiceNamespace', $content);
   $content = str_replace('FC.ActionServiceName', 'ActionServiceName', $content);

   file_put_contents($file, $content);
}
