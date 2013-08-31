<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*recipients.ini');

$searchSection = '#\[Kontakt ([0-9]+)\]#';
$searchRecipientName = '#EmpfaengerName ?= ?"(.+)"#';
$searchRecipientAddress = '#EmpfaengerAdresse ?= ?"(.+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // migrate section naming
   $content = preg_replace_callback($searchSection, function ($matches) {
      return '[Contact ' . $matches[1] . ']';
   }, $content);

   // migrate name
   $content = preg_replace_callback($searchRecipientName, function ($matches) {
      return 'recipient-name = "' . $matches[1] . '"';
   }, $content);

   // migrate address
   $content = preg_replace_callback($searchRecipientAddress, function ($matches) {
      return 'recipient-address = "' . $matches[1] . '"';
   }, $content);

   file_put_contents($file, $content);
}