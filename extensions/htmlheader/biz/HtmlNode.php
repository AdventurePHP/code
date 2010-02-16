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

   /**
    * @namespace extensions::htmlheader::biz
    * @class HtmlNode
    *
    * General Node for HtmlHeaderManagers data.
    *
    * @author Ralf Schubert
    * @version 0.1, 25.09.2009<br>
    */
   class HtmlNode extends coreObject{

       /**
        * This checksum allows to compare nodes, in order to find duplicates.
        * Must be filled by constructor.
        *
        * @var string Md5 checksum.
        */
       protected $__checksum = null;

       /**
        * Transforms the node into html.
        *
        * @return string The ready html-code.
        */
       public function transform(){
           return '';
       }


       /**
        * Builds a Link for the JsCssInclusion FC-action
        *
        * @param string $namespace Namespace of file
        * @param string $filename Name of file
        * @param string $type Filetype
        * @return string FC-action link.
        */
       protected function __buildFCLink($namespace, $filename, $type){
           import('tools::link','FrontcontrollerLinkHandler');
           $path = '';
           $reg = &Singleton::getInstance('Registry');
           $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
           $namespace = str_replace('::','_',$namespace);

           $actionParam = array();

           if($urlRewriting === true) {
               $path = $reg->retrieve('apf::core','URLBasePath');
               $actionParam = array(
                   'extensions_jscssinclusion_biz-action/sGCJ' => 'path/'.$namespace.'/type/'.$type.'/file/'.$filename
               );
           // end if
           }
           else {
               $path = 'http://' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'],'', $_SERVER['SCRIPT_FILENAME']);
               $actionParam = array(
                   'extensions_jscssinclusion_biz-action:sGCJ' => 'path:'.$namespace.'|type:'.$type.'|file:'.$filename
               );
           // end else
           }

           // return url
           return FrontcontrollerLinkHandler::generateLink($path,$actionParam);

       }

       /**
        * Returns the objects checksum, which is needed to check wether this node is
        * already existing or not.
        * @return string
        */
       public function getChecksum(){
           return $this->__checksum;
       }
   }
?>