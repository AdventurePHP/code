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
 * This file sets up the core APF services/settings in order to start up very fast.
 */

/**
 * @file bootstrap.php
 *
 * Setups the framework's core environment. Initializes the Registry, that stores parameters,
 * that are used within the complete framework. These are
 * <ul>
 * <li>Environment      : environment, the application is executed in. The value is 'DEFAULT' in common</li>
 * <li>URLRewriting     : indicates, is url rewriting should be used</li>
 * <li>LogDir           : path, where log files are stored. The value is './logs' by default.</li>
 * <li>URLBasePath      : absolute url base path of the application (not really necessary)</li>
 * <li>LibPath          : path, where the framework and your own libraries reside. This path can be used
 *                        to address files with in the lib path directly (e.g. images or other resources)</li>
 * <li>CurrentRequestURL: the fully qualified request url</li>
 * </ul>
 * Further, the built-in input and output filters are initialized. For this reason, the following
 * registry entries are created within the "apf::core::filter" namespace:
 * <ul>
 * <li>PageControllerInputFilter : the definition of the input filter</li>
 * <li>OutputFilter              : the definition of the output filter</li>
 * </ul>
 * The file also contains the page controller core implementation with the classes Page,
 * Document, TagLib, APFObject, XmlParser and BaseDocumentController (the basic MVC document controller).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.06.2008<br />
 * Version 0.2, 16.07.2008 (added the LibPath to the registry namespace apf::core)
 * Version 0.3, 07.08.2008 (Made LibPath readonly)<br />
 * Version 0.4, 13.08.2008 (Fixed some timing problems with the registry initialisation)<br />
 * Version 0.5, 14.08.2008 (Changed LogDir initialisation to absolute paths)<br />
 * Version 0.6, 05.11.2008 (Added the 'CurrentRequestURL' attribute to the 'apf::core' namespace of the registry)<br />
 * Version 0.7, 11.12.2008 (Added the input and output filter initialization)<br />
 * Version 0.8, 01.02.2009 (Added the protocol prefix to the URLBasePath)<br />
 * Version 0.9, 21.02.2009 (Added the exception handler, turned off the php5 support in the import() function of the PHP4 branch)<br />
 */

/////////////////////////////////////////////////////////////////////////////////////////////////
// Define the internally used base path for the adventure php framework libraries.             //
// In case of symlink usage or multi-project installation, you can define it manually.         //
/////////////////////////////////////////////////////////////////////////////////////////////////
if (!isset($apfClassLoaderRootPath)) {
   $apfClassLoaderRootPath = str_replace('/core', '', str_replace('\\', '/', dirname(__FILE__)));
}

// include the class loader
include_once(dirname(__FILE__) . '/loader/RootClassLoader.php');

// register class loader before including/configuring further elements
\APF\core\loader\RootClassLoader::addLoader(new \APF\core\loader\StandardClassLoader('APF', $apfClassLoaderRootPath));
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

Registry::register('apf::core', 'Environment', 'DEFAULT');
Registry::register('apf::core', 'InternalLogTarget', 'apf');
Registry::register('apf::core', 'Charset', 'UTF-8');

// define current request url entry (check if the indices exist is important for cli-usage, because there they are neither available nor helpful)
if (isset($_SERVER['SERVER_PORT']) && isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
   $protocol = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
   Registry::register('apf::core', 'URLBasePath', $protocol . $_SERVER['HTTP_HOST']);
   Registry::register('apf::core', 'CurrentRequestURL', $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

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
