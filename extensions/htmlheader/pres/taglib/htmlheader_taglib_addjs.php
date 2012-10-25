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
import('extensions::htmlheader::biz', 'DynamicJsNode');

/**
 * @package extensions::htmlheader::pres::taglib
 * @class htmlheader_taglib_addjs
 *
 *  Taglib for adding javascripts to htmlheader.
 *
 * @example
 *  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addjs" prefix="htmlheader" name="addjs" />
 *  Use FC-Action to deliver file:
 *  <htmlheader:addjs namespace="{CONTEXT}::pres::frontend::static::js::anything" filename="jsfile" />
 *  <ul>
 *    <li>namespace: Namespace of javascript file</li>
 *    <li>filename: Javascript filename without '.js'</li>
 *  </ul>
 *
 * Use External file:
 * <htmlheader:addjs
 *    url="http://static/"
 *    folder="js::anything"
 *    filename="jsfile"
 *    rewriting="false"
 *    fcaction="false"
 * />
 *  <ul>
 *    <li>url: URL of file server</li>
 *    <li>folder: Folder of javascript file</li>
 *    <li>filename: Javascript filename without '.js'</li>
 *    <li>rewriting: Rewriting of target server enabled? (optional, option will be used from actual application otherwise)
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
class htmlheader_taglib_addjs extends Document {

   public function transform() {

      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');

      $url = $this->getAttribute('url');
      $folder = $this->getAttribute('folder');
      $namespace = $this->getAttribute('namespace');
      $filename = $this->getAttribute('filename');

      $rewriting = $this->getAttribute('rewriting');
      $fcaction = $this->getAttribute('fcaction');

      if ($rewriting === 'true') {
         $rewriting = true;
      } elseif ($rewriting === 'false') {
         $rewriting = false;
      }

      if ($fcaction === 'true') {
         $fcaction = true;
      } elseif ($fcaction === 'false') {
         $fcaction = false;
      }

      if ($url !== null) {
         $node = new DynamicJsNode($url, $folder, $filename, $rewriting, $fcaction);
      } else {
         $node = new DynamicJsNode(null, $namespace, $filename, $rewriting, $fcaction);
      }

      if (strtolower($this->getAttribute('appendtobody')) === 'true') {
         $node->setAppendToBody(true);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
