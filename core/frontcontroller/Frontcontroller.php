<?php
namespace APF\core\frontcontroller;

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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\filter\InputFilterChain;
use APF\core\filter\OutputFilterChain;
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\Page;
use APF\core\registry\Registry;
use APF\core\service\APFDIService;
use APF\core\service\DIServiceManager;
use APF\core\singleton\Singleton;

/**
 * @package APF\core\frontcontroller
 * @class AbstractFrontcontrollerAction
 * @abstract
 *
 * Implements an action interface for a front controller action.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.01.2007<br />
 * Version 0.2, 24.02.2007 (Added param "KeepInURL")<br />
 * Version 0.3, 08.11.2007 (Switched default value for "KeepInURL" to false)<br />
 * Version 0.4, 07.08.2010 (Added the isActive() method to be able to self-deactivate actions on demand)<br />
 * Version 0.5, 12.04.2011 (Introduced constants for action types to be more type safe)<br />
 */
abstract class AbstractFrontcontrollerAction extends APFObject implements APFDIService {

   const TYPE_PRE_PAGE_CREATE = 'prepagecreate';
   const TYPE_PRE_TRANSFORM = 'pretransform';
   const TYPE_POST_TRANSFORM = 'posttransform';

   /**
    * @private
    * @var string The namespace of the action.
    */
   protected $actionNamespace;

   /**
    * @private
    * @var string The name of the action (used to identify the action within the action stack).
    */
   protected $actionName;

   /**
    * @private
    * @var FrontcontrollerInput Input object of the action.
    */
   protected $input;

   /**
    * @private
    * Defines the type of the action. Allowed values
    * <ul>
    *   <li>prepagecreate: executed before the page controller page is created</li>
    *   <li>pretransform: executed before transformation of the page</li>
    *   <li>posttransform: executed after transformation of the page</li>
    * </ul>
    * The default value is "prepagecreate".
    */
   protected $type = self::TYPE_PRE_PAGE_CREATE;

   /**
    * @private
    * @var boolean Indicates, if the action should be included in the URL. Values: true | false.
    */
   private $keepInUrl = false;

   /**
    * @var Frontcontroller The front controller instance the action belongs to.
    */
   private $frontController;

   /**
    * @public
    *
    * Returns the input object of the action.
    *
    * @return FrontcontrollerInput The input object associated with the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.02.2007<br />
    */
   public function &getInput() {
      return $this->input;
   }

   /**
    * @public
    *
    * Injects the input param wrapper of the current action.
    *
    * @param FrontcontrollerInput $input The input object associated with the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setInput($input) {
      $this->input = $input;
   }

   /**
    * @public
    *
    * Sets the name of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @param string $name The name of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setActionName($name) {
      $this->actionName = $name;
   }

   /**
    * @public
    *
    * Returns the name of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @return string The name of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getActionName() {
      return $this->actionName;
   }

   /**
    * @public
    *
    * Sets the namespace of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @param string $namespace The namespace of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setActionNamespace($namespace) {
      $this->actionNamespace = $namespace;
   }

   /**
    * @public
    *
    * Returns the namespace of the action, that is used to refer it within the
    * front controller's action stack.
    *
    * @return string The namespace of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getActionNamespace() {
      return $this->actionNamespace;
   }

   /**
    * @public
    *
    * Sets the type of the action, that defines the execution time.
    *
    * @param string $type The type of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setType($type) {
      $this->type = $type;
   }

   /**
    * @public
    *
    * Returns the type of the action, that defines the execution time.
    *
    * @return string The type of the action.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getType() {
      return $this->type;
   }

   /**
    * @public
    *
    * Set the indicator, whether the action should be kept in the url
    * generating a fully qualified front controller link.
    *
    * @param bool $keepInUrl The url generation indicator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setKeepInUrl($keepInUrl) {
      $this->keepInUrl = $keepInUrl;
   }

   /**
    * @public
    *
    * Returns the indicator, whether the action should be kept in the url
    * generating a fully qualified front controller link.
    *
    * @return bool The url generation indicator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getKeepInUrl() {
      return $this->keepInUrl;
   }

   /**
    * @public
    *
    * Indicates, whether the action should be executed by the front controller or not.
    * This method can be overridden in case an action should not be executed due to
    * special concerns. This maybe the execution of another action or certain url params.
    *
    * @return boolean True, in case the action should be executed, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.08.2010<br />
    */
   public function isActive() {
      return true;
   }

