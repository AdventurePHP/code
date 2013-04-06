<?php
namespace APF\modules\usermanagement\pres;

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
use APF\core\singleton\Singleton;
use APF\core\frontcontroller\Frontcontroller;
use APF\core\benchmark\BenchmarkTimer;

/**
 * @file umgt.php
 * This file represents a bootstrap file to operate the usermanagement module.
 */

// include the pagecontroller (change the path to what ever you want)
include_once('./APF/core/bootstrap.php');

// create the front controller instance
/* @var $fC Frontcontroller */
$fC = & Singleton::getInstance('APF\core\frontcontroller\Frontcontroller');

// set the current context (change the context to what ever you want)
$fC->setContext('...');

// start the front controller
$fC->start('APF\modules\usermanagement\pres\templates', 'main');

// create the benchmark report
/* @var $t BenchmarkTimer */
$t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
echo $t->createReport();
