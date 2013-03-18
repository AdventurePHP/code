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

/**
 * @file umgt.php
 * This file represents a bootstrap file to operate the usermanagement module.
 */

// include the pagecontroller (change the path to what ever you want)
include_once('./apps/core/pagecontroller/pagecontroller.php');

// import the front controller
use APF\core\frontcontroller\Frontcontroller;

// create the front controller instance
$fC = &Singleton::getInstance('Frontcontroller');

// set the current context (change the context to what ever you want)
$fC->setContext('...');

// start the front controller
$fC->start('modules::usermanagement::pres::templates', 'main');

// create the benchmark report
$t = &Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
echo $t->createReport();
