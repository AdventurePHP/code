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
 * @file bootstrap.php
 *
 * Setups the framework's core environment. Initializes the Registry, that stores parameters,
 * that are used within the complete framework. These are
 * <ul>
 * <li>Environment      : environment, the application is executed in. The value is 'DEFAULT' in common.</li>
 * <li>InternalLogTarget: the name of the standard log target where framework log statements are written to.</li>
 * <li>Charset          : the internal character set used for string operations.</li>
 * </ul>
 * Besides, link schemes, input and/or output filter configuration as well as default
 * configuration providers are registered.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.04.2013<br />
 */

/////////////////////////////////////////////////////////////////////////////////////////////////
// Define the internally used base path for the adventure php framework libraries.             //
// In case of symlink usage or multi-project installation, you can define it manually.         //
/////////////////////////////////////////////////////////////////////////////////////////////////
if (!isset($apfClassLoaderRootPath)) {
   $apfClassLoaderRootPath = str_replace('/core', '', str_replace('\\', '/', dirname(__FILE__)));
}

// Manual definition of the configuration root path allows separation of APF source and configuration
// files. By default, configuration files reside under the same root folder.
if (!isset($apfClassLoaderConfigurationRootPath)) {
   $apfClassLoaderConfigurationRootPath = $apfClassLoaderRootPath;
}

// include the class loader
include_once(dirname(__FILE__) . '/loader/RootClassLoader.php');

// register class loader before including/configuring further elements
\APF\core\loader\RootClassLoader::addLoader(
   new \APF\core\loader\StandardClassLoader(
      'APF',
      $apfClassLoaderRootPath,
      $apfClassLoaderConfigurationRootPath
   )
);
spl_autoload_register(array('\APF\core\loader\RootClassLoader', 'load'));

// register the APF error handler to be able to easily configure the error handling mechanism
use APF\core\exceptionhandler\DefaultExceptionHandler;
use APF\core\exceptionhandler\GlobalExceptionHandler;

GlobalExceptionHandler::registerExceptionHandler(new DefaultExceptionHandler());
GlobalExceptionHandler::enable();

// let PHP raise and display all errors to be able to handle them suitable by the APF error handler.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('html_errors', 'off');

// register the APF error handler to be able to easily configure the error handling mechanism
use APF\core\errorhandler\DefaultErrorHandler;
use APF\core\errorhandler\GlobalErrorHandler;

GlobalErrorHandler::registerErrorHandler(new DefaultErrorHandler());
GlobalErrorHandler::enable();

// include the page controller classes here (no auto loading) to avoid issues with multiple classes per file
include_once(dirname(__FILE__) . '/pagecontroller/pagecontroller.php');

// Define base parameters of the framework's core and tools layer
use APF\core\registry\Registry;

Registry::register('APF\core', 'Environment', 'DEFAULT');
Registry::register('APF\core', 'InternalLogTarget', 'apf');
Registry::register('APF\core', 'Charset', 'UTF-8');

// set up configuration provider to let the developer customize it later on
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfigurationProvider;

ConfigurationManager::registerProvider('ini', new IniConfigurationProvider());

// configure logger (outside namespace'd file! otherwise initialization will not work)
use APF\core\singleton\Singleton;
use APF\core\logging\Logger;

register_shutdown_function(function () {
   /* @var $logger Logger */
   $logger = & Singleton::getInstance('APF\core\logging\Logger');
   $logger->flushLogBuffer();
});

// Set up default link scheme configuration. In case url rewriting is required, please
// specify another link scheme within your application bootstrap file.
use APF\tools\link\LinkGenerator;
use APF\tools\link\DefaultLinkScheme;

LinkGenerator::setLinkScheme(new DefaultLinkScheme());

// Add the front controller filter that is a wrapper on the front controller's input
// filters concerning thr url rewriting configuration. In case rewriting is required,
// please specify another input filter within your application bootstrap file according
// to your url mapping requirements (e.g. use the ChainedUrlRewritingInputFilter included
// within the APF).
// As shipped, the APF does not define an output filter since "normal" url handling
// does not require rewriting. In case rewriting is required, please specify another output
// filter according to your url mapping requirements (e.g. use the ChainedUrlRewritingOutputFilter
// included within the APF).
use APF\core\filter\InputFilterChain;
use APF\core\filter\ChainedStandardInputFilter;

InputFilterChain::getInstance()->appendFilter(new ChainedStandardInputFilter());
