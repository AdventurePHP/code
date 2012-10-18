<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */

echo 'This is a sample setup script, that must be adapted to your requirements! '
      . 'Please do not use as is to avoid unexpected results! :)';
exit(0);

// include page controller
require('../../apps/core/pagecontroller/pagecontroller.php');

// configure the registry if desired
Registry::register('apf::core', 'Environment', '{ENVIRONMENT}');

// include SetupMapper
import('modules::genericormapper::data::tools', 'GenericORMapperSetup');

// create setup tool
$setup = new GenericORMapperSetup();

// set Context (important for the configuration files!)
$setup->setContext('{CONTEXT}');

// adapt storage engine (default is MyISAM)
//$setup->setStorageEngine('MyISAM|INNODB');

// adapt data type of the indexed columns, that are used for object and relation ids
//$setup->setIndexColumnDataType('INT(5) UNSIGNED');

// create database layout
$setup->setupDatabase('{CONFIG_NAMESPACE}', '{CONFIG_NAME_AFFIX}', '{CONNECTION_NAME}');

// display statements only
$setup->setupDatabase('{CONFIG_NAMESPACE}', '{CONFIG_NAME_AFFIX}');
