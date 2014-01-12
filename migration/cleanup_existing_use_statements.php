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

         // remove extends/implements for classes without namespaces (e.g. " extends \APFObject {")
         $content = str_replace(' extends \\' . $key . ' ', ' extends ' . $key . ' ', $content);
         $content = str_replace(' implements \\' . $key . ' ', ' implements ' . $key . ' ', $content);

         // remove method type declarations for classes without namespace
         // "@return \FooClass "
         // "@param \CommonJSONClient "
         $content = str_replace('@return \\' . $key . ' ', '@return ' . $key . ' ', $content);
         $content = str_replace('@param \\' . $key . ' ', '@param ' . $key . ' ', $content);

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
$files = filterApfDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));
removeSimpleUseStatements($files, $classMap);