   /**
    * @public
    *
    * Allows you to manipulate the priority of this instance in comparison to other actions
    * of the same type on the action stack.
    * <p/>
    * In case priority of another action is higher than the value returned by this method for
    * the current instance, this action takes higher priority and is executed first.
    * <p/>
    * Default value is 10 to allow easier prioritization at a granular level. Example: returning
    * <em>1</em> means higher priority, <em>20</em> means lower priority.
    *
    * @return int The action's priority on the action stack.
    */
   public function getPriority() {
      return 10;
   }

   /**
    * @public
    *
    * Returns the associated front controller instance.
    *
    * @return Frontcontroller The associated front controller instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getFrontController() {
      return $this->frontController;
   }

   /**
    * @public
    *
    * Let's the front controller inject itself.
    *
    * @param Frontcontroller $frontController The current front controller instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setFrontController(Frontcontroller &$frontController) {
      $this->frontController = & $frontController;
   }

   /**
    * @public
    * @abstract
    *
    * Defines the interface method, that must be implemented by each concrete action. The method
    * is called by the front controller when the action is executed.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.01.2007<br />
    */
   public abstract function run();
}

/**
 * @package APF\core\frontcontroller
 * @class FrontcontrollerInput
 *
 * Implements a base class for input parameters for front controller actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.01.2007<br />
 */
class FrontcontrollerInput extends APFObject {

   /**
    * @var AbstractFrontcontrollerAction The action the input belongs to.
    */
   private $action;

   public function &getAction() {
      return $this->action;
   }

   public function setAction(AbstractFrontcontrollerAction &$action) {
      $this->action = & $action;
   }

}

/**
 * @package APF\core\frontcontroller
 * @class Frontcontroller
 *
 * Implements the APF front controller. It enables the developer to execute actions
 * defined within the bootstrap file or the url to enrich a page controller application
 * with business logic.
 * <p/>
 * The controller has it's own timing model. Hence, he can be used for special jobs such
 * as image delivery or creation of the business layer components concerning the time
 * slots the actions are executed. Please refer to the documentation page for a
 * timing diagram.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.01.2007<br />
 * Version 0.2, 01.03.2007 (Input objects are now loaded by the front controller, too!)<br />
 * Version 0.3, 08.06.2007 (Now permanent actions defined within the bootstrap file are introduced.)<br />
 * Version 0.4, 01.07.2007 (Removed __createInputObject())<br />
 * Version 0.5. 20.08.2013 Jan Wiese (Added support for actions generated by thw DIServiceManager)<br />
 */
class Frontcontroller extends APFObject {

   /**
    * @protected
    * @var AbstractFrontcontrollerAction[] The front controller's action stack.
    */
   protected $actionStack = array();

   /**
    * @protected
    * @var string The keyword used in the url to indicate an action.
    */
   private $actionKeyword = 'action';

   /**
    * @protected
    * @var string Namespace delimiter within the action definition in url.
    */
   private $namespaceURLDelimiter = '_';

   /**
    * @protected
    * @var string Namespace to action keyword delimiter within the action definition in url.
    */
   private $namespaceKeywordDelimiter = '-';

   /**
    * @protected
    * @var string Delimiter between action keyword and action class within the action definition in url.
    */
   private $keywordClassDelimiter = ':';

   /**
    * @protected
    * @var string Delimiter between action keyword and action class within the action definition in url (url rewriting case!)
    */
   private $urlRewritingKeywordClassDelimiter = '/';

   /**
    * @protected
    * @var string Delimiter between input value couples.
    */
   private $inputDelimiter = '|';

   /**
    * @protected
    * @var string Delimiter between input value couples (url rewriting case!).
    */
   private $urlRewritingInputDelimiter = '/';

   /**
    * @protected
    * @var string Delimiter between input param name and value.
    */
   private $keyValueDelimiter = ':';

   /**
    * @protected
    * @var string Delimiter between input param name and value (url rewrite case!).
    */
   private $urlRewritingKeyValueDelimiter = '/';

   public function getActionKeyword() {
      return $this->actionKeyword;
   }

   public function getNamespaceURLDelimiter() {
      return $this->namespaceURLDelimiter;
   }

   public function getNamespaceKeywordDelimiter() {
      return $this->namespaceKeywordDelimiter;
   }

   public function getKeywordClassDelimiter() {
      return $this->keywordClassDelimiter;
   }

   public function getURLRewritingKeywordClassDelimiter() {
      return $this->urlRewritingKeywordClassDelimiter;
   }

   public function getInputDelimiter() {
      return $this->inputDelimiter;
   }

