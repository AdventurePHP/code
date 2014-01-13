<?php
include(dirname(__FILE__) . '/migrate_base.php');

// Resolve APF class usages within application code ////////////////////////////////////////////////////////////////////
$files = filterApplicationDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));
addUseStatements($files, $classMap);

// Resolve application class usages within application code ////////////////////////////////////////////////////////////
$files = filterApfDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));
addUseStatements($files, $classMap);

// special classes from PHP SPL that are often used
$splClassMap = array(
   'DateTime' => 'DateTime',
   'DateInterval' => 'DateInterval',
   'InvalidArgumentException' => 'InvalidArgumentException',
   'DOMDocument' => 'DOMDocument',
   'Exception' => 'Exception',
   'stdClass' => 'stdClass',
   'UnexpectedValueException' => 'UnexpectedValueException',
   'SoapFault' => 'SoapFault'
);
addUseStatements($files, $splClassMap);
