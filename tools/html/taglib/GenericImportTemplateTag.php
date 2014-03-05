<?php
namespace APF\tools\html\taglib;

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
use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\singleton\Singleton;
use APF\core\frontcontroller\Frontcontroller;

/**
 * @package APF\tools\html\taglib
 * @class GenericImportTemplateTag
 *
 * Implements a fully generic including tag. The tag retrieves both namespace and the template
 * name from the desired model object. Further, the developer is free to choose, which mode is
 * used to fetch the model object from the ServiceManager. For details on the modes, please have
 * a look at the ServiceManager documentation. To use this tag, the following attributes must be
 * involved:
 * <pre>&lt;generic:importdesign
 *             model-class=""
 *             model-mode="NORMAL|SINGLETON|SESSIONSINGLETON"
 *             namespace-param=""
 *             template-param=""
 *             [get-method=""]
 *             [dependent-action-namespace="VENDOR\action\namespace"
 *             dependent-action-name="ActionName"
 *             [dependent-action-params="param1:value1|param2:value2"]]
 * /&gt;</pre>
 * The <em>dependentaction*</em> params can be used to register a dependent action to the front controller.
 * This optional mechanism can be used to have an action registered, that is used for navigation
 * purposes (aka click on link displays the start page instead of the module's view).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.10.2008<br />
 * Version 0.2, 01.11.2008 (Added documentation and introduced the modelmode and getmethode params)<br />
 */
class GenericImportTemplateTag extends ImportTemplateTag {

   /**
    * @public
    *
    * Handles the tag's attributes (ses class documentation) and includes the desired template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.10.2008<br />
    * Version 0.2, 01.11.2008 (Added the modelmode and getmethode params)<br />
    * Version 0.3, 29.12.2208 (Added the dependent action options)<br />
    */
   public function onParseTime() {

      // model-class=""
      $modelClass = $this->getAttribute('model-class');
      if ($modelClass === null) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The attribute "modelclass" is empty or not present. Please provide the name '
               . 'of the model class within this attribute!');
      }

      // model-mode="NORMAL|SINGLETON|SESSIONSINGLETON"
      $modelMode = $this->getAttribute('model-mode');
      if ($modelMode === null) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The attribute "modelmode" is empty or not present. Please provide the '
               . 'service type of the model within this attribute! Allowed values are '
               . 'NORMAL, SINGLETON or SESSIONSINGLETON.');
      }

      // namespace-param=""
      $namespaceParam = $this->getAttribute('namespace-param');
      if ($namespaceParam === null) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The attribute "namespaceparam" is empty or not present. Please provide the '
               . 'name of the model param for the namespace of the template file within this '
               . 'attribute!');
      }

      // template-param=""
      $templateParam = $this->getAttribute('template-param');
      if ($templateParam === null) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] The '
               . 'attribute "templateparam" is empty or not present. Please provide the name '
               . 'of the model param for the name of the template file within this attribute!');
      }

      // get-method="" (e.g. "getAttribute" or "get")
      $getMethod = $this->getAttribute('get-method');
      if ($getMethod === null) {
         $getMethod = 'getAttribute';
      }

      // register dependent action (needed, if the action is used for navigation purposes)
      $dependentActionNamespace = $this->getAttribute('dependent-action-namespace');
      $dependentActionName = $this->getAttribute('dependent-action-name');
      $dependentActionParams = $this->getAttribute('dependent-action-params');

      if ($dependentActionNamespace !== null && $dependentActionName !== null) {

         // create param list
         $actionParamList = array();
         if ($dependentActionParams !== null) {

            $paramPieces = explode('|', $dependentActionParams);

            foreach ($paramPieces as $piece) {
               $temp = explode(':', $piece);
               if (isset($temp[1])) {
                  $actionParamList[trim($temp[0])] = trim($temp[1]);
               }
            }
         }

         // register action with the front controller
         /* @var $fC Frontcontroller */
         $fC = &Singleton::getInstance('APF\core\frontcontroller\Frontcontroller');
         $action = &$fC->getActionByName($dependentActionName);
         if ($action === null) {
            $fC->addAction($dependentActionNamespace, $dependentActionName, $actionParamList);
         }
      }

      // get model
      $model = &$this->getServiceObject($modelClass, $modelMode);

      // check for the get method
      if (!method_exists($model, $getMethod)) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The model class ("' . $modelClass . '") does not support the method "' . $getMethod
               . '" provided within the "getmethod" attribute. Please provide the correct '
               . 'function name!');
      }

      // read the params from the model
      $templateNamespace = $model->$getMethod($namespaceParam);
      if (empty($templateNamespace)) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The model ("' . $modelClass . '") returned an empty value when trying to get '
               . 'the template namespace using the "' . $getMethod . '" method! Please specify '
               . 'another getter or check the model class implementation!');
      }

      $templateName = $model->$getMethod($templateParam);
      if (empty($templateName)) {
         throw new \InvalidArgumentException('[GenericImportTemplateTag::onParseTime()] '
               . 'The model ("' . $modelClass . '") returned an empty value when trying to get '
               . 'the template name using the "' . $getMethod . '" method! Please specify another '
               . 'getter or check the model class implementation!');
      }

      // import desired template
      $this->loadContentFromFile($templateNamespace, $templateName);
      $this->extractDocumentController();
      $this->extractTagLibTags();
   }

}