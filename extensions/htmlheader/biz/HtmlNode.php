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

   import('tools::link', 'FrontcontrollerLinkHandler');
   import('extensions::htmlheader::biz','HeaderNode');

   /**
    * @abstract
    * @namespace extensions::htmlheader::biz
    * @class HtmlNode
    *
    * General node for HtmlHeaderManagers data.
    *
    * @author Ralf Schubert
    * @version
    * 0.1, 25.09.2009 <br />
    * 0.2, 27.02.2010 (Added external file support) <br />
    */
   abstract class HtmlNode extends APFObject implements HeaderNode {

      /**
       * @var string The content of the node.
       */
      private $content = null;

      public function getContent() {
         return $this->content;
      }

      public function setContent($content) {
         $this->content = $content;
      }

      /**
       * Transforms the node into html.
       *
       * @return string The ready html-code.
       */
      public function transform() {

         $attributes = $this->getAttributes();

         $html = '<'.$this->getTagName();

         if(count($attributes) > 0){
            $html .= ' '.$this->getAttributesAsString($attributes);
         }
         
         $content = $this->getContent();
         if($content === null){
            $html .= ' />';
         } else {
            $html .= '>'.$content.'</'.$this->getTagName().'>';
         }
         
         return $html;
      }

      /**
       * @return The name of the current html tag.
       */
      protected abstract function getTagName();

      /**
       * Builds a Link for the JsCssInclusion FC-action
       *
       * @param string $url Optional url.
       * @param string $namespace Namespace of file
       * @param string $filename Name of file
       * @param bool $urlRewriting Optional. Create rewriting Url.
       * @param bool $fcaction Optional. Create link for FC-Action.
       * @param string $type Filetype
       * @return string elements' link.
       */
      protected function __buildFCLink($url, $namespace, $filename, $urlRewriting, $fcaction, $type) {

         if ($urlRewriting === null) {
            $urlRewriting = Registry::retrieve('apf::core', 'URLRewriting');
         }
         if ($fcaction === null) {
            $fcaction = true;
         }

         // Generate url if not given
         if ($url === null) {
            if ($urlRewriting) {
               $url = Registry::retrieve('apf::core', 'URLBasePath');
            } else {
               $tmpPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
               $slash = (substr($tmpPath, 0, 1) !== '/') ? '/' : '';
               $url = 'http://' . $_SERVER['HTTP_HOST'] . $slash . $tmpPath;
            }

         }

         if ($fcaction) {
            $namespace = str_replace('::', '_', $namespace);

            if ($urlRewriting) {
               $actionParam = array(
                   'extensions_jscssinclusion_biz-action/sGCJ' => 'path/' . $namespace . '/type/' . $type . '/file/' . $filename
               );
            } else {

               $actionParam = array(
                   'extensions_jscssinclusion_biz-action:sGCJ' => 'path:' . $namespace . '|type:' . $type . '|file:' . $filename
               );
            }

            // return url
            return FrontcontrollerLinkHandler::generateLink($url, $actionParam);
         } else {
            $namespace = str_replace('::', '/', $namespace);
            $url .= ( substr($url, -1, 1) !== '/') ? '/' : '';

            //return url
            return $url . $namespace . '/' . $filename . '.' . $type;
         }
         
      }

   }
?>