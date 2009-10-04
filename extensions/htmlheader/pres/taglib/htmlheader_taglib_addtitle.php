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
    *  @class htmlheader_taglib_addtitle
    *
    *  Taglib for adding a title to htmlheader.
    *
    *  @example
    *  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="addtitle" />
    *  <htmlheader:addtitle append="false">Testwebpage title</htmlheader:addtitle>
    *  Set append to true, if you want to add the given tag-content at the end of
    *  the existing title instead of overwriting it.
    *
    *  @author Ralf Schubert
    *  @version 0.1, 20.09.2009<br>
    *  @version 0.2, 27.09.2009<br>
    */
   class htmlheader_taglib_addtitle extends Document
   {
       public function onParseTime() {
           $HHM = $this->__getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');
           $append = false;
           if($this->getAttribute('append') !== false){
               $append = $this->getAttribute('append');
           }
           if(!empty($this->__Content)){
               if($append == 'true'){
                   $HHM->title .= $this->__Content;
               }
               else{
                   $HHM->title = $this->__Content;
               }
           }
       }
       public function transform(){
           return '';
       }
   }
?>