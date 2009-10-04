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
    *  @namespace extensions::htmlheader::pres::taglib
    *  @class htmlheader_taglib_addjs
    *
    *  Taglib for adding javascripts to htmlheader.
    *
    *  @example
    *  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addjs" />
    *  <htmlheader:addjs namespace="{CONTEXT}::pres::frontend::static::js" filename="jsfile" />
    *  <ul>
    *    <li>namespace: Namespace of javascript file</li>
    *    <li>filename: Javascript filename without '.js'</li>
    *  </ul>
    *
    *  @author Ralf Schubert
    *  @version 0.1, 20.09.2009<br>
    *  @version 0.2, 27.09.2009<br>
    */
   class htmlheader_taglib_addjs extends Document
   {
       public function onParseTime() {
           $HHM = $this->__getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');
           $namespace = $this->getAttribute('namespace');
           $filename = $this->getAttribute('filename');
           if(empty($namespace)){
               trigger_error('[htmlheader_taglib_addjs::onParseTime()] The attribute "namespace" is empty or not present.');
           }
           if(empty($filename)){
               trigger_error('[htmlheader_taglib_addjs::onParseTime()] The attribute "filename" is empty or not present.');
           }
           import('extensions::htmlheader::biz','JsNode');
           $JsNode = new JsNode($namespace, $filename);
           $HHM->addJs($JsNode);
       }


       public function transform(){
           return '';
       }

   }
?>