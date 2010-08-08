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

   // setup the front controller input filter and disable the page controller filter
   Registry::register('apf::core::filter','FrontControllerInputFilter',new FilterDefinition('core::filter','FrontControllerInputFilter'));
   Registry::register('apf::core::filter','PageControllerInputFilter',null);

   /**
    * @package core::frontcontroller
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
    */
   abstract class AbstractFrontcontrollerAction extends APFObject {

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
       *   <li>postpagecreate: executed after the page controller page is created</li>
       *   <li>pretransform: executed before transformation of the page</li>
       *   <li>posttransform: executed after transformation of the page</li>
       * </ul>
       * The default value is "prepagecreate".
       */
      protected $type = 'prepagecreate';

      /**
       * @private
       * @var boolean Indicates, if the action should be included in the URL. Values: true | false.
       */
      private $keepInUrl = false;

      public function AbstractFrontcontrollerAction(){
      }

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
      public function &getInput(){
         return $this->input;
       // end function
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
      public function setInput($input){
         $this->input = $input;
       // end function
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
      public function setActionName($name){
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
      public function getActionName(){
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
      public function setActionNamespace($namespace){
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
      public function getActionNamespace(){
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
      public function setType($type){
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
      public function getType(){
         return $this->type;
      }

      /**
       * @public
       *
       * Set the indicator, whether the action should be keept in the url
       * generating a fully qualified front controller link.
       *
       * @param string $keepInUrl The url generation indicator.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function setKeepInUrl($keepInUrl){
         $this->keepInUrl = $keepInUrl;
      }

      /**
       * @public
       *
       * Returns the indicator, whether the action should be keept in the url
       * generating a fully qualified front controller link.
       *
       * @return string The url generation indicator.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getKeepInUrl(){
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
      public function isActive(){
         return true;
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

    // end class
   }

   /**
    * @package core::frontcontroller
    * @class FrontcontrollerInput
    *
    * Implements a base class for input parameters for front controller actions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.01.2007<br />
    */
   class FrontcontrollerInput extends APFObject {

      public function FrontcontrollerInput(){
      }

      /**
       * @public
       *
       * Returns all input parameters as a URL formatted string.
       *
       * @param boolean $urlRewriting True for activated url rewriting, false instead.
       * @return string URL formatted attributes string.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2007<br />
       * Version 0.2, 08.11.2007 (Fehler bei leerem Input-Objekt korrigiert)<br />
       * Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
       */
      public function getAttributesAsString($urlRewriting = null){

         // get the current front controller
         $action = &$this->__ParentObject;
         $fC = &$action->getParentObject();

         // set URLRewriting manually
         if($urlRewriting === null){
            $urlRewriting = Registry::retrieve('apf::core','URLRewriting');
          // end if
         }

         // define url delimiter
         if($urlRewriting == true){
            $inputDelimiter = $fC->get('URLRewritingInputDelimiter');
            $keyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
          // end if
         }
         else{
            $inputDelimiter = $fC->get('InputDelimiter');
            $keyValueDelimiter = $fC->get('KeyValueDelimiter');
          // end else
         }

         // fill consolidated attributes array
         $attributes = array();
         if(count($this->__Attributes) > 0){
            foreach($this->__Attributes as $key => $value){
               $attributes[] = $key.$keyValueDelimiter.$value;
            }
         }

         return implode($inputDelimiter,$attributes);

       // end function
      }

    // end class
   }

   /**
    * @package core::frontcontroller
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
    */
   class Frontcontroller extends APFObject {

      /**
       * @protected
       * @var AbstractFrontcontrollerAction[] The front controller's action stack.
       */
      protected $actionStack = array();

      /**
       * @protected
       * @var string The keyword used in the url to inidicate an action.
       */
      protected $__ActionKeyword = 'action';

      /**
       * @protected
       * @var string Namespace delimiter within the action definition in url.
       */
      protected $__NamespaceURLDelimiter = '_';

      /**
       * @protected
       * @var string Namespace to action keyword delimiter within the action definition in url.
       */
      protected $__NamespaceKeywordDelimiter = '-';

      /**
       * @protected
       * @var string Delimiter between action keyword and action class within the action definition in url.
       */
      protected $__KeywordClassDelimiter = ':';

      /**
       * @protected
       * @var string Delimiter between action keyword and action class within the action definition in url (url rewriting case!)
       */
      protected $__URLRewritingKeywordClassDelimiter = '/';

      /**
       * @protected
       * @var string Delimiter between input value couples.
       */
      protected $__InputDelimiter = '|';

      /**
       * @protected
       * @var string Delimiter between input value couples (url rewriting case!).
       */
      protected $__URLRewritingInputDelimiter = '/';

      /**
       * @protected
       * @var string Delimiter between input param name and value.
       */
      protected $__KeyValueDelimiter = ':';

      /**
       * @protected
       * @var string Delimiter between input param name and value (url rewrite case!).
       */
      protected $__URLRewritingKeyValueDelimiter = '/';

      /**
       * @protected
       * @var string Namespace of the Frontcontroller class.
       */
      protected $__Namespace = 'core::frontcontroller';

      public function Frontcontroller(){
      }

      /**
       * @public
       *
       * Executes the desired actions and creates the page output.
       *
       * @param string $namespace Namespace of the templates.
       * @param string $template Name of the templates.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.01.2007<br />
       * Version 0.2, 27.01.2007<br />
       * Version 0.3, 31.01.2007 (Context-Behandlung hinzugefï¿½gt)<br />
       * Version 0.4, 03.02.2007 (Permanente Actions hinzugefï¿½gt)<br />
       * Version 0.5, 08.06.2007 (URL-Filtering in generische Filter ausgelagert)<br />
       * Version 0.6, 01.07.2007 (Ausfï¿½hrung von permanentpre und permanentpost gelï¿½scht)<br />
       * Version 0.7, 29.09.2007 (Aufrufzeiten der Actions erweitert / geï¿½ndert)<br />
       * Version 0.8, 21.06.2008 (Introduced Registry to retrieve URLRewrite configuration)<br />
       * Version 0.9, 13.10.2008 (Removed $URLRewriting parameter, because URL rewriting must be configured in the registry)<br />
       * Version 1.0, 11.12.2008 (Switched to the new input filter concept)<br />
       */
      public function start($namespace,$template){

         // set URLRewrite
         $urlRewriting = Registry::retrieve('apf::core','URLRewriting');

         // check if the context is set. If not, use the current namespace
         if(empty($this->__Context)){
            $this->__Context = $namespace;
          // end if
         }

         // apply front controller input filter
         $filterDef = Registry::retrieve('apf::core::filter','FrontControllerInputFilter');

         if($filterDef !== null){
            $inputFilter = FilterFactory::getFilter($filterDef);
            $inputFilter->filter(null);
          // end if
         }

         // execute pre page create actions (see timing model)
         $this->runActions('prepagecreate');

         // create new page
         $page = new Page('FrontControllerPage',$urlRewriting);

         // set context
         $page->setContext($this->__Context);

         // set language
         $page->setLanguage($this->__Language);

         // load desired design
         $page->loadDesign($namespace,$template);

         // execute actions after page creation (see timing model)
         $this->runActions('postpagecreate');

         // execute actions before transformation (see timing model)
         $this->runActions('pretransform');

         // transform page
         $pageContent = $page->transform();

         // execute actions after page transformation (see timing model)
         $this->runActions('posttransform');

         // display page content
         echo $pageContent;

       // end function
      }

      /**
       * @public
       *
       * Returns the action specified by the input param.
       *
       * @param string $actionName The name of the action to return.
       * @return AbstractFrontcontrollerAction The desired action or null.
       *
       * @author Christian SchÃ¤fer
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
      public function &getActionByName($actionName){

         foreach($this->actionStack as $actionHash => $DUMMY){

            if($this->actionStack[$actionHash]->getActionName() == $actionName){
               return $this->actionStack[$actionHash];
             // end if
            }

          // end foreach
         }

         // return null, if action could not be found
         $null = null;
         return $null;

       // end function
      }

      /**
       * @public
       *
       * Returns the action stack.
       *
       * @return AbstractFrontcontrollerAction[] The front controller action stack.
       *
       * @author Christian SchÃ¤fer
       * @version
       * Version 0.1, 05.02.2007<br />
       */
      public function &getActions(){
         return $this->actionStack;
       // end function
      }

      /**
       * @private
       *
       * Creates the url representation of a given namespace.
       *
       * @param string $namespaceUrlRepresenation The url string.
       * @return The namespace of the action.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2007<br />
       */
      protected function getActionNamespaceByURLString($namespaceUrlRepresenation){
         return str_replace($this->__NamespaceURLDelimiter,'::',$namespaceUrlRepresenation);
       // end function
      }

      /**
       * @public
       *
       * Registers an action to the front controller. This includes action configuration using
       * the action oarams defined within the action mapping. Each action definition is expected
       * to be stored in the <em>{ENVIRONMENT}_actionsconfig.ini</em> file under the namespace
       * <em>{$namespace}::actions::{$this->__Context}.</em>
       *
       * @param string $namespace Namespace of the action to register.
       * @param string $name Name of the action to register.
       * @param array $params (Input-) params of the action.
       *
       * @author Christian SchÃ¤fer
       * @version
       * Version 0.1, 08.06.2007<br />
       * Version 0.2, 01.07.2007 (Action namespace is now translated at the addAction() method)<br />
       * Version 0.3, 01.07.2007 (Config params are now parsed correctly)<br />
       */
      public function registerAction($namespace,$name,$params = array()){

         $config = &$this->__getConfiguration($namespace.'::actions','actionconfig');

         if($config != null){

            // separate param strings
            if(strlen(trim($config->getValue($name,'FC.InputParams'))) > 0){

               // separate params
               $staticParams = explode($this->__InputDelimiter,$config->getValue($name,'FC.InputParams'));

               for($i = 0; $i < count($staticParams); $i++){

                  if(substr_count($staticParams[$i],$this->__KeyValueDelimiter) > 0){

                     $pairs = explode($this->__KeyValueDelimiter,$staticParams[$i]);

                     // re-order and add to param list
                     if(isset($pairs[0]) && isset($pairs[1])){
                        $params = array_merge($params,array($pairs[0] => $pairs[1]));
                      // end if
                     }

                   // end if
                  }

                // end for
               }

             // end if
            }

          // end if
         }

         $this->addAction($namespace,$name,$params);

       // end function
      }

      /**
       * @public
       *
       * Adds an action to the Frontcontroller action stack. Please note, that the namespace of
       * the namespace of the action config is remapped using the <em>::action</em> suffix and
       * the current context. The name of the config file is concatenated by the current
       * environment and the string <em>_actionsconfig.ini</em>.
       *
       * @param string $namespace Namespace of the action.
       * @param string $name Name of the action (section key of the config file).
       * @param string[] $params (Input-)params of the action.
       * @throws InvalidArgumentException In case the action cannot be found within the appropriate
       * configuration or the action implementation classes are not available.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.06.2007<br />
       * Version 0.2, 01.07.2007<br />
       * Version 0.3, 02.09.2007<br />
       * Version 0.4, 08.09.2007 (Bugfix: input params from config are now evaluated)<br />
       * Version 0.5, 08.11.2007 (Changed action stack construction to hash offsets)<br />
       * Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT constant with a value from the Registry)<br />
       */
      public function addAction($namespace,$name,$params = array()){

         // re-map namespace
         $namespace = $this->getActionNamespaceByURLString($namespace);
         $namespace .= '::actions';

         // load the action configuration
         $config = &$this->__getConfiguration($namespace,'actionconfig');

         if($config == null){
            throw new InvalidArgumentException('[Frontcontroller::__parseActions()] No '
                    .'configuration available for namespace "'.$namespace.'" and context "'
                    .$this->__Context.'"!',E_USER_ERROR);
            exit;
          // end if
         }
         $actionConfig = $config->getSection($name);

         // throw exception, in case the action config is not present
         if($actionConfig == null){
            $env = Registry::retrieve('apf::core','Environment');
            throw new InvalidArgumentException('[Frontcontroller::addAction()] No config '
                    .'section for action key "'.$name.'" available in configuration file "'.$env
                    .'_actionconfig.ini" in namespace "'.$namespace.'" and context "'
                    .$this->__Context.'"!',E_USER_ERROR);
         }

         // include action implementation
         import($actionConfig['FC.ActionNamespace'],$actionConfig['FC.ActionFile']);

         // include input implementation
         import($actionConfig['FC.ActionNamespace'],$actionConfig['FC.InputFile']);

         // check for class beeing present
         if(!class_exists($actionConfig['FC.ActionClass']) || !class_exists($actionConfig['FC.InputClass'])){
            throw new InvalidArgumentException('[Frontcontroller::addAction()] Action class with name "'
                    .$actionConfig['FC.ActionClass'].'" or input class with name "'
                    .$actionConfig['FC.InputClass'].'" could not be found. Please check your action '
                    . 'configuration file!',E_USER_ERROR);
         }

         // init action
         $action = new $actionConfig['FC.ActionClass'];
         $action->setActionNamespace($namespace);
         $action->setActionName($name);
         $action->setContext($this->__Context);
         $action->setLanguage($this->__Language);

         // init input
         $input = new $actionConfig['FC.InputClass'];

         // merge input params with the configured params (params included in the URL are kept!)
         $input->setAttributes(array_merge(
                 $this->generateParamsFromInputConfig($actionConfig['FC.InputParams']),
                 $params));

         $input->setParentObject($action);
         $action->setInput($input);

         // set the frontcontroller as a parent object to the action
         $action->setParentObject($this);

         // add the action as a child
         $this->actionStack[md5($namespace.'~'.$name)] = $action;

       // end function
      }

      /**
       * @protected
       *
       * Create an array from a input param string (scheme: <code>a:b|c:d</code>).
       *
       * @param string $inputConfig The config string contained in the action config.
       * @return string[] The resulting param-value array.
       *
       * @author Christian W. Schäfer
       * @version
       * Version 0.1, 08.09.2007<br />
       */
      protected function generateParamsFromInputConfig($inputConfig = ''){

         $inputParams = array();

         $inputConfig = trim($inputConfig);

         if(strlen($inputConfig) > 0){

            // first: explode couples by "|"
            $paramsArray = explode($this->__InputDelimiter,$inputConfig);

            for($i = 0; $i < count($paramsArray); $i++){

               // second: explode key and value by ":"
               $tmpAry = explode($this->__KeyValueDelimiter,$paramsArray[$i]);

               if(isset($tmpAry[0]) && isset($tmpAry[1]) && !empty($tmpAry[0]) && !empty($tmpAry[1])){
                  $inputParams[$tmpAry[0]] = $tmpAry[1];
                // end if
               }

             // end foreach
            }

          // end if
         }

         return $inputParams;

       // end function
      }

      /**
       * @protected
       *
       * Executes all actions with the given type. Possible types are
       * <ul>
       * <li>prepagecreate</li>
       * <li>postpagecreate</li>
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
      protected function runActions($type = 'prepagecreate'){

         $t = &Singleton::getInstance('BenchmarkTimer');

         foreach($this->actionStack as $actionHash => $DUMMY){

            // only execute, when the current action has a suitable type
            if($this->actionStack[$actionHash]->getType() == $type && $this->actionStack[$actionHash]->isActive()){

               $id = get_class($this->actionStack[$actionHash]).'::run()';
               $t->start($id);

               $this->actionStack[$actionHash]->run();

               $t->stop($id);

            }

          // end for
         }

       // end function
      }

    // end class
   }
?>