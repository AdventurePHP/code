<?php
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;

include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$requestResponseTrait = 'use GetRequestResponse;';

$search = '#new Session\(\'(.+)\'\)#U';
$searchSessionId = '#\$(.+)\->getSessionID\(\)#U';

// gather APF installation path and setup class loader for later code analysis
include(dirname(dirname(__FILE__)) . '/core/bootstrap.php');

\APF\core\exceptionhandler\GlobalExceptionHandler::disable();
\APF\core\errorhandler\GlobalErrorHandler::disable();

foreach ($files as $file) {

   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, 'new Session(') === false && strpos($content, '->getSessionID()') === false) {
      continue;
   }

   // session construction  --------------------------------------------------------------------------------------------
   preg_match_all($search, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {
      $content = str_replace($match[0], 'self::getRequest()->getSession(\'' . $match[1] . '\')', $content);
   }

   // session id query -------------------------------------------------------------------------------------------------
   preg_match_all($searchSessionId, $content, $matches, PREG_SET_ORDER);

   foreach ($matches as $match) {
      $content = str_replace($match[0], 'self::getRequest()->getSessionId()', $content);
   }

   // check on presence of self::getRequest() --------------------------------------------------------------------------
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

      try {
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
      } catch (Exception $e) {
         echo '  Error while migrating file ' . $file . '. Maybe manual interaction required. Details: '
               . $e->getMessage() . PHP_EOL;
      }
   }

   // rewrite namespace mapping
   $content = str_replace('use APF\core\session\Session;', 'use APF\core\http\Session;', $content);

   file_put_contents($file, $content);

}
