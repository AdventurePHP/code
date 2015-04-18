<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#->getServiceObject\((.+)\)(;|\->)#msU';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, '$this->getServiceObject(') === false) {
      continue;
   }

   // gather all service manager calls
   preg_match_all($search, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      $arguments = explode(',', $match[1]);
      $count = count($arguments);

      // Only migrate calls with 2 or three arguments as the second one
      // with the new signature has already a default value!
      if ($count === 2) {
         preg_match('#([ |\n|\r\n]*)(.+)([ |\n|\r\n]*),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)#ms', $match[1], $argumentList);

         // avoid double migration...
         if (substr($argumentList[5], 0, 1) == '[' || substr($argumentList[5], 0, 6) == 'array(') {
            continue;
         }

         $content = str_replace($argumentList[0],
               $argumentList[1] . $argumentList[2] . $argumentList[3] . ','
               . $argumentList[4] . '[],'
               . $argumentList[4] . $argumentList[5] . $argumentList[6],
               $content
         );
      } else if ($count === 3) {
         preg_match('#([ |\n|\r\n]*)(.+)([ |\n|\r\n]*),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)#ms', $match[1], $argumentList);

         // avoid double migration...
         if (substr($argumentList[5], 0, 1) == '[' || substr($argumentList[5], 0, 6) == 'array(') {
            continue;
         }

         $content = str_replace($argumentList[0],
               $argumentList[1] . $argumentList[2] . $argumentList[3] . ','
               . $argumentList[4] . '[],'
               . $argumentList[4] . $argumentList[5] . $argumentList[6] . ','
               . $argumentList[7] . $argumentList[8] . $argumentList[9],
               $content
         );
      }

   }

   file_put_contents($file, $content);

}
