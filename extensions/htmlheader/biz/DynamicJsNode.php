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

   import('extensions::htmlheader::biz', 'HtmlNode');
   import('extensions::htmlheader::biz', 'JsNode');

   /**
    * @namespace extensions::htmlheader::biz
    * @class DynamicJsNode
    *
    * Js file node for HtmlHeaderManagers data.
    *
    * @author Ralf Schubert
    * @version
    * 0.1, 20.09.2009 <br />
    * 0.2, 27.02.2010 (Added external file support) <br />
    */
   class DynamicJsNode extends HtmlNode implements JsNode {

      /**
       * Receives information and configures node.
       * @param string $url Optional url.
       * @param string $namespace Namespace of file
       * @param string $filename Name of file (without .js)
       * @param bool $urlRewriting Optional. Create rewriting Url.
       * @param bool $fcaction Optional. Create link for FC-Action.
       */
      public function __construct($url, $namespace, $filename, $rewriting = null, $fcaction = null) {
         $this->setAttribute('src', $this->buildFrontcontrollerLink(
                         $url,
                         $namespace,
                         $filename,
                         $rewriting,
                         $fcaction,
                         'js'
         ));
         $this->setAttribute('type', 'text/javascript');
         $this->setContent(''); // empty content but existent!
      }

      public function getTagName() {
         return 'script';
      }

      public function getChecksum() {
         return md5($this->getAttribute('src'));
      }

   }
?>