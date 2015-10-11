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
namespace APF\extensions\htmlheader\biz;

use APF\core\pagecontroller\Document;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * General node for HtmlHeaderManagers data.
 *
 * @author Ralf Schubert
 * @version
 * 0.1, 25.09.2009 <br />
 * 0.2, 27.02.2010 (Added external file support) <br />
 */
abstract class HtmlNode extends Document implements HeaderNode {

   /**
    * The priority of the header node.
    *
    * @var int $priority
    */
   private $priority = 0;

   /**
    * Defines if the taglib should be set to gethead or getjsbody taglib.
    *
    * @var bool $appendToBody
    */
   private $appendToBody = false;

   public function getPriority() {
      return $this->priority;
   }

   public function setPriority($priority) {
      $this->priority = intval($priority); // normalize priority to 0 for all faulty inputs
      return $this;
   }

   public function getAppendToBody() {
      return $this->appendToBody;
   }

   public function setAppendToBody($value) {
      $this->appendToBody = $value;

      return $this;
   }

   /**
    * Transforms the node to html.
    *
    * @return string The ready html code.
    */
   public function transform() {

      $attributes = $this->getAttributes();

      $html = '<' . $this->getTagName();

      if (count($attributes) > 0) {
         $html .= ' ' . $this->getAttributesAsString($attributes);
      }

      $content = $this->getContent();
      if ($content === null) {
         $html .= ' />';
      } else {
         $html .= '>' . $content . '</' . $this->getTagName() . '>';
      }

      return $html;
   }

   /**
    * @return string The name of the current html tag.
    */
   protected abstract function getTagName();

   /**
    * Builds a Link for the JsCssInclusion FC-action
    *
    * @param string $url Optional url.
    * @param string $namespace Namespace of file
    * @param string $filename Name of file
    * @param bool $fcaction Optional. Create link for FC-Action.
    * @param string $type Filetype
    *
    * @return string elements' link.
    */
   protected function buildFrontcontrollerLink($url, $namespace, $filename, $fcaction, $type) {

      if ($fcaction === null) {
         $fcaction = true;
      }

      if ($fcaction) {
         $UrlObj = ($url === null) ? Url::fromCurrent(true) : Url::fromString($url);

         return LinkGenerator::generateActionUrl($UrlObj, 'APF\extensions\htmlheader', 'JsCss', [
               'path' => str_replace('\\', '_', $namespace),
               'type' => $type,
               'file' => $filename
         ]);
      } else {
         $namespace = str_replace('\\', '/', $namespace);
         $url .= (substr($url, -1, 1) !== '/') ? '/' : '';

         return $url . $namespace . '/' . $filename . '.' . $type;
      }
   }

}
