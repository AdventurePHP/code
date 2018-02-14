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

/**
 * Bootstrap to start test application environment.<br />
 *
 * @author Florian Horn
 * @version
 * Version 0.1, 17.12.2011<br />
 * Version 0.2, 19.10.2015 (Removed static assertions include to ensure compatibility with newer PHPUnit and composer)<br />
 */
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '';
define('UnittestDir', dirname(__FILE__));
date_default_timezone_set('Europe/Berlin');

//
// --- Loading first resources
//
require_once(__DIR__ . '/../../../core/bootstrap.php');

use APF\core\errorhandler\GlobalErrorHandler;
use APF\core\exceptionhandler\GlobalExceptionHandler;

GlobalErrorHandler::disable();

GlobalExceptionHandler::disable();