   public function getURLRewritingInputDelimiter() {
      return $this->urlRewritingInputDelimiter;
   }

   public function getKeyValueDelimiter() {
      return $this->keyValueDelimiter;
   }

   public function getURLRewritingKeyValueDelimiter() {
      return $this->urlRewritingKeyValueDelimiter;
   }

   /**
    * @public
    *
    * Executes the desired actions and creates the page output.
    *
    * @param string $namespace Namespace of the templates.
    * @param string $template Name of the templates.
    *
    * @return string The content of the transformed page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.01.2007<br />
    * Version 0.2, 27.01.2007<br />
    * Version 0.3, 31.01.2007<br />
    * Version 0.4, 03.02.2007 (Added permanent actions)<br />
    * Version 0.5, 08.06.2007 (Outsourced URL filtering to generic input filter)<br />
    * Version 0.6, 01.07.2007 (Removed permanentpre and permanentpost actions)<br />
    * Version 0.7, 29.09.2007 (Added further benchmark tags)<br />
    * Version 0.8, 21.06.2008 (Introduced Registry to retrieve URLRewrite configuration)<br />
    * Version 0.9, 13.10.2008 (Removed $URLRewriting parameter, because URL rewriting must be configured in the registry)<br />
    * Version 1.0, 11.12.2008 (Switched to the new input filter concept)<br />
    */
   public function start($namespace, $template) {

      // check if the context is set. If not, use the current namespace
      $context = $this->getContext();
      if (empty($context)) {
         $this->setContext($namespace);
      }

      // apply front controller input filter
      InputFilterChain::getInstance()->filter(null);

      // execute pre page create actions (see timing model)
      $this->runActions(AbstractFrontcontrollerAction::TYPE_PRE_PAGE_CREATE);

      // create new page
      $page = new Page();

      // set context
      $page->setContext($this->getContext());

      // set language
      $page->setLanguage($this->getLanguage());

      // load desired design
      $page->loadDesign($namespace, $template);

      // execute actions before transformation (see timing model)
      $this->runActions(AbstractFrontcontrollerAction::TYPE_PRE_TRANSFORM);

      // transform page
      $pageContent = OutputFilterChain::getInstance()->filter($page->transform());

      // execute actions after page transformation (see timing model)
      $this->runActions(AbstractFrontcontrollerAction::TYPE_POST_TRANSFORM);

      return $pageContent;
   }

   /**
    * @public
    *
    * Returns the action specified by the input param.
    *
    * @param string $actionName The name of the action to return.
    *
    * @return AbstractFrontcontrollerAction The desired action or null.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.02.2007<br />
    * Version 0.2, 08.02.2007<br />
    * Version 0.3, 11.02.2007<br />
    * Version 0.4, 01.03.2007<br />
    * Version 0.5, 01.03.2007<br />
    * Version 0.6, 08.03.2007 (Switched to new ConfigurationManager)<br />
    * Version 0.7, 08.06.2007<br />
    * Version 0.8, 08.11.2007 (Switched to new hash offsets)<br />
    */
   public function &getActionByName($actionName) {

      foreach ($this->actionStack as $actionHash => $DUMMY) {
         if ($this->actionStack[$actionHash]->getActionName() == $actionName) {
            return $this->actionStack[$actionHash];
         }
      }

      // return null, if action could not be found
      $null = null;

      return $null;
   }

   /**
    * @public
    *
    * Returns the action stack.
    *
    * @return AbstractFrontcontrollerAction[] The front controller action stack.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.02.2007<br />
    */
   public function &getActions() {
      return $this->actionStack;
   }

   /**
    * @private
    *
    * Creates the url representation of a given namespace.
    *
    * @param string $namespaceUrlRepresentation The url string.
    *
    * @return string The namespace of the action.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2007<br />
    */
   protected function getActionNamespaceByURLString($namespaceUrlRepresentation) {
      return str_replace($this->namespaceURLDelimiter, '\\', $namespaceUrlRepresentation);
   }

