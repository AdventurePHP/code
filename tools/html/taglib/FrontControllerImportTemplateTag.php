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
use APF\core\service\APFService;

/**
 * @package APF\tools\html\taglib
 * @class FrontControllerImportTemplateTag
 *
 * Implements the functionality of the &lt;core:importdesign /&gt; tag that loads the view to display from an application
 * model. This tag can be configured for any application case using the following attributes:
 * <ul>
 *   <li>templatenamespace: Namespace of the template</li>
 *   <li>modelclass: Fully-qualified model class</li>
 *   <li>modelparam: Name of the model parameter to use as the template name</li>
 *   <li>context: Set's the context of the current node (incl. all children)</li>
 *   <li>sessionsingleton: defines, if the model is retrieved sessionsingleton or just singleton (values: true|false)</li>
 * </ul>
 * All attributes except <em>context</em> are mandatory.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2007<br />
 */
class FrontControllerImportTemplateTag extends ImportTemplateTag {

   public function onParseTime() {

      if (!isset($this->attributes['templatenamespace'])) {
         throw new \InvalidArgumentException('[FrontControllerImportTemplateTag::onParseTime()] Attribute "templatenamespace" is not given!');
      } else {
         $templateNamespace = $this->attributes['templatenamespace'];
      }

      if (!isset($this->attributes['modelclass'])) {
         throw new \InvalidArgumentException('[FrontControllerImportTemplateTag::onParseTime()] Attribute "modelclass" is not given!');
      } else {
         $modelClass = $this->attributes['modelclass'];
      }

      if (!isset($this->attributes['modelparam'])) {
         throw new \InvalidArgumentException('[FrontControllerImportTemplateTag::onParseTime()] Attribute "modelparam" is not given!');
      } else {
         $modelParam = $this->attributes['modelparam'];
      }

      // get initialization mode
      if (!isset($this->attributes['sessionsingleton']) || $this->attributes['sessionsingleton'] == 'false') {
         $initMode = APFService::SERVICE_TYPE_SINGLETON;
      } else {
         $initMode = APFService::SERVICE_TYPE_SESSION_SINGLETON;
      }

      // read the name of the template from the model
      $model = & $this->getServiceObject($modelClass, $initMode);
      $templateName = $model->getAttribute($modelParam);

      if (isset($this->attributes['context'])) {
         $this->context = trim($this->attributes['context']);
      }

      $this->loadContentFromFile($templateNamespace, $templateName);

      $this->extractDocumentController();

      $this->extractTagLibTags();

   }

}
