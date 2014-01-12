<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#import\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\'([ |\n|\r\n]*)\);#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // check for file-based namespace declaration
   if (!preg_match('#^namespace ([A-Za-z0-9\\\\]+)#', $content)) {

      $namespace = str_replace('.', '', str_replace('/', '\\', $file));
      $namespace = str_replace('\\\\', '\\', 'APF\\' . substr($namespace, 0, strrpos($namespace, '\\')));

      $content = str_replace('<?php', '<?php' . "\n" . 'namespace ' . $namespace . ';' . "\n", $content);
   }

   $content = preg_replace_callback($search, function ($matches) {
      return 'use APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . ';';
   }, $content);

   file_put_contents($file, $content);
}