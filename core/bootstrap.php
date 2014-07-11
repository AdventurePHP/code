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
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\errorhandler\DefaultErrorHandler;
use APF\core\errorhandler\GlobalErrorHandler;
use APF\core\exceptionhandler\DefaultExceptionHandler;
use APF\core\exceptionhandler\GlobalExceptionHandler;
use APF\core\filter\ChainedStandardInputFilter;
use APF\core\filter\InputFilterChain;
use APF\core\logging\Logger;
use APF\core\pagecontroller\Document;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;

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
 * Version 0.2, 27.06.2014 (Added static registration of tags)<br />
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
include_once(dirname(__FILE__) . '/loader/ClassLoader.php');
include_once(dirname(__FILE__) . '/loader/StandardClassLoader.php');
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
GlobalExceptionHandler::registerExceptionHandler(new DefaultExceptionHandler());
GlobalExceptionHandler::enable();

// let PHP raise and display all errors to be able to handle them suitable by the APF error handler.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('html_errors', 'off');

// register the APF error handler to be able to easily configure the error handling mechanism
GlobalErrorHandler::registerErrorHandler(new DefaultErrorHandler());
GlobalErrorHandler::enable();

// Define base parameters of the framework's core and tools layer
Registry::register('APF\core', 'Environment', 'DEFAULT');
Registry::register('APF\core', 'InternalLogTarget', 'apf');
Registry::register('APF\core', 'Charset', 'UTF-8');

// set up configuration provider to let the developer customize it later on
ConfigurationManager::registerProvider('ini', new IniConfigurationProvider());

// configure logger (outside namespace'd file! otherwise initialization will not work)
register_shutdown_function(function () {
   /* @var $logger Logger */
   $logger = & Singleton::getInstance('APF\core\logging\Logger');
   $logger->flushLogBuffer();
});

// Set up default link scheme configuration. In case url rewriting is required, please
// specify another link scheme within your application bootstrap file.
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
InputFilterChain::getInstance()->appendFilter(new ChainedStandardInputFilter());

// The 2.2 APF parser allows to globally register tags. This not only eases tag implementation and re-usage
// but also registration at a central place (bootstrap file). The following section registers all APF tags
// shipped with the release to have them available for custom tags. Tags are grouped per namespace.

// APF\core
Document::addTagLib('APF\core\pagecontroller\AddTaglibTag', 'core', 'addtaglib');
Document::addTagLib('APF\core\pagecontroller\AppendNodeTag', 'core', 'appendnode');
Document::addTagLib('APF\core\pagecontroller\ImportTemplateTag', 'core', 'importdesign');

Document::addTagLib('APF\core\pagecontroller\LanguageLabelTag', 'html', 'getstring');
Document::addTagLib('APF\core\pagecontroller\PlaceHolderTag', 'html', 'placeholder');

Document::addTagLib('APF\core\pagecontroller\TemplateTag', 'html', 'template');

// APF\tools
Document::addTagLib('APF\tools\form\taglib\HtmlFormTag', 'html', 'form');

