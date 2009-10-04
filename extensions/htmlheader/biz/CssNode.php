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

   import('extensions::htmlheader::biz','HtmlNode');

   /**
    *  @namespace extensions::htmlheader::biz
    *  @Class cssNode
    *
    *  Css file node for HtmlHeaderManagers data.
    *
    *  @author Ralf Schubert
    *  @version 0.1, 25.09.2009<br>
    */
   class CssNode extends HtmlNode{

       protected $__namespace = null;
       protected $__filename = null;

       /**
        * Receives information and configures node.
        * @param string $namespace Namespace of stylesheet
        * @param string $filename Name of stylesheet (without .css)
        */
       public function CssNode($namespace, $filename){
           $this->__namespace = $namespace;
           $this->__filename = $filename;
           $this->__checksum = md5($namespace . $filename);
       }

       /**
        * Transforms the node into html.
        *
        * @return string The ready html-code.
        */
       public function transform(){
           $link = $this->__buildFCLink($this->__namespace, $this->__filename, 'css');
           return '<link href="' . $link . '" rel="stylesheet" type="text/css">
   ';
       }


   }
?>