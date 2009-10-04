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
    * @namespace extensions::htmlheader::biz
    * @class RefreshNode
    *
    * Meta refresh node for HtmlHeaderManagers data.
    *
    * @author Ralf Schubert
    * @version 0.1, 25.09.2009<br>
    */
   class RefreshNode extends HtmlNode{

       protected $__url = null;
       protected $__time = null;
       protected $__parameter = array();

       /**
        * Receives information and configures node.
        * @param string $namespace Namespace of stylesheet
        * @param string $filename Name of stylesheet (without .css)
        * @param array $parameter Optional. Array of url parameters.
        */
       public function RefreshNode($target, $time, $parameter = array()){
           $this->__url = $target;
           $this->__time = $time;
           $this->__parameter = $parameter;
           $this->__checksum = md5($target . $time);
       }

       /**
        * Transforms the node into html.
        *
        * @return string The ready html-code.
        */
       public function transform(){
           import('tools::link','linkHandler');
           $link = linkHandler::generateLink($this->__url, $this->__parameter);
           return '<meta http-equiv="refresh" content="' . $this->__time . '; URL=' . $link . '">
   ';
       }


   }
?>