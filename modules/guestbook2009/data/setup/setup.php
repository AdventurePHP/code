<?php
namespace APF\modules\guestbook2009\data\setup;

/**
 * In order to use this script for database setup, include this file after including the
 * <em>bootstrap.php</em> file and setting the context and connection key.
 *
 * @example
 * <code>
 * include('./apps/core/bootstrap.php');
 * $context = 'mycontext';
 * $connectionKey = 'mysql-db';
 * include('./apps/modules/guestbook2009/data/setup/setup.php');
 * </code>
 *
 * Please note, that you may have to adapt the include path for the <em>pagecontroller.php</em>.
 */
use APF\modules\genericormapper\data\tools\GenericORMapperManagementTool;
$setup = new GenericORMapperManagementTool();
$setup->setContext($context);
$setup->addMappingConfiguration('APF\modules\guestbook2009\data', 'guestbook2009');
$setup->addRelationConfiguration('APF\modules\guestbook2009\data', 'guestbook2009');
$setup->setConnectionName($connectionKey);
$setup->run();