   /**
    * @public
    *
    * Registers an action to the front controller. This includes action configuration using
    * the action params defined within the action mapping. Each action definition is expected
    * to be stored in the <em>{ENVIRONMENT}_actionconfig.ini</em> file under the namespace
    * <em>{$namespace}\{$this->context}.</em>
    *
    * @param string $namespace Namespace of the action to register.
    * @param string $name Name of the action to register.
    * @param array $params (Input-) params of the action.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.06.2007<br />
    * Version 0.2, 01.07.2007 (Action namespace is now translated at the addAction() method)<br />
    * Version 0.3, 01.07.2007 (Config params are now parsed correctly)<br />
    * Version 0.4, 27.09.2010 (Removed synthetic "actions" sub-namespace)<br />
    */
   public function registerAction($namespace, $name, array $params = array()) {

      $config = $this->getConfiguration($namespace, 'actionconfig.ini');

      if ($config != null) {

         // separate param strings
         if (strlen(trim($config->getValue($name, 'InputParams'))) > 0) {

            // separate params
            $staticParams = explode($this->inputDelimiter, $config->getValue($name, 'InputParams'));

            for ($i = 0; $i < count($staticParams); $i++) {

               if (substr_count($staticParams[$i], $this->keyValueDelimiter) > 0) {

                  $pairs = explode($this->keyValueDelimiter, $staticParams[$i]);

                  // re-order and add to param list
                  if (isset($pairs[0]) && isset($pairs[1])) {
                     $params = array_merge($params, array($pairs[0] => $pairs[1]));
                  }
               }
            }
         }
      }

      $this->addAction($namespace, $name, $params);
   }

