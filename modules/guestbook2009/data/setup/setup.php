<?php
/**
 * In order to use this script for database setup, include this file after including the
 * <em>pagecontroller.php</em> file and setting the context and connection key.
 *
 * @example
 * <code>
 * include('./apps/core/pagecontroller/pagecontroller.php');
 * $context = 'mycontext';
 * $connectionKey = 'mysql-db';
 * include('./apps/modules/guestbook2009/data/setup/setup.php');
 * </code>
 *
 * Please note, that you may have to adapt the include path for the <em>pagecontroller.php</em>.
 */
import('modules::genericormapper::data::tools', 'GenericORMapperSetup');
$setupMapper = new GenericORMapperSetup();
$setupMapper->setContext($context);
$setupMapper->setupDatabase('modules::guestbook2009::data', 'guestbook2009', $connectionKey);
?>