Document::addTagLib('APF\tools\form\taglib\AddFormControlFilterTag', 'form', 'addfilter');
Document::addTagLib('APF\tools\form\taglib\AddFormControlValidatorTag', 'form', 'addvalidator');
Document::addTagLib('APF\tools\form\taglib\ButtonTag', 'form', 'button');
Document::addTagLib('APF\tools\form\taglib\CheckBoxTag', 'form', 'checkbox');
Document::addTagLib('APF\tools\form\taglib\CsrfProtectionHashTag', 'form', 'csrfhash');
Document::addTagLib('APF\tools\form\taglib\DateSelectorTag', 'form', 'date');
Document::addTagLib('APF\tools\form\taglib\DynamicFormElementMarkerTag', 'form', 'marker');
Document::addTagLib('APF\tools\form\taglib\FileUploadTag', 'form', 'file');
Document::addTagLib('APF\tools\form\multifileupload\pres\taglib\MultiFileUploadTag', 'form', 'multifileupload');
Document::addTagLib('APF\tools\form\taglib\FormErrorDisplayTag', 'form', 'error');
Document::addTagLib('APF\tools\form\taglib\FormLabelTag', 'form', 'label');
Document::addTagLib('APF\tools\form\taglib\FormSuccessDisplayTag', 'form', 'success');
Document::addTagLib('APF\tools\form\taglib\HiddenFieldTag', 'form', 'hidden');
Document::addTagLib('APF\tools\form\taglib\ImageButtonTag', 'form', 'imagebutton');
Document::addTagLib('APF\tools\form\taglib\MultiSelectBoxTag', 'form', 'multiselect');
Document::addTagLib('APF\tools\form\taglib\PasswordFieldTag', 'form', 'password');
Document::addTagLib('APF\tools\form\taglib\RadioButtonTag', 'form', 'radio');
Document::addTagLib('APF\tools\form\taglib\ResetButtonTag', 'form', 'reset');
Document::addTagLib('APF\tools\form\taglib\SelectBoxTag', 'form', 'select');
Document::addTagLib('APF\tools\form\taglib\TextAreaTag', 'form', 'area');
Document::addTagLib('APF\tools\form\taglib\TextFieldTag', 'form', 'text');
Document::addTagLib('APF\tools\form\taglib\TimeCaptchaTag', 'form', 'timecaptcha');
Document::addTagLib('APF\tools\form\taglib\TimeSelectorTag', 'form', 'time');
Document::addTagLib('APF\tools\form\taglib\ValidationListenerTag', 'form', 'listener');

Document::addTagLib('APF\tools\html\taglib\HtmlIteratorTag', 'html', 'iterator');
Document::addTagLib('APF\core\pagecontroller\TemplateTag', 'iterator', 'fallback');
Document::addTagLib('APF\tools\html\taglib\HtmlIteratorItemTag', 'iterator', 'item');

Document::addTagLib('APF\tools\form\taglib\ButtonLanguageLabelTag', 'button', 'getstring');

Document::addTagLib('APF\tools\form\taglib\LabelLanguageLabelTag', 'label', 'getstring');

Document::addTagLib('APF\tools\form\taglib\SelectBoxGroupTag', 'select', 'group');
Document::addTagLib('APF\tools\form\taglib\SelectBoxOptionTag', 'select', 'option');
Document::addTagLib('APF\tools\form\taglib\SelectBoxOptionTag', 'group', 'option');

Document::addTagLib('APF\tools\link\taglib\LinkLanguageLabelActiveTag', 'aActive', 'getstring');
Document::addTagLib('APF\tools\link\taglib\LinkLanguageLabelTag', 'a', 'getstring');
Document::addTagLib('APF\tools\link\taglib\LinkLanguageTitleActiveTag', 'titleActive', 'getstring');
Document::addTagLib('APF\tools\link\taglib\LinkLanguageTitleTag', 'title', 'getstring');

Document::addTagLib('APF\tools\media\taglib\MediaInclusionTag', 'html', 'mediastream');

// APF\modules
Document::addTagLib('APF\modules\recaptcha\pres\taglib\ReCaptchaTranslationTag', 'recaptcha', 'getstring');
Document::addTagLib('APF\modules\usermanagement\pres\taglib\UmgtMediaInclusionLanguageLabelTag', 'media', 'getstring');

Document::addTagLib('APF\modules\captcha\pres\taglib\SimpleCaptchaTag', 'form', 'captcha');
Document::addTagLib('APF\modules\recaptcha\pres\taglib\ReCaptchaTag', 'form', 'recaptcha');

// APF\extensions
Document::addTagLib('APF\extensions\htmllist\taglib\DefinitionListDefinitionTag', 'list', 'elem_defdef');
Document::addTagLib('APF\extensions\htmllist\taglib\DefinitionListTermTag', 'list', 'elem_defterm');
Document::addTagLib('APF\extensions\htmllist\taglib\ListElementTag', 'list', 'elem_list');