   /**
    * @public
    *
    * Adds an action to the front controller action stack. Please note, that the namespace of
    * the namespace of the action config is added the current context. The name of the
    * config file is concatenated by the current environment and the string
    * <em>*_actionconfig.ini</em>.
    *
    * @param string $namespace Namespace of the action.
    * @param string $name Name of the action (section key of the config file).
    * @param array $params (Input-)params of the action.
    *
    * @throws \InvalidArgumentException In case the action cannot be found within the appropriate
    * configuration or the action implementation classes are not available.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2007<br />
    * Version 0.2, 01.07.2007<br />
    * Version 0.3, 02.09.2007<br />
    * Version 0.4, 08.09.2007 (Bug-fix: input params from config are now evaluated)<br />
    * Version 0.5, 08.11.2007 (Changed action stack construction to hash offsets)<br />
    * Version 0.6, 21.06.2008 (Replaced APPS__ENVIRONMENT constant with a value from the Registry)<br />
    * Version 0.7, 27.09.2010 (Removed synthetic "actions" sub-namespace)<br />
    * Version 0.8, 09.04.2011 (Made input implementation optional, removed separate action and input class file definition)<br />
    * Version 0.9. 20.08.2013 Jan Wiese (Added support for actions generated by thw DIServiceManager)<br />
    */
   public function addAction($namespace, $name, array $params = array()) {

      // re-map namespace
      $namespace = $this->getActionNamespaceByURLString($namespace);

      // load the action configuration
      $config = $this->getConfiguration($namespace, 'actionconfig.ini');
      $actionConfig = $config->getSection($name);

      // throw exception, in case the action config is not present
      if ($actionConfig == null) {
         $env = Registry::retrieve('APF\core', 'Environment');
         throw new \InvalidArgumentException('[Frontcontroller::addAction()] No config '
               . 'section for action key "' . $name . '" available in configuration file "' . $env
               . '_actionconfig.ini" in namespace "' . $namespace . '" and context "'
               . $this->getContext() . '"!', E_USER_ERROR);
      }


      // evaluate which method to use: simple object or di service
      $actionServiceName = $actionConfig->getValue('ActionServiceName');
      $actionServiceNamespace = $actionConfig->getValue('ActionServiceNamespace');

      if (!(empty($actionServiceName) || empty($actionServiceNamespace))) {
         // use di service

         try {
            $action = DIServiceManager::getServiceObject(
                  $actionServiceNamespace,
                  $actionServiceName,
                  $this->getContext(),
                  $this->getLanguage()
            );
         } catch (\Exception $e) {
            throw new \InvalidArgumentException('[Frontcontroller::addAction()] Action could not
            be created using DIServiceManager with service name "' . $actionServiceName . '" and service
            namespace "' . $actionServiceNamespace . '". Please check your action and service
            configuration files! Message from DIServiceManager was: ' . $e->getMessage(), $e->getCode());
         }

      } else {
         // use simple object

         // include action implementation
         $actionClass = $actionConfig->getValue('ActionClass');

         // check for class being present
         if (!class_exists($actionClass)) {
            throw new \InvalidArgumentException('[Frontcontroller::addAction()] Action class with name "'
                  . $actionClass . '" could not be found. Please check your action configuration file!', E_USER_ERROR);
         }

         // init action
         $action = new $actionClass;
         /* @var $action AbstractFrontcontrollerAction */

         $action->setContext($this->getContext());
         $action->setLanguage($this->getLanguage());

      }

      // init action
      $action->setActionNamespace($namespace);
      $action->setActionName($name);

      // check for custom input implementation
      $inputClass = $actionConfig->getValue('InputClass');

      // include input implementation in case a custom implementation is desired
      if (empty($inputClass)) {
         $inputClass = 'APF\core\frontcontroller\FrontcontrollerInput';
      }

      // check for class being present
      if (!class_exists($inputClass)) {
         throw new \InvalidArgumentException('[Frontcontroller::addAction()] Input class with name "' . $inputClass
               . '" could not be found. Please check your action configuration file!', E_USER_ERROR);
      }

      // init input
      $input = new $inputClass;
      /* @var $input FrontcontrollerInput */

      // merge input params with the configured params (params included in the URL are kept!)
      $input->setAttributes(array_merge(
            $this->generateParamsFromInputConfig($actionConfig->getValue('InputParams')),
            $params));

      $input->setAction($action);
      $action->setInput($input);

      // set the frontcontroller as a parent object to the action
      $action->setFrontController($this);

      // add the action as a child
      $this->actionStack[] = $action;

      // Sort actions to allow prioritization of actions. This is done using
      // uksort() in order to both respect AbstractFrontcontrollerAction::getPriority()
      // and the order of registration for equivalence groups.
      uksort($this->actionStack, array($this, 'sortActions'));
   }

   /**
    * @private
    *
    * Compares two actions to allow sorting of actions.
    * <p/>
    * Actions with a lower priority returned by <em>AbstractFrontcontrollerAction::getPriority()</em>
    * are executed prior to others.
    *
    * @param int $a Offset one for comparison.
    * @param int $b Offset two for comparison.
    *
    * @return int <em>-1</em> in case action <em>$one</em> has lower priority, <em>1</em> in case <em>$two</em> has higher priority. <em>0</em> in case actions are equal.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.03.2014<br />
    */
   private function sortActions($a, $b) {
      if ($this->actionStack[$a]->getPriority() == $this->actionStack[$b]->getPriority()) {
         if ($a == $b) {
            return 0;
         }

         return $a > $b ? 1 : -1; // sort equals again to preserve order!
      }

      return $this->actionStack[$a]->getPriority() > $this->actionStack[$b]->getPriority() ? -1 : 1;
   }

   /**
    * @protected
    *
    * Create an array from a input param string (scheme: <code>a:b|c:d</code>).
    *
    * @param string $inputConfig The config string contained in the action config.
    *
    * @return string[] The resulting param-value array.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 08.09.2007<br />
    */
   protected function generateParamsFromInputConfig($inputConfig = '') {

      $inputParams = array();

      $inputConfig = trim($inputConfig);

      if (strlen($inputConfig) > 0) {

         // first: explode couples by "|"
         $paramsArray = explode($this->inputDelimiter, $inputConfig);

         for ($i = 0; $i < count($paramsArray); $i++) {

            // second: explode key and value by ":"
            $tmpAry = explode($this->keyValueDelimiter, $paramsArray[$i]);

            if (isset($tmpAry[0]) && isset($tmpAry[1]) && !empty($tmpAry[0]) && !empty($tmpAry[1])) {
               $inputParams[$tmpAry[0]] = $tmpAry[1];
            }
         }
      }

      return $inputParams;
   }

   /**
    * @protected
    *
    * Executes all actions with the given type. Possible types are
    * <ul>
    * <li>prepagecreate</li>
    * <li>pretransform</li>
    * <li>posttransform</li>
    * </ul>
    *
    * @param string $type Type of the actions to execute.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.01.2007<br />
    * Version 0.2, 31.01.2007<br />
    * Version 0.3, 03.02.2007 (Added benchmarker)<br />
    * Version 0.4, 01.07.2007 (Removed debug output)<br />
    * Version 0.5, 08.11.2007<br />
    * Version 0.6, 28.03.2008 (Optimized benchmarker call)<br />
    * Version 0.7, 07.08.2010 (Added action activation indicator to disable actions on demand)<br />
    */
   protected function runActions($type = AbstractFrontcontrollerAction::TYPE_PRE_PAGE_CREATE) {

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');

      foreach ($this->actionStack as $actionHash => $DUMMY) {

         // only execute, when the current action has a suitable type
         if ($this->actionStack[$actionHash]->getType() == $type
               && $this->actionStack[$actionHash]->isActive()
         ) {

            $id = get_class($this->actionStack[$actionHash]) . '::run()';
            $t->start($id);

            $this->actionStack[$actionHash]->run();

            $t->stop($id);
         }
      }
   }

}
