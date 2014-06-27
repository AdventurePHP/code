<?php
// collect all <*:addtaglib /> tags, remove them from the templates, and suggest index.php statements to add

// examples:
/*
<core:addtaglib class="APF\tools\form\taglib\HtmlFormTag" prefix="html" name="form"/>
<core:addtaglib
      class="APF\modules\guestbook2009\pres\taglib\GuestbookLanguageLabelTag"
      prefix="html"
      name="langlabel"
            />
*/

use APF\core\pagecontroller\TagLib;
use APF\core\pagecontroller\XmlParser;

include(dirname(__FILE__) . '/migrate_base.php');
include(dirname(dirname(__FILE__)) . '/core/pagecontroller/XmlParser.php');
include(dirname(dirname(__FILE__)) . '/core/pagecontroller/TagLib.php');
include(dirname(dirname(__FILE__)) . '/core/bootstrap.php');

\APF\core\exceptionhandler\GlobalExceptionHandler::disable();
\APF\core\errorhandler\GlobalErrorHandler::disable();

$files = find('.', '*.html');

// ([ |\n|\r\n]*)
$search = '#<([A-Za-z\-_]+):addtaglib([ |\n|\r\n]*)(.+)/>#mU';

//=([ |\n|\r\n]*)new TagLib\(\'([A-Za-z0-9\\\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\'([ |\n|\r\n]*)\)#m';

$tags = array();

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
         echo '- Removing registration of tag <' . $attributes['prefix'] . ':' . $attributes['name'] . ' /> from ' . $file . '.' . PHP_EOL;
      } else {
         // register with prefix and name to unique selection
         $tags[$attributes['prefix'] . ':' . $attributes['name']] = $attributes;
      }

   }

}

echo PHP_EOL . PHP_EOL . '************************************************************' . PHP_EOL;
echo 'Recommended to add the following tags within your bootstrap filed (e.g. index.php):' . PHP_EOL;

foreach ($tags as $tag) {
   echo 'Document::addTagLib(new TagLib(\'' . $tag['class'] . '\', \'' . $tag['prefix'] . '\', \'' . $tag['name'] . '\'));' . PHP_EOL;
}

// remove tags that have been statically registered (fetch tags from Document) and suggest other tags can be registered...
