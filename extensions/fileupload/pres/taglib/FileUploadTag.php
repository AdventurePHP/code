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

namespace APF\extensions\fileupload\pres\taglib;

use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;
use APF\extensions\htmlheader\biz\JsContentNode;
use Exception;


class FileUploadTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'multiple';
   }

   public function onParseTime() {
      // check for name attribute
      if ($this->normalizeName($this->getAttribute('name')) == null) {
         throw new Exception('[' . get_class($this) . '::onParseTime()] FileUpload tag attributes are not correct!
            Please check you template, the "name" attribute could not be found.', E_USER_ERROR);
      }

      // check for config
      $config = $this->getConfiguration('APF\extensions\fileupload', 'config.php');
      if(!$config->hasSection($this->normalizeName($this->getAttribute('name')))) {
         throw new Exception('[' . get_class($this) . '::onParseTime()] FileUpload configuration file is not correct.
            The section "' . $this->normalizeName($this->getAttribute('name')) . '" could not be found. Please check your configuration file.', E_USER_ERROR);
      }
   }

   public function transform() {

      // Zugriff auf HTML-Header-Manager
      /* @var $headerManager HtmlHeaderManager */
      $headerManager = $this->getServiceObject(HtmlHeaderManager::class);
      $node = new JsContentNode($this->createJsCode($this->normalizeName($this->getAttribute('name'))));
      $node->setAppendToBody(true);
      $headerManager->addNode($node);

      $actionLink = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\extensions\fileupload', 'FileUpload', ['name' => $this->normalizeName($this->getAttribute('name'))]);
      $html = '<input id="' . $this->getAttribute('id') . '" type="file" name="' . $this->getAttribute('name') . '" data-url="' . $actionLink . '" multiple>';

      return $html;
   }

   /**
    * Removes [] of the name-attribute if needed
    *
    * @param $name String The name-attribute of the tag
    * @return string The normalized name
    *
    * @author dave
    * @version
    * Version 0.1, 30.01.2017<br />
    */
   protected function normalizeName($name) {
      // remove [] of name-attribute if necessary
      if(substr($name, -2) === '[]') {
         return substr($name, 0, -2);
      } else {
         return $name;
      }
   }

   /**
    * @param $name String The name-attribute of the tag
    * @return string Creates dynamic JS-Code for the jQuery-plugin
    *
    * @author dave
    * @version
    * Version 0.1, 30.01.2017<br />
    */
   protected function createJsCode($name) {
      // load config
      $config = $this->getConfiguration('APF\extensions\fileupload', 'config.php');
      $section = $config->getSection($name);

      $id = '#' . $this->getAttribute('id');
      $actionLink = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\extensions\fileupload', 'FileUpload', ['name' => $name]);

      $code = '
         $(\'' . $id . '\').fileupload({
            url: \'' . $actionLink . '\',
            paramName: \'' . $name . '\',
            dataType: \'json\',
            maxFileSize: '. $section->getValue('max_file_size') .',
            acceptFileTypes: ' . $section->getValue('accept_file_types') . '
         });
         ';
      return $code;
   }
}