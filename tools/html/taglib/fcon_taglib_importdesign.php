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
 * @package tools::html::taglib
 * @class fcon_taglib_importdesign
 *
 * Implements the functionality of the &lt;core:importdesign /&gt; tag that loads the view to display from an application
 * model. This tag can be configured for any application case using the following attributes:
 * <ul>
 *   <li>templatenamespace: Namespace of the template</li>
 *   <li>modelnamespace: Namespace of the application model</li>
 *   <li>modelclass: Name of the model class</li>
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
class fcon_taglib_importdesign extends core_taglib_importdesign {

   public function onParseTime() {

      /* @var $t BenchmarkTimer */
      $t = &Singleton::getInstance('BenchmarkTimer');
      $t->start('(fcon_taglib_importdesign) ' . $this->__ObjectID . '::onParseTime()');

      if (!isset($this->__Attributes['templatenamespace'])) {
         throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "templatenamespace" is not given!');
      } else {
         $templateNamespace = $this->__Attributes['templatenamespace'];
      }

      if (!isset($this->__Attributes['modelnamespace'])) {
         throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelnamespace" is not given!');
      } else {
         $modelNamespace = $this->__Attributes['modelnamespace'];
      }

      if (!isset($this->__Attributes['modelclass'])) {
         throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelclass" is not given!');
      } else {
         $modelClass = $this->__Attributes['modelclass'];
      }

      if (!isset($this->__Attributes['modelparam'])) {
         throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelparam" is not given!');
      } else {
         $modelParam = $this->__Attributes['modelparam'];
      }

      if (!class_exists($modelClass)) {
         import($modelNamespace, $modelClass);
      }

      // get initialization mode
      if (!isset($this->__Attributes['sessionsingleton']) || $this->__Attributes['sessionsingleton'] == 'false') {
         $initMode = APFService::SERVICE_TYPE_SINGLETON;
      } else {
         $initMode = APFService::SERVICE_TYPE_SESSION_SINGLETON;
      }

      // read the name of the template from the model
      $model = &$this->getServiceObject($modelNamespace, $modelClass, $initMode);
      $templateName = $model->getAttribute($modelParam);

      if (isset($this->__Attributes['context'])) {
         $this->__Context = trim($this->__Attributes['context']);
      }

      $this->__loadContentFromFile($templateNamespace, $templateName);

      $this->__extractDocumentController();

      $this->__extractTagLibTags();

      $t->stop('(fcon_taglib_importdesign) ' . $this->__ObjectID . '::onParseTime()');

   }

}
