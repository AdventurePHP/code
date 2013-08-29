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


// resolve some weired stuff