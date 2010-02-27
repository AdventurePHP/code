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

    import('tools::link','FrontcontrollerLinkHandler');
    
   /**
    * @namespace extensions::htmlheader::biz
    * @class HtmlNode
    *
    * General Node for HtmlHeaderManagers data.
    *
    * @author Ralf Schubert
    * @version
    * 0.1, 25.09.2009 <br />
    * 0.2, 27.02.2010 (Added external file support) <br />
    */
   class HtmlNode extends APFObject{

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
        * @param string $url Optional url.
        * @param string $namespace Namespace of file
        * @param string $filename Name of file
        * @param bool $urlRewriting Optional. Create rewriting Url.
        * @param bool $fcaction Optional. Create link for FC-Action.
        * @param string $type Filetype
        * @return string elements' link.
        */
       protected function __buildFCLink($url, $namespace, $filename, $urlRewriting, $fcaction, $type){
           $reg = &Singleton::getInstance('Registry');

           if($urlRewriting === null){
               $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
           }
           if($fcaction === null){
               $fcaction = true;
           }

           // Generate url if not given
           if($url === null){
               if($urlRewriting) {
                    $url = $reg->retrieve('apf::core','URLBasePath');
               }
               else {
                    $tmpPath = str_replace($_SERVER['DOCUMENT_ROOT'],'', $_SERVER['SCRIPT_FILENAME']);
                    $slash =  (substr($tmpPath, 0,1) !== '/') ? '/' : '';
                    $url = 'http://' . $_SERVER['HTTP_HOST'] . $slash . $tmpPath;
               }
               
           // end if
           }

           if($fcaction){
               $namespace = str_replace('::','_',$namespace);

               if($urlRewriting) {
                   $actionParam = array(
                       'extensions_jscssinclusion_biz-action/sGCJ' => 'path/'.$namespace.'/type/'.$type.'/file/'.$filename
                   );
               // end if
               }
               else {

                   $actionParam = array(
                       'extensions_jscssinclusion_biz-action:sGCJ' => 'path:'.$namespace.'|type:'.$type.'|file:'.$filename
                   );
               // end else
               }

               // return url
               return FrontcontrollerLinkHandler::generateLink($url,$actionParam);
           // end if
           }
           else {
                $namespace = str_replace('::','/',$namespace);
                $url .=  (substr($url, -1,1) !== '/') ? '/' : '';
                
                //return url
                return $url . $namespace . '/' . $filename .'.'. $type;
           // end else
           }
           

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