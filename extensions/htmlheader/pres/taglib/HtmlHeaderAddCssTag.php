<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
use APF\extensions\htmlheader\biz\DynamicCssNode;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;

/**
 * Taglib for adding stylesheets to html header.
 *
 * @example
 *  <core:addtaglib class="APF\extensions\htmlheader\pres\taglib\HtmlHeaderAddCssTag" prefix="htmlheader" name="addcss" />
 *  Use FC-action to deliver file:
 *  <htmlheader:addcss namespace="VENDOR\namespace\of\component\css\anything" filename="examplefile" />
 *  <ul>
 *    <li>namespace: Namespace of stylesheet file</li>
 *    <li>filename: Stylesheet filename without '.css'</li>
 *  </ul>
 *  Use External file:
 *  <htmlheader:addcss
 *    url="http://static/"
 *    folder="css/anything"
 *    filename="examplefile"
 *    fcaction="false"
 *    [media=""]
 *  />
 *  <ul>
 *    <li>url: URL of file server</li>
 *    <li>folder: Folder of css file</li>
 *    <li>filename: Css filename without '.css'</li>
 *    <li>fcaction: Use an fc-action on target server? (optional, will be set to true by default)
 *    <li>media: The media type of the css file (e.g. "screen", "print", or any other media query)</li>
 *  </ul>
 *
 * @author Ralf Schubert
 * @version
 *  0.1, 20.09.2009 <br />
 *  0.2, 27.02.2010 (Added attributes for external file support) <br />
 */
class HtmlHeaderAddCssTag extends Document {

   public function onParseTime() {
   }

   public function transform() {

      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject(HtmlHeaderManager::class);

      $url = $this->getAttribute('url');
      $folder = $this->getAttribute('folder');
      $namespace = $this->getAttribute('namespace');
      $filename = $this->getAttribute('filename');

      $fcaction = $this->getAttribute('fcaction');

      $media = $this->getAttribute('media');

      if ($fcaction === 'true') {
         $fcaction = true;
      } elseif ($fcaction === 'false') {
         $fcaction = false;
      }

      if ($url !== null) {
         $node = new DynamicCssNode($url, $folder, $filename, $fcaction, $media);
      } else {
         $node = new DynamicCssNode(null, $namespace, $filename, $fcaction, $media);
      }

      $node->setPriority(intval($this->getAttribute('priority')));
      $header->addNode($node);

      return '';
   }

}
