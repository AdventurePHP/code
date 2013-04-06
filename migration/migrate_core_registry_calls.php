<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#Registry::(register|retrieve)\(\'apf::core\'#';

$searchAll = '#Registry::(register|retrieve)\(\'([A-Za-z0-9:\-]+)\',#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace internal registry calls
   $content = preg_replace_callback($search, function ($matches) {
      return 'Registry::' . $matches[1] . '(\'APF\core\'';
   }, $content);

   // replace general registry calls
   $content = preg_replace_callback($searchAll, function ($matches) {
      return 'Registry::' . $matches[1] . '(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\',';
   }, $content);

   file_put_contents($file, $content);
}
