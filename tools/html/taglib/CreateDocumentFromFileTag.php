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
use APF\core\pagecontroller\Document;
use APF\tools\request\RequestHandler;

/**
 * @package APF\tools\html\taglib\doc
 * @class CreateDocumentFromFileTag
 *
 * Implements a taglib that creates a child node by the content of a file.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 04.01.2006<br />
 * Version 0.2, 29.09.2007 (Renamed to CreateDocumentFromFileTag)<br />
 */
class CreateDocumentFromFileTag extends Document {

   public function onParseTime() {

      // get the attributes
      $requestParameter = $this->getAttribute('requestparam');
      $defaultValue = $this->getAttribute('defaultvalue');

      // get current request param
      $currentRequestParameter = RequestHandler::getValue($requestParameter, $defaultValue);

      // fill content
      $this->content = $this->loadContent($currentRequestParameter);

      // extract tags and document controller
      $this->extractTagLibTags();
      $this->extractDocumentController();

   }

   public function onAfterAppend() {
      $this->extractExpressionTags();
   }

   /**
    * @protected
    *
    * Reads the content of a file using the param to indicate it's name. If the file does not
    * exist, a file with name "404" is taken instead.
    *
    * @param string $pageName The name of the page content file to load.
    * @return string The content of the file.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 30.05.2006<br />
    * Version 0.2, 31.05.2006 (Path changed from /APF/sites  to ./frontend/content)<br />
    * Version 0.3, 29.09.2007 (Introduced the language in the filename)<br />
    */
   protected function loadContent($pageName) {

      $file = './frontend/content/c_' . $this->language . '_' . strtolower($pageName) . '.html';

      if (!file_exists($file)) {
         $file = './frontend/content/c_' . $this->language . '_404.html';
      }

      return file_get_contents($file);

   }

}
