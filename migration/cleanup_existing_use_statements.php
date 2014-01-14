<?php
include(dirname(__FILE__) . '/migrate_base.php');

// Remove existing use statements to not have issues with e.g.
//
// use APF\core\frontcontroller\Frontcontroller;
// use Frontcontroller;
//
// combinations.
function removeSimpleUseStatements(array $files, array $classMap) {
   foreach ($files as $file) {
      $content = file_get_contents($file);

      foreach ($classMap as $key => $value) {
         // $key: Frontcontroller
         // $value: APF\core\frontcontroller\Frontcontroller

         // remove existing use statements for classes without namespaces
         $content = str_replace('use ' . $key . ';', '', $content);

         // remove method type declarations for classes without namespace
         // 1) "@return \FooClass"
         $content = str_replace('@return \\' . $key, '@return ' . $key, $content);
         // 2) "@param \CommonJSONClient $client"
         $content = str_replace('@param \\' . $key . ' ', '@param ' . $key . ' ', $content);
         // 3) "@var $fC \Frontcontroller" --> inline statements
         $content = preg_replace('#@var \$([A-Za-z_]+) \\\\' . $key . ' #', '@var \$$1 ' . $key . ' ', $content);
         // 4) "@var \Frontcontroller" --> class variables
         $content = str_replace('@var \\' . $key, '@var ' . $key, $content);

         // clean up static calls for classes without namespaces
         // e.g. $fC = \Singleton::getInstance(...);
         $content = str_replace('\\' . $key . '::', $key . '::', $content);

      }

      file_put_contents($file, $content);
   }
}

// Resolve APF class usages within application code ////////////////////////////////////////////////////////////////////
$files = filterApplicationDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));
removeSimpleUseStatements($files, $classMap);

// Resolve application class usages within application code ////////////////////////////////////////////////////////////
$classMap = getClassMap($files);

// apply class map to entire code
removeSimpleUseStatements($files, $classMap);