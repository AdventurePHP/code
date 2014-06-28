<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$tagLibSubPattern = '([ |\n|\r\n]*)new TagLib\(([ |\n|\r\n]*)\'([A-Za-z0-9\\\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\'([ |\n|\r\n]*)\)';

$search = '#\$this\->tagLibs\[\]([ |\n|\r\n]*)=' . $tagLibSubPattern . ';#m';

$searchArray = '#\$this\->tagLibs([ |\n|\r\n]*)=([ |\n|\r\n]*)(array\(|\[)(.*)(\)|\]);#msU';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'self::addTagLib(new TagLib(\'' . $matches[4] . '\', \'' . $matches[6] . '\', \'' . $matches[8] . '\'));';
   }, $content);

   preg_match_all($searchArray, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      preg_match_all('#' . $tagLibSubPattern . '#', $match[4], $tagLibMatches, PREG_SET_ORDER);

      $tags = array();

      foreach ($tagLibMatches as $tagLibMatch) {
         $tags[] = 'self::addTagLib(new TagLib(\'' . $tagLibMatch[3] . '\', \'' . $tagLibMatch[5] . '\', \'' . $tagLibMatch[7] . '\'));';
      }

      $content = str_replace($match[0], implode(PHP_EOL, $tags), $content);

   }

   file_put_contents($file, $content);
}
