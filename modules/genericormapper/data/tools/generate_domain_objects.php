<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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

// include APF bootstrap file
require('./APF/core/bootstrap.php');

// configure the registry if desired
use APF\core\registry\Registry;

Registry::register('APF\core', 'Environment', '{ENVIRONMENT}');

use APF\modules\genericormapper\data\tools\GenericORMapperDomainObjectGenerator;

// create setup tool
$generator = new GenericORMapperDomainObjectGenerator();

// set context (important for the configuration files!)
$generator->setContext('{CONTEXT}');

// initialize mapping configuration
$generator->addMappingConfiguration('{CONFIG_NAMESPACE}', '{CONFIG_NAME_AFFIX}');

// initialize domain object configuration
$generator->addDomainObjectsConfiguration('{CONFIG_NAMESPACE}', '{CONFIG_NAME_AFFIX}');

$generator->generateServiceObjects();