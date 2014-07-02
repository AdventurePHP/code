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
namespace APF\tools\html\taglib;

use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\service\APFService;
use APF\tools\html\model\FrontControllerImportTemplateModel;

/**
 * @package APF\tools\html\taglib
 * @class FrontControllerImportTemplateTag
 *
 * Implements the functionality of the &lt;core:importdesign /&gt; tag that loads the view to display from an application
 * model. This tag can be configured for any application case using the following attributes:
 * <ul>
 *   <li>model (mandatory): Fully-qualified model class.</li>
 *   <li>context (optional): Set's the context of the current node (incl. all children)</li>
 *   <li>sessionsingleton (optional): defines, if the model is retrieved sessionsingleton or just singleton (values: true|false)</li>
 * </ul>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.11.2007<br />
 * Version 0.2, 22.06.2014 (ID#207: Refactored tag during separation of attributes handling from APFObject)<br />
 */
class FrontControllerImportTemplateTag extends ImportTemplateTag {

   public function onParseTime() {

      // switch context if desired
      $context = $this->getAttribute('context');
      if ($context !== null) {
         $this->setContext($context);
      }

      $modelCreationMode = $this->getAttribute('sessionsingleton', 'false') === 'true'
            ? APFService::SERVICE_TYPE_SESSION_SINGLETON
            : APFService::SERVICE_TYPE_SINGLETON;

      $modelImplementation = $this->getRequiredAttribute('model');

      // read the name of the template from the model
      /* @var $model FrontControllerImportTemplateModel */
      $model = & $this->getServiceObject($modelImplementation, $modelCreationMode);

      $this->loadContentFromFile($model->getTemplateNamespace(), $model->getTemplateName());

      $this->extractDocumentController();

      $this->extractTagLibTags();

   }

}
