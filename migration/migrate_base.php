<?php
function find($dir, $pattern) {
   // escape any character in a string that might be used to trick
   // a shell command into executing arbitrary commands
   $dir = escapeshellcmd($dir);
   // get a list of all matching files in the current directory
   $files = glob($dir . '/' . $pattern);
   // find a list of all directories in the current directory
   // directories beginning with a dot are also included
   foreach (glob($dir . '/{.[^.]*,*}', GLOB_BRACE | GLOB_ONLYDIR) as $sub_dir) {
      $arr = find($sub_dir, $pattern); // recursive call
      $files = array_merge($files, $arr); // merge array with files from subdirectory
   }
   return $files;
}

function filterApfDirectories($files) {
   $filteredFiles = array();

   foreach ($files as $file) {
      if (preg_match('#\.[/|\\\\](core|modules|tools|tests|extensions|examples|migration)[/|\\\\]#', $file)) {
         continue;
      }
      $filteredFiles[] = $file;
   }
   return $filteredFiles;
}

function filterApplicationDirectories($files) {
   $filteredFiles = array();

   foreach ($files as $file) {
      if (preg_match('#\.[/|\\\\](core|modules|tools|extensions)[/|\\\\]#', $file)) {
         $filteredFiles[] = $file;
      }
   }
   return $filteredFiles;
}

function addUseStatement($content, $class) {

   // rely on class being defined only once (what is normally true having projects without using namespaces)
   $simpleClass = substr($class, strrpos($class, '\\') + 1);

   // this condition may lead to unused use statements that we accept over missing statements!
   if (strpos($content, 'use ' . $class . ';') === false && strpos($content, 'class ' . $simpleClass) === false) {
      // search for first use statement (but with line break in front to avoid interference with code comments)
      $use = strpos($content, "\n" . 'use ');
      if ($use !== false) {
         // since we do a preg_replace() only the first occurrence is affected and thus replaces.
         // probably not the best way, but it works.
         $use = $use + 1; // shift line break
         $semicolon = strpos($content, ';', $use);
         $length = $semicolon - $use + 1;
         $currentUse = substr($content, $use, $length);
         $content = substr_replace($content, $currentUse . "\n" . 'use ' . $class . ';', $use, $length);
      } else {
         // if there is no use defined yet, add it below the namespace definition
         $content = preg_replace('#namespace ([A-Za-z0-9\\\\-]+);#', 'namespace $1;' . "\n" . "\n" . 'use ' . $class . ';', $content);
      }
   }
   return $content;
}
