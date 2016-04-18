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
use APF\core\expression\taglib\ConditionalPlaceHolderTag;
use APF\core\expression\taglib\ConditionalTemplateTag;
use APF\core\filter\ChainedStandardInputFilter;
use APF\core\filter\InputFilterChain;
use APF\core\logging\Logger;
use APF\core\pagecontroller\AddTaglibTag;
use APF\core\pagecontroller\AppendNodeTag;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DynamicTemplateExpression;
use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\pagecontroller\LanguageLabelTag;
use APF\core\pagecontroller\PlaceHolderTag;
use APF\core\pagecontroller\PlaceHolderTemplateExpression;
use APF\core\pagecontroller\TemplateTag;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use APF\extensions\htmllist\taglib\DefinitionListDefinitionTag;
use APF\extensions\htmllist\taglib\DefinitionListTermTag;
use APF\extensions\htmllist\taglib\ListElementTag;
use APF\modules\captcha\pres\taglib\SimpleCaptchaTag;
use APF\modules\recaptcha\pres\taglib\ReCaptchaTag;
use APF\modules\usermanagement\pres\taglib\UmgtMediaInclusionLanguageLabelTag;
use APF\tools\form\mapping\CheckBoxValueMapper;
use APF\tools\form\mapping\MultiSelectBoxValueMapper;
use APF\tools\form\mapping\RadioButtonValueMapper;
use APF\tools\form\mapping\SelectBoxValueMapper;
use APF\tools\form\mapping\StandardValueMapper;
use APF\tools\form\multifileupload\pres\taglib\MultiFileUploadTag;
use APF\tools\form\taglib\AddFormControlFilterTag;
use APF\tools\form\taglib\AddFormControlValidatorTag;
use APF\tools\form\taglib\ButtonLanguageLabelTag;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\CheckBoxTag;
use APF\tools\form\taglib\CsrfProtectionHashTag;
use APF\tools\form\taglib\DateSelectorTag;
use APF\tools\form\taglib\DynamicFormElementMarkerTag;
use APF\tools\form\taglib\FileUploadTag;
use APF\tools\form\taglib\FormErrorDisplayTag;
use APF\tools\form\taglib\FormGroupTag;
use APF\tools\form\taglib\FormLabelTag;
use APF\tools\form\taglib\FormSuccessDisplayTag;
use APF\tools\form\taglib\HiddenFieldTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\ImageButtonTag;
use APF\tools\form\taglib\LabelLanguageLabelTag;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\form\taglib\PasswordFieldTag;
use APF\tools\form\taglib\RadioButtonTag;
use APF\tools\form\taglib\ResetButtonTag;
use APF\tools\form\taglib\SelectBoxGroupTag;
use APF\tools\form\taglib\SelectBoxOptionTag;
use APF\tools\form\taglib\SelectBoxTag;
use APF\tools\form\taglib\TextAreaTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\taglib\TimeCaptchaTag;
use APF\tools\form\taglib\TimeSelectorTag;
use APF\tools\form\taglib\ValidationListenerTag;
use APF\tools\html\taglib\FillHtmlIteratorTag;
use APF\tools\html\taglib\HtmlIteratorItemTag;
use APF\tools\html\taglib\HtmlIteratorTag;
use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;
use APF\tools\link\taglib\LinkLanguageLabelActiveTag;
use APF\tools\link\taglib\LinkLanguageLabelTag;
use APF\tools\link\taglib\LinkLanguageTitleActiveTag;
use APF\tools\link\taglib\LinkLanguageTitleTag;
use APF\tools\media\taglib\MediaInclusionTag;

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
spl_autoload_register(['\APF\core\loader\RootClassLoader', 'load']);

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
   $logger = Singleton::getInstance(Logger::class);
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

// The 2.2/3.0 APF parser allows to globally register tags. This not only eases tag implementation and re-usage
// but also registration at a central place (bootstrap file). The following section registers all APF tags
// shipped with the release to have them available for custom tags. Tags are grouped per namespace and purpose.

// APF\core
Document::addTagLib(AddTaglibTag::class, 'core', 'addtaglib');
Document::addTagLib(AppendNodeTag::class, 'core', 'appendnode');
Document::addTagLib(ImportTemplateTag::class, 'core', 'importdesign');

Document::addTagLib(LanguageLabelTag::class, 'html', 'getstring');
Document::addTagLib(PlaceHolderTag::class, 'html', 'placeholder');

Document::addTagLib(TemplateTag::class, 'html', 'template');

Document::addTagLib(ConditionalPlaceHolderTag::class, 'cond', 'placeholder');
Document::addTagLib(ConditionalTemplateTag::class, 'cond', 'template');

