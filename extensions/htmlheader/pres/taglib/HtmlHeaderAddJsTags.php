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
namespace APF\extensions\htmlheader\pres\taglib;

use APF\core\pagecontroller\Document;
use APF\extensions\htmlheader\biz\DynamicJsNode;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;

/**
 *  Taglib for adding javascripts to htmlheader.
 *
 * @example
 *  <core:addtaglib class="APF\extensions\htmlheader\pres\taglib\HtmlHeaderAddJsTags" prefix="htmlheader" name="addjs" />
 *  Use FC-Action to deliver file:
 *  <htmlheader:addjs namespace="VENDOR\namespace\of\component\js\anything" filename="jsfile" />
 *  <ul>
 *    <li>namespace: Namespace of javascript file</li>
 *    <li>filename: Javascript filename without '.js'</li>
 *  </ul>
 *
 * Use External file:
 * <htmlheader:addjs
 *    url="http://static/"
 *    folder="js/anything"
 *    filename="jsfile"
 *    fcaction="false"
 * />
 *  <ul>
 *    <li>url: URL of file server</li>
 *    <li>folder: Folder of javascript file</li>
 *    <li>filename: Javascript filename without '.js'</li>
 *    <li>fcaction: Use an fc-action on target server? (optional, will be set to true by default)
 *    <li>appendtobody: If set to true, tag will not be included to htmlheader:gethead replacements, but to htmlheader:getbodyjs
 *  </ul>
 *
 * @author Ralf Schubert
 * @version
 *  0.1, 20.09.2009<br />
 *  0.2, 27.09.2009<br />
 *  0.3, 27.02.2010 (Added attributes for external file support) <br />
 */
class HtmlHeaderAddJsTags extends Document {

   public function transform() {

      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject(HtmlHeaderManager::class);

      $url = $this->getAttribute('url');
      $folder = $this->getAttribute('folder');
      $namespace = $this->getAttribute('namespace');
      $filename = $this->getAttribute('filename');

      $fcaction = $this->getAttribute('fcaction');

      if ($fcaction === 'true') {
         $fcaction = true;
      } elseif ($fcaction === 'false') {
         $fcaction = false;
      }

      if ($url !== null) {
         $node = new DynamicJsNode($url, $folder, $filename, $fcaction);
      } else {
         $node = new DynamicJsNode(null, $namespace, $filename, $fcaction);
      }

      if (strtolower($this->getAttribute('appendtobody')) === 'true') {
         $node->setAppendToBody(true);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
