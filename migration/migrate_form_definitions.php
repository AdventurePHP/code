<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.html');

$searchFormTag = '#<html:form(.+)>(.+)</html:form>#msU';
$searchContentButtons = '#(<form:button|<form:imagebutton)(.+)name ?= ?"(.+)"(.*)(</form:button>|</form:imagebutton>)#msU';
$searchButtons = '#<form:(button|imagebutton)(.+)name ?= ?"(.+)"(.*)/>#msU';
$searchObserver = '#<form:(addvalidator|addfilter)(.+)button ?= ?"(.+)"(.+)/>#msU';

/**
 * @param string $content The content of the file.
 * @param array $buttons The list of buttons within the current form.
 * @param string $buttonName The name of the button currently processed.
 * @param string[] $observerAttributes Tag position and tag content to relocate (addvalidator or addfilter).
 *
 * @return string The updated content.
 */
function relocateObserver($content, array $buttons, $buttonName, $observerAttributes) {

   // skip definitions in case button is not defined (should result in an error anyway)
   if (!isset($buttons[$buttonName])) {
      return $content;
   }

   if ($observerAttributes['pos'] < $buttons[$buttonName]['pos']) {
      // remove old occurrence ...
      $content = str_replace($observerAttributes['tag'], '', $content);

      // ... and add it after respective button definition
      $content = str_replace($buttons[$buttonName]['tag'], $buttons[$buttonName]['tag'] . PHP_EOL . $observerAttributes['tag'], $content);
   }

   return $content;
}

foreach ($files as $file) {
   $content = file_get_contents($file);

   // skip files without form tag
   if (!preg_match_all($searchFormTag, $content, $formMatches, PREG_SET_ORDER)) {
      continue;
   }

   $buttons = array();
   $filters = array();
   $validators = array();
   foreach ($formMatches as $formMatch) {

      // First search for explicit closing buttons, then for implicit-closing ones.
      // This is necessary as reg exps are not made for parsing XML!

      // search position of all content buttons
      $currentFormContent = $formMatch[2];
      if (preg_match_all($searchContentButtons, $currentFormContent, $buttonMatches, PREG_SET_ORDER)) {
         foreach ($buttonMatches as $buttonMatch) {
            $pos = strpos($currentFormContent, $buttonMatch[0]);
            $buttons[$buttonMatch[3]] = array(
                  'pos' => $pos,
                  'tag' => $buttonMatch[0]
            );
         }
      }

      // search position of all buttons
      if (!preg_match_all($searchButtons, $currentFormContent, $buttonMatches, PREG_SET_ORDER)) {
         continue;
      }

      foreach ($buttonMatches as $buttonMatch) {
         if (isset($buttons[$buttonMatch[3]])) {
            // avoid overwriting of explicitly closing buttons with their implicit-closing pendents
            continue;
         }
         $pos = strpos($currentFormContent, $buttonMatch[0]);
         $buttons[$buttonMatch[3]] = array(
               'pos' => $pos,
               'tag' => $buttonMatch[0]
         );
      }

      // search all <form:addvalidator /> and <form:addfilter /> tags
      if (!preg_match_all($searchObserver, $currentFormContent, $observerMatches, PREG_SET_ORDER)) {
         continue;
      }

      foreach ($observerMatches as $observerMatch) {
         $pos = strpos($currentFormContent, $observerMatch[0]);
         if ($observerMatch[1] == 'addfilter') {
            $filters[$observerMatch[3]][] = array(
                  'pos' => $pos,
                  'tag' => $observerMatch[0]
            );
         } else {
            $validators[$observerMatch[3]][] = array(
                  'pos' => $pos,
                  'tag' => $observerMatch[0]
            );
         }
      }

      // match positions and re-arrange validators if necessary
      foreach ($validators as $buttonName => $tags) {
         // reverse list to preserve initial order
         foreach (array_reverse($tags) as $tag) {
            $content = relocateObserver($content, $buttons, $buttonName, $tag);
         }
      }

      // match positions and re-arrange filters if necessary (filters first, thus last here!)
      foreach ($filters as $buttonName => $tags) {
         // reverse list to preserve initial order
         foreach (array_reverse($tags) as $tag) {
            $content = relocateObserver($content, $buttons, $buttonName, $tag);
         }
      }

   }

   file_put_contents($file, $content);
}
