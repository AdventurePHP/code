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

/**
 * @package APF\tests\lib\core
 *
 * Bootstrap to start test application environment.<br />
 *
 * @author Florian Horn
 * @version
 * Version 0.1, 17.12.2011<br />
 */
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '';
define('UnittestDir', dirname(__FILE__));
date_default_timezone_set('Europe/Berlin');

//
// --- Loading first resources
//
require_once('../core/bootstrap.php');
require_once('PHPUnit/Framework/Assert/Functions.php');

use APF\core\errorhandler\GlobalErrorHandler;

GlobalErrorHandler::disable();

use APF\core\exceptionhandler\GlobalExceptionHandler;

GlobalExceptionHandler::disable();
