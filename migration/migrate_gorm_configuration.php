<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*domainobjects.ini');

$search = '#Namespace ?= ?"([A-Za-z0-9:\-_]+)"([\n|\r\n]+)?Class ?= ?"([A-Za-z0-9\-]+)"#';

$searchBaseClass = '#Base.Namespace ?= ?"([A-Za-z0-9:\-_]+)"([\n|\r\n]+)?Base.Class ?= ?"([A-Za-z0-9\-]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace domain object class
   $content = preg_replace_callback($search, function ($matches) {
      return 'Class = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   // replace base class definition
   $content = preg_replace_callback($searchBaseClass, function ($matches) {
      return 'Base.Class = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   file_put_contents($file, $content);
}