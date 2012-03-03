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
import('extensions::htmlheader::biz', 'DynamicCssNode');

/**
 * @package extensions::htmlheader::pres::taglib
 * @class htmlheader_taglib_addcss
 *
 *  Taglib for adding stylesheets to htmlheader.
 *
 * @example
 *  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addcss" />
 *  Use FC-action to deliver file:
 *  <htmlheader:addcss namespace="{CONTEXT}::pres::frontend::static::css:anything" filename="examplefile" />
 *  <ul>
 *    <li>namespace: Namespace of stylesheet file</li>
 *    <li>filename: Stylesheet filename without '.css'</li>
 *  </ul>
 *
 *  Use External file:
 *  <htmlheader:addcss
 *    url="http://static/"
 *    folder="css::anything"
 *    filename="examplefile"
 *    rewriting="false"
 *    fcaction="false"
 *  />
 *  <ul>
 *    <li>url: URL of file server</li>
 *    <li>folder: Folder of css file</li>
 *    <li>filename: Css filename without '.css'</li>
 *    <li>rewriting: Rewriting of target server enabled? (optional, option will be used from actual application otherwise)
 *    <li>fcaction: Use an fc-action on target server? (optional, will be set to true by default)
 *  </ul>
 *
 * @author Ralf Schubert
 * @version
 *  0.1, 20.09.2009 <br />
 *  0.2, 27.02.2010 (Added attributes for external file support) <br />
 */
class htmlheader_taglib_addcss extends Document {

   public function onParseTime() {
   }

   public function transform() {
      $header = &$this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');

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
         $node = new DynamicCssNode($url, $folder, $filename, $rewriting, $fcaction);
      } else {
         $node = new DynamicCssNode(null, $namespace, $filename, $rewriting, $fcaction);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}

?>