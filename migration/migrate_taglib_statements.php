<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.html'));

$searchAddTaglib = '#<([A-Za-z0-9\-]+):addtaglib([\*| |\n|\r\n]+)namespace ?= ?"([A-Za-z0-9:\-]+)"([\*| |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"#';

$searchSingleNamespace = '#<([A-Za-z0-9\-]+):importdesign([\*| |\n|\r\n]+)namespace="([A-Za-z0-9:\-_]+)"#';

$searchGetStringNamespace = '#<([A-Za-z0-9\-]+):getstring([\*| |\n|\r\n]+)namespace="([A-Za-z0-9:\-_]+)"#';

$searchAppendNodeNamespace = '#<([A-Za-z0-9\-]+):appendnode([\*| |\n|\r\n]+)namespace="([A-Za-z0-9:\-_]+)"#';

$searchHeaderJs = '#<htmlheader:addjs([\*| |\n|\r\n]+)namespace="([A-Za-z0-9:\-_]+)"#';
$searchHeaderCss = '#<htmlheader:addcss([\*| |\n|\r\n]+)namespace="([A-Za-z0-9:\-_]+)"#';

// respect explicit and none-explicit calls (explicits first!)
$searchAddValidatorExplicit = '#<([A-Za-z0-9\-]+):addvalidator([ |\n|\r\n]+)namespace ?= ?"([A-Za-z0-9:\-]+)"([\*| |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"#';
$searchAddValidator = '#<([A-Za-z0-9\-]+):addvalidator([ |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"#';
$searchAddFilterExplicit = '#<([A-Za-z0-9\-]+):addfilter([ |\n|\r\n]+)namespace ?= ?"([A-Za-z0-9:\-]+)"([\*| |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"#';
$searchAddFilter = '#<([A-Za-z0-9\-]+):addfilter([ |\n|\r\n]+)class ?= ?"([A-Za-z0-9\-_]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace <*:addtaglib /> calls
   $content = preg_replace_callback($searchAddTaglib, function ($matches) {
      return '<' . $matches[1] . ':addtaglib' . $matches[2] . 'class="APF\\' . str_replace('::', '\\', $matches[3]) . '\\' . $matches[5] . '"';
   }, $content);

   // replace <*:importdesign /> calls
   $content = preg_replace_callback($searchSingleNamespace, function ($matches) {
      return '<' . $matches[1] . ':importdesign' . $matches[2] . 'namespace="APF\\' . str_replace('::', '\\', $matches[3]) . '"';
   }, $content);

   // replace <*:getstring /> calls
   $content = preg_replace_callback($searchGetStringNamespace, function ($matches) {
      return '<' . $matches[1] . ':getstring' . $matches[2] . 'namespace="APF\\' . str_replace('::', '\\', $matches[3]) . '"';
   }, $content);

   // replace <*:appendnode /> calls
   $content = preg_replace_callback($searchAppendNodeNamespace, function ($matches) {
      return '<' . $matches[1] . ':appendnode' . $matches[2] . 'namespace="APF\\' . str_replace('::', '\\', $matches[3]) . '"';
   }, $content);

   // replace <htmlheader:add* /> calls
   $content = preg_replace_callback($searchHeaderJs, function ($matches) {
      return '<htmlheader:addjs' . $matches[1] . 'namespace="APF\\' . str_replace('::', '\\', $matches[2]) . '"';
   }, $content);
   $content = preg_replace_callback($searchHeaderCss, function ($matches) {
      return '<htmlheader:addcss' . $matches[1] . 'namespace="APF\\' . str_replace('::', '\\', $matches[2]) . '"';
   }, $content);

   // replace explicit addvalidator
   $content = preg_replace_callback($searchAddValidatorExplicit, function ($matches) {
      return '<' . $matches[1] . ':addvalidator' . $matches[2] . 'class="APF\\' . str_replace('::', '\\', $matches[3]) . '\\' . $matches[5] . '"';
   }, $content);

   // replace none-explicit addvalidator
   $content = preg_replace_callback($searchAddValidator, function ($matches) {
      return '<' . $matches[1] . ':addvalidator' . $matches[2] . 'class="APF\tools\form\validator\\' . $matches[3] . '"';
   }, $content);

   // replace explicit addfilter
   $content = preg_replace_callback($searchAddFilterExplicit, function ($matches) {
      return '<' . $matches[1] . ':addfilter' . $matches[2] . 'class="APF\\' . str_replace('::', '\\', $matches[3]) . '\\' . $matches[5] . '"';
   }, $content);

   // replace none-explicit addfilter
   $content = preg_replace_callback($searchAddFilter, function ($matches) {
      return '<' . $matches[1] . ':addfilter' . $matches[2] . 'class="APF\tools\form\filter\\' . $matches[3] . '"';
   }, $content);

   file_put_contents($file, $content);
}