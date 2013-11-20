<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*pager.ini');

$searchStatement = '#Pager\.StatementNamespace ?= ?"([A-Za-z0-9:\-]+)"#';
$searchTemplate = '#Pager\.DesignNamespace ?= ?"([A-Za-z0-9:\-]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // migrate statement file namespace
   $content = preg_replace_callback($searchStatement, function ($matches) {
      return 'Pager.StatementNamespace = "APF\\' . str_replace('::', '\\', $matches[1]) . '"';
   }, $content);

   // migrate template file namespace
   $content = preg_replace_callback($searchTemplate, function ($matches) {
      return 'Pager.DesignNamespace = "APF\\' . str_replace('::', '\\', $matches[1]) . '"';
   }, $content);

   // migrate statement parameters configuration:
   // - Pager.CountStatement.Params + Pager.EntriesStatement.Params => Pager.StatementParameters
   // - content "copied" from Pager.EntriesStatement.Params
   $content = preg_replace('#Pager.CountStatement.Params ?= ?"(.+)"#', '', $content);
   $content = str_replace('Pager.EntriesStatement.Params', 'Pager.StatementParameters', $content);

   file_put_contents($file, $content);
}