<?php
include(dirname(__FILE__) . '/migrate_base.php');

function sanitizeNamespace($namespace) {
   return preg_replace('/[^A-Za-z0-9\\\\]/', '', $namespace);
}

function checkNamespace($namespace) {
   return preg_match('/^[A-Z]+$/', $namespace) // vendor only
   || preg_match('/^[A-Za-z0-9\\\\]+$/', $namespace); // vendor and sub-namespace
}

// gather necessary parameters
$sourceNamespace = $argv[1];
$targetNamespace = $argv[2];

// sanitize namespaces
$sourceNamespace = sanitizeNamespace($sourceNamespace);
$targetNamespace = sanitizeNamespace($targetNamespace);

// check for correct escaping, meaning that from and to namespace contain backslashes
if (!checkNamespace($sourceNamespace)) {
   echo '--> Source namespace not well-formed ("' . $sourceNamespace . '") ... ';
   exit(1);
}
if (!checkNamespace($targetNamespace)) {
   echo '--> Target namespace not well-formed ("' . $targetNamespace . '") ... ';
   exit(1);
}

// show namespaces
echo PHP_EOL . 'Source namespace: ' . $sourceNamespace . PHP_EOL;
echo 'Target namespace: ' . $targetNamespace . PHP_EOL . PHP_EOL;

$sourcePath = str_replace('\\', '/', $sourceNamespace);
$targetPath = str_replace('\\', '/', $targetNamespace);

// strip first path part as this is the VENDOR (source always has sub-namespace since it is located under "APF")
$realSourcePath = substr($sourcePath, strpos($sourcePath, '/') + 1);

// assemble target path as it will be in parallel to this directory (at least for a start)
$realTargetPath = '../' . $targetPath;

// gather application files on
$files = filterApfDirectories(find($realSourcePath, '*'));

// create target path if not existing
$sourceDirPermissions = fileperms($realSourcePath);
if (file_exists($realTargetPath)) {
   // display warning that files may be overwritten
   echo '--> WARNING: Target path already exists! Files may be overwritten ... ';
} else {
   if (!mkdir($realTargetPath, $sourceDirPermissions, true)) {
      echo '--> Failed to create target path "' . $realTargetPath . '" ... ';
      exit(1);
   }
}

echo '* Copy files to target structure ...' . PHP_EOL;
foreach ($files as $file) {
   // skip directories as we create them on the go on the target side
   if (!is_dir($file)) {
      $targetFile = preg_replace('#^' . $realSourcePath . '#', $realTargetPath, $file);

      // lazily create directory since copy() doesn't do that for us
      $dir = dirname($targetFile);
      if (!file_exists($dir)) {
         mkdir($dir, $sourceDirPermissions, true);
      }
      copy($file, $targetFile);
   }

}

// scan target files and re-map namespace
$files = find($realTargetPath, '*');

echo '* Re-mapping namespace on target ... ' . PHP_EOL;
foreach ($files as $file) {
   // skip directories as we only map namespaces in files
   if (!is_dir($file)) {
      $content = file_get_contents($file);
      $content = str_replace($sourceNamespace, $targetNamespace, $content);
      file_put_contents($file, $content);
   }
}

// Display hint on config
$sourceVendor = substr($sourceNamespace, 0, strpos($sourceNamespace, '\\'));
$separatorPos = strpos($targetNamespace, '\\');
if ($separatorPos === false) {
   $targetVendor = $targetNamespace;
} else {
   $targetVendor = substr($targetNamespace, 0, $separatorPos);
}

$configPath = getcwd() . '/config';
if (file_exists($configPath)) {
   echo PHP_EOL . '######################################' . PHP_EOL . PHP_EOL;
   echo 'NOTE: Please note, that relocate.sh does not handle relocation of configuration files. Thus, please revise folder "'
         . realpath($configPath) . '" and extract configuration files for vendor "' . $sourceVendor . '" to new vendor "'
         . $targetVendor . '" as desired!' . PHP_EOL;
}

// Display hint on class loader registration
$targetVendorRootPath = realpath('../' . $targetVendor);

echo PHP_EOL . '######################################' . PHP_EOL . PHP_EOL;
echo 'NOTE: Please be sure to add a new class loader configuration for new vendor "'
      . $targetVendor . '" within your bootstrap file (index.php). You may want to use the following as a start: ';
echo PHP_EOL . PHP_EOL;
echo 'use APF\core\loader\RootClassLoader;' . PHP_EOL;
echo 'use APF\core\loader\StandardClassLoader;' . PHP_EOL;
echo 'RootClassLoader::addLoader(new StandardClassLoader(\'' . $targetVendor . '\', \'' . $targetVendorRootPath . '\'));' . PHP_EOL;

exit(0);
