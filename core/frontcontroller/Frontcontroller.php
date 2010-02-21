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
   $reg = &Singleton::getInstance('Registry');
   $reg->register('apf::core::filter','FrontControllerInputFilter',new FilterDefinition('core::filter','FrontControllerInputFilter'));
   $reg->register('apf::core::filter','PageControllerInputFilter',null);

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
    * Version 0.3, 08.11.2007 (Standardwert von KeepInURL auf false gesetzt)<br />
    */
   abstract class AbstractFrontcontrollerAction extends APFObject {

      /**
       * @private
       * @var string The namespace of the action.
       */
      protected $__ActionNamespace;

      /**
       * @private
       * @var string The name of the action (used to identify the action within the action stack).
       */
      protected $__ActionName;

      /**
       * @private
       * @var FrontcontrollerInput Input object of the action.
       */
      protected $__Input;

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
      protected $__Type = 'prepagecreate';

      /**
       * @private
       * @var boolean Indicates, if the action should be included in the URL. Values: true | false.
       */
      protected $__KeepInUrl = false;

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
         return $this->__Input;
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
         $this->__Input = $input;
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
         $this->__ActionName = $name;
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
         return $this->__ActionName;
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
         $this->__ActionNamespace = $namespace;
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
         return $this->__ActionNamespace;
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
         $this->__Type = $type;
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
         return $this->__Type;
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
         $this->__KeepInUrl = $keepInUrl;
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
         return $this->__KeepInUrl;
      }

      /**
       * @public 
       * @abstract
       *
       * Defines the interface method, that must be implemented by each concrete action. The method
       * is called by the front controller, when the action is executed.
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
       * @param boolean $urlRewriting True for activated url rewriting, false instead
       * @return string $attributesString URL formatted attributes string
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2007<br />
       * Version 0.2, 08.11.2007 (Fehler bei leerem Input-Objekt korrigiert)<br />
       * Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
       */
      function getAttributesAsString($urlRewriting = null){

         // get the current front controller
         $action = &$this->__ParentObject;
         $fC = &$action->getParentObject();

         // set URLRewriting manually
         if($urlRewriting === null){
            $reg = &Singleton::getInstance('Registry');
            $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
          // end if
         }

         // define url delimiter
         if($urlRewriting == true){
            $InputDelimiter = $fC->get('URLRewritingInputDelimiter');
            $KeyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
          // end if
         }
         else{
            $InputDelimiter = $fC->get('InputDelimiter');
            $KeyValueDelimiter = $fC->get('KeyValueDelimiter');
          // end else
         }

         // fill consolidated attributes array
         $AttributesArray = array();
         if(count($this->__Attributes) > 0){

            foreach($this->__Attributes as $Key => $Value){
               $AttributesArray[] = $Key.$KeyValueDelimiter.$Value;
             // end if
            }

          // end if
         }

         return implode($InputDelimiter,$AttributesArray);

       // end function
      }

    // end class
   }

   /**
    * @package core::frontcontroller
    * @class Frontcontroller
    *
    * Implements the APF front controller. He enables the developer to execute actions
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
      protected $__Actions = array();

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
       * Version 0.3, 31.01.2007 (Context-Behandlung hinzugef�gt)<br />
       * Version 0.4, 03.02.2007 (Permanente Actions hinzugef�gt)<br />
       * Version 0.5, 08.06.2007 (URL-Filtering in generische Filter ausgelagert)<br />
       * Version 0.6, 01.07.2007 (Ausf�hrung von permanentpre und permanentpost gel�scht)<br />
       * Version 0.7, 29.09.2007 (Aufrufzeiten der Actions erweitert / ge�ndert)<br />
       * Version 0.8, 21.06.2008 (Introduced Registry to retrieve URLRewrite configuration)<br />
       * Version 0.9, 13.10.2008 (Removed $URLRewriting parameter, because URL rewriting must be configured in the registry)<br />
       * Version 1.0, 11.12.2008 (Switched to the new input filter concept)<br />
       */
      public function start($namespace,$template){

         // set URLRewrite
         $reg = &Singleton::getInstance('Registry');
         $urlRewriting = $reg->retrieve('apf::core','URLRewriting');

         // check if the context is set. If not, use the current namespace
         if(empty($this->__Context)){
            $this->__Context = $namespace;
          // end if
         }

         // apply front controller input filter
         $filterDef = $reg->retrieve('apf::core::filter','FrontControllerInputFilter');

         if($filterDef !== null){
            $inputFilter = FilterFactory::getFilter($filterDef);
            $inputFilter->filter(null);
          // end if
         }

         // execute pre page create actions (see timing model)
         $this->__runActions('prepagecreate');

         // create new page
         $page = new Page('FrontControllerPage',$urlRewriting);

         // set context
         $page->setContext($this->__Context);

         // set language
         $page->setLanguage($this->__Language);

         // load desired design
         $page->loadDesign($namespace,$template);

         // execute actions after page creation (see timing model)
         $this->__runActions('postpagecreate');

         // execute actions before transformation (see timing model)
         $this->__runActions('pretransform');

         // transform page
         $pageContent = $page->transform();

         // execute actions after page transformation (see timing model)
         $this->__runActions('posttransform');

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
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 05.02.2007<br />
       * Version 0.2, 08.02.2007 (Noch nicht geladene Actions werden lazy nachgeladen und zur�ckgeliefert)<br />
       * Version 0.3, 11.02.2007 (ActionName ist nun der Name der Section, bzw. des Alias der Action)<br />
       * Version 0.4, 01.03.2007 (Input-Objekte werden nun vom Frontcontroller geladen!)<br />
       * Version 0.5, 01.03.2007 (Pr�fung ob Action-Klasse vorhanden ist hinzugef�gt!)<br />
       * Version 0.6, 08.03.2007 (Auf neuen configurationManager umgestellt)<br />
       * Version 0.7, 08.06.2007 (Automatisches Neuerstellen einer Action entfernt)<br />
       * Version 0.8, 08.11.2007 (Umstellung auf Hash-Offsets nachgezogen)<br />
       */
      public function &getActionByName($actionName){

         foreach($this->__Actions as $actionHash => $DUMMY){

            if($this->__Actions[$actionHash]->getActionName() == $actionName){
               return $this->__Actions[$actionHash];
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
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.02.2007<br />
       */
      public function &getActions(){
         return $this->__Actions;
       // end function
      }

      /**
      *  @private
      *
      *  Erzeugt aus einem URL-Namespace einen regul�ren Namespace.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      protected function __getActionNamespaceByURLString($NamespaceString){
         return str_replace($this->__NamespaceURLDelimiter,'::',$NamespaceString);
       // end function
      }

      /**
       * @public
       *
       * Registriert eine Action beim FC und l�d die Parameter des Models aus einem Config-File.<br />
       * Erwartet eine Konfigurationsdatei mit Namen {APPS__ENVIRONMENT}_actionsconfig.ini unter<br />
       * dem Pfad {$ActionNamespace}::actions::{$this->__Context}.<br />
       *
       * @param string $ActionNamespace; Namespace der Action
       * @param string $ActionName; Name der Action
       * @param array $ActionParams; (Input-)Parameter der Action
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 08.06.2007<br />
       * Version 0.2, 01.07.2007 (ActionNamespace wird nun zentral in addAction() �bersetzt)<br />
       * Version 0.3, 01.07.2007 (Parsen der Config-Parameter wird nun korrekt durchgef�hrt)<br />
       */
      public function registerAction($ActionNamespace,$ActionName,$ActionParams = array()){

         // Config f�r Input laden
         $Config = &$this->__getConfiguration($ActionNamespace.'::actions','actionconfig');

         if($Config != null){

            // Parameter-Wert-Strings trennen
            if(strlen(trim($Config->getValue($ActionName,'FC.InputParams'))) > 0){

               // Parameter trennen
               $Params = explode($this->__InputDelimiter,$Config->getValue($ActionName,'FC.InputParams'));

               for($i = 0; $i < count($Params); $i++){

                  // Parameter und Wert trennen
                  if(substr_count($Params[$i],$this->__KeyValueDelimiter) > 0){

                     $ParamValuePair = explode($this->__KeyValueDelimiter,$Params[$i]);

                     // Paar zu den ActionParams hinzuf�gen
                     if(isset($ParamValuePair[0]) && isset($ParamValuePair[1])){
                        $ActionParams = array_merge($ActionParams,array($ParamValuePair[0] => $ParamValuePair[1]));
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

         $this->addAction($ActionNamespace,$ActionName,$ActionParams);

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
         $namespace = $this->__getActionNamespaceByURLString($namespace);
         $namespace .= '::actions';

         // load the action configuration
         $config = &$this->__getConfiguration($namespace,'actionconfig');

         if($config == null){
            trigger_error('[Frontcontroller::__parseActions()] No configuration available for namespace "'.$namespace.'" and context "'.$this->__Context.'"!',E_USER_ERROR);
            exit;
          // end if
         }
         $actionConfig = $config->getSection($name);

         // throw exception, in case the action config is not present
         if($actionConfig == null){
            $reg = &Singleton::getInstance('Registry');
            $env = $reg->retrieve('apf::core','Environment');
            throw new Exception('[Frontcontroller::__parseActions()] No config section for action key "'
                    .$name.'" available in configuration file "'.$env.'_actionconfig.ini" in namespace "'
                    .$namespace.'" and context "'.$this->__Context.'"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // include action implementation
         import($actionConfig['FC.ActionNamespace'],$actionConfig['FC.ActionFile']);

         // include input implementation
         import($actionConfig['FC.ActionNamespace'],$actionConfig['FC.InputFile']);

         // check for class beeing present
         if(!class_exists($actionConfig['FC.ActionClass']) || !class_exists($actionConfig['FC.InputClass'])){
            throw new Exception('[Frontcontroller::__parseActions()] Action class with name "'
                    .$actionConfig['FC.ActionClass'].'" or input class with name "'
                    .$actionConfig['FC.InputClass'].'" could not be found. Please check your action '
                    . 'configuration file!',E_USER_ERROR);
            exit(1);
          // end if
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
                 $this->__generateParamsFromInputConfig($actionConfig['FC.InputParams']),
                 $params));

         $input->setParentObject($action);
         $action->setInput($input);

         // set the frontcontroller as a parent object to the action
         $action->setParentObject($this);

         // add the action as a child
         $this->__Actions[md5($namespace.'~'.$name)] = $action;

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
      protected function __generateParamsFromInputConfig($inputConfig = ''){

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
       * Executes all actions with the given type.
       *
       * @param string $type Type of the actions to execute (prepagecreate | postpagecreate | pretransform | posttransform).
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.01.2007<br />
       * Version 0.2, 31.01.2007<br />
       * Version 0.3, 03.02.2007 (Added benchmarker)<br />
       * Version 0.4, 01.07.2007 (Removed debug output)<br />
       * Version 0.5, 08.11.2007<br />
       * Version 0.6, 28.03.2008 (Optimized benchmarker call)<br />
       */
      protected function __runActions($type = 'prepagecreate'){

         $t = &Singleton::getInstance('BenchmarkTimer');

         foreach($this->__Actions as $actionHash => $DUMMY){

            // only execute, when the current action has a suitable type
            if($this->__Actions[$actionHash]->getType() == $type){

               $id = get_class($this->__Actions[$actionHash]).'::run()';
               $t->start($id);

               $this->__Actions[$actionHash]->run();

               $t->stop($id);

             // end if
            }

          // end for
         }

       // end function
      }

    // end class
   }
?>