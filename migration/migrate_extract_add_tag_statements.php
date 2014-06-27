<?php
// Collect all <*:addtaglib /> tags, remove them from the templates, and suggest index.php
// statements to add for custom tags.

use APF\core\pagecontroller\TagLib;
use APF\core\pagecontroller\XmlParser;

include(dirname(__FILE__) . '/migrate_base.php');
include(dirname(dirname(__FILE__)) . '/core/pagecontroller/XmlParser.php');
include(dirname(dirname(__FILE__)) . '/core/pagecontroller/TagLib.php');
include(dirname(dirname(__FILE__)) . '/core/bootstrap.php');

\APF\core\exceptionhandler\GlobalExceptionHandler::disable();
\APF\core\errorhandler\GlobalErrorHandler::disable();

$files = find('.', '*.html');

$search = '#<([A-Za-z\-_]+):addtaglib([ |\n|\r\n|\r]+)(.*)/>#smU';

$class = new ReflectionClass('APF\core\pagecontroller\Document');
$property = $class->getProperty('knownTags');
$property->setAccessible(true);

$registeredTags = $property->getValue();

function isRegistered(array $registeredTags, $class, $prefix, $name) {
   /* @var $tag TagLib */
   foreach ($registeredTags as $tag) {
      if ($tag->getClass() === $class && $tag->getPrefix() === $prefix && $tag->getName() === $name) {
         return true;
      }
   }

   return false;
}

foreach ($files as $file) {
   $content = file_get_contents($file);

   preg_match_all($search, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {
      $attributes = XmlParser::getAttributesFromString(trim($match[3]));

      if (isRegistered($registeredTags, $attributes['class'], $attributes['prefix'], $attributes['name'])) {
         $content = str_replace($match[0], '', $content);
      }

   }

   file_put_contents($file, $content);
}
