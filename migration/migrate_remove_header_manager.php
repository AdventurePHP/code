<?php
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;

include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$requestResponseTrait = 'use GetRequestResponse;';

$searchSendFull = '#HeaderManager::send\(([ |\n|\r\n]*)(.+)([ |\n|\r\n]*),([ |\n|\r\n]*)(true|false)([ |\n|\r\n]*),([ |\n|\r\n]*)(true|false|[0-9]+)([ |\n|\r\n]*)\);#msU';
$searchSendReplace = '#HeaderManager::send\(([ |\n|\r\n]*)(.+)([ |\n|\r\n]*),([ |\n|\r\n]*)(true|false)([ |\n|\r\n]*)\);#msU';
$searchSend = '#HeaderManager::send\(([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\);#msU';

// gather APF installation path and setup class loader for later code analysis
include(dirname(dirname(__FILE__)) . '/core/bootstrap.php');

\APF\core\exceptionhandler\GlobalExceptionHandler::disable();
\APF\core\errorhandler\GlobalErrorHandler::disable();

function getHeaderString($string) {

   if (substr($string, 0, 1) == '\'' || substr($string, 0, 1) == '"') {

      $colon = strpos($string, ':');
      if ($colon === false) {
         return null;
      }

      $headerName = trim(substr($string, 1, $colon - 1));
      $headerContent = str_replace('\' ', '\'',
            str_replace('" ', '"',
                  str_replace($headerName . ':', '', $string)
            )
      );

      return 'new HeaderImpl(\'' . $headerName . '\', ' . $headerContent . ')';
   }

   return null;
}

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, 'HeaderManager') === false) {
      continue;
   }

   // forward() --------------------------------------------------------------------------------------------------------
   $content = str_replace('HeaderManager::forward(', 'self::getResponse()->forward(', $content);

   // redirect() -------------------------------------------------------------------------------------------------------
   $content = str_replace('HeaderManager::redirect(', 'self::getResponse()->redirect(', $content);

   // sendNotFound() ---------------------------------------------------------------------------------------------------
   $content = str_replace('HeaderManager::sendNotFound(', 'self::getResponse()->sendNotFound(', $content);

   // sendServerError() ------------------------------------------------------------------------------------------------
   $content = str_replace('HeaderManager::sendServerError(', 'self::getResponse()->sendServerError(', $content);

   // send() -----------------------------------------------------------------------------------------------------------
   preg_match_all($searchSendFull, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      $headerString = getHeaderString($match[2]);
      if ($headerString === null) {
         continue;
      }

      $content = str_replace(
            $match[0],
            'self::getResponse()->setStatusCode(' . $match[8] . ')->setHeader(' . $headerString . ');',
            $content
      );

      // add use statement in case we don't have one
      $content = addUseStatement($content, 'APF\core\http\HeaderImpl');
   }

   preg_match_all($searchSendReplace, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      $headerString = getHeaderString($match[2]);
      if ($headerString === null) {
         continue;
      }

      $content = str_replace($match[0], 'self::getResponse()->setHeader(' . $headerString . ');', $content);

      // add use statement in case we don't have one
      $content = addUseStatement($content, 'APF\core\http\HeaderImpl');
   }

   preg_match_all($searchSend, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {

      $headerString = getHeaderString($match[2]);
      if ($headerString === null) {
         continue;
      }

      $content = str_replace($match[0], 'self::getResponse()->setHeader(' . $headerString . ');', $content);

      // add use statement in case we don't have one
      $content = addUseStatement($content, 'APF\core\http\HeaderImpl');
   }

   // check on presence of self::getResponse() -------------------------------------------------------------------------
   // - is use use GetRequestResponse; present --> ok
   if (strpos($content, $requestResponseTrait) !== false) {
      file_put_contents($file, $content);
      continue;
   }

   // - is class that derives from Document, AbstractFrontcontrollerAction, BaseDocumentController, AbstractFormValidator
   preg_match('#^(abstract |final )?class ([A-Za-z0-9]+) (\{|extends|implements)#m', $content, $matchesClass);
   preg_match('#namespace ([A-Za-z0-9\\\\]+);#', $content, $matchesNamespace);

   if (!empty($matchesClass[2]) && !empty($matchesNamespace[1])) {

      // auto-register custom class loader if necessary
      $vendor = RootClassLoader::getVendor($matchesNamespace[1] . '\\' . $matchesClass[2]);
      try {
         RootClassLoader::getLoaderByVendor($vendor);
      } catch (InvalidArgumentException $e) {
         $folder = str_replace('\\', '/', realpath(dirname($file)));
         $namespaceAsPath = str_replace('\\', '/', RootClassLoader::getNamespaceWithoutVendor($matchesNamespace[1] . '\\' . $matchesClass[2]));
         $basePath = str_replace($namespaceAsPath, '', $folder);

         RootClassLoader::addLoader(new StandardClassLoader($vendor, $basePath));
      }

      $class = new ReflectionClass($matchesNamespace[1] . '\\' . $matchesClass[2]);
      if (
            $class->isSubclassOf('APF\core\pagecontroller\Document')
            || $class->isSubclassOf('APF\core\pagecontroller\BaseDocumentController')
            || $class->isSubclassOf('APF\core\frontcontroller\AbstractFrontcontrollerAction')
            || $class->isSubclassOf('APF\tools\form\validator\AbstractFormValidator')
      ) {
      } else {
         $content = addUseStatement($content, 'APF\core\http\mixins\GetRequestResponse');

         // add trait use second to not mess up with addUseStatement() logic
         $content = preg_replace('#class ' . $matchesClass[2] . ' (.*){#',
               'class ' . $matchesClass[2] . ' \\1{' . "\n\n" . '   ' . $requestResponseTrait,
               $content
         );
      }
   }

   // remove header manager uses
   $content = str_replace('use APF\tools\http\HeaderManager;', '', $content);

   file_put_contents($file, $content);

}