// APF\tools
Document::addTagLib(HtmlFormTag::class, 'html', 'form');

Document::addTagLib(AddFormControlFilterTag::class, 'form', 'addfilter');
Document::addTagLib(AddFormControlValidatorTag::class, 'form', 'addvalidator');
Document::addTagLib(ButtonTag::class, 'form', 'button');
Document::addTagLib(CheckBoxTag::class, 'form', 'checkbox');
Document::addTagLib(CsrfProtectionHashTag::class, 'form', 'csrfhash');
Document::addTagLib(DateSelectorTag::class, 'form', 'date');
Document::addTagLib(DynamicFormElementMarkerTag::class, 'form', 'marker');
Document::addTagLib(FileUploadTag::class, 'form', 'file');
Document::addTagLib(MultiFileUploadTag::class, 'form', 'multifileupload');
Document::addTagLib(FormErrorDisplayTag::class, 'form', 'error');
Document::addTagLib(FormLabelTag::class, 'form', 'label');
Document::addTagLib(FormSuccessDisplayTag::class, 'form', 'success');
Document::addTagLib(HiddenFieldTag::class, 'form', 'hidden');
Document::addTagLib(ImageButtonTag::class, 'form', 'imagebutton');
Document::addTagLib(MultiSelectBoxTag::class, 'form', 'multiselect');
Document::addTagLib(PasswordFieldTag::class, 'form', 'password');
Document::addTagLib(RadioButtonTag::class, 'form', 'radio');
Document::addTagLib(ResetButtonTag::class, 'form', 'reset');
Document::addTagLib(SelectBoxTag::class, 'form', 'select');
Document::addTagLib(TextAreaTag::class, 'form', 'area');
Document::addTagLib(TextFieldTag::class, 'form', 'text');
Document::addTagLib(TimeCaptchaTag::class, 'form', 'timecaptcha');
Document::addTagLib(TimeSelectorTag::class, 'form', 'time');
Document::addTagLib(ValidationListenerTag::class, 'form', 'listener');
Document::addTagLib(FormGroupTag::class, 'form', 'group');

Document::addTagLib(HtmlIteratorTag::class, 'html', 'iterator');
Document::addTagLib(TemplateTag::class, 'iterator', 'fallback');
Document::addTagLib(HtmlIteratorItemTag::class, 'iterator', 'item');
Document::addTagLib(FillHtmlIteratorTag::class, 'item', 'fill-iterator');

Document::addTagLib(ButtonLanguageLabelTag::class, 'button', 'getstring');

Document::addTagLib(LabelLanguageLabelTag::class, 'label', 'getstring');

Document::addTagLib(SelectBoxGroupTag::class, 'select', 'group');
Document::addTagLib(SelectBoxOptionTag::class, 'select', 'option');
Document::addTagLib(SelectBoxOptionTag::class, 'group', 'option');

Document::addTagLib(LinkLanguageLabelActiveTag::class, 'aActive', 'getstring');
Document::addTagLib(LinkLanguageLabelTag::class, 'a', 'getstring');
Document::addTagLib(LinkLanguageTitleActiveTag::class, 'titleActive', 'getstring');
Document::addTagLib(LinkLanguageTitleTag::class, 'title', 'getstring');

Document::addTagLib(MediaInclusionTag::class, 'html', 'mediastream');

// APF\modules
Document::addTagLib(UmgtMediaInclusionLanguageLabelTag::class, 'media', 'getstring');

Document::addTagLib(SimpleCaptchaTag::class, 'form', 'captcha');
Document::addTagLib(ReCaptchaTag::class, 'form', 'recaptcha');

// APF\extensions
Document::addTagLib(DefinitionListDefinitionTag::class, 'list', 'elem_defdef');
Document::addTagLib(DefinitionListTermTag::class, 'list', 'elem_defterm');
Document::addTagLib(ListElementTag::class, 'list', 'elem_list');

// The 2.2/3.0 APF parser allows to globally register template expressions. This allows to register custom "short cuts"
// for template syntax (e.g. place holders, language labels). The following section registers the default APF expressions
// shipped with the release to have them available for all templates.
Document::addTemplateExpression(PlaceHolderTemplateExpression::class);
Document::addTemplateExpression(DynamicTemplateExpression::class);

// Register form value mappers used to translate/transcribe form values into a DTO/model.
HtmlFormTag::addFormValueMapper(StandardValueMapper::class);
HtmlFormTag::addFormValueMapper(RadioButtonValueMapper::class);
HtmlFormTag::addFormValueMapper(SelectBoxValueMapper::class);
HtmlFormTag::addFormValueMapper(MultiSelectBoxValueMapper::class);
HtmlFormTag::addFormValueMapper(CheckBoxValueMapper::class);
