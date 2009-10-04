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
    *  @class htmlheader_taglib_gethead
    *
    *  Taglib for receiving the complete htmlheader.
    *
    *  @example
    *  <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="gethead" />
    *  <htmlheader:gethead />
    *
    *  @author Ralf Schubert
    *  @version 0.1, 20.09.2009<br>
    *  @version 0.2, 27.09.2009<br>
    */
   class htmlheader_taglib_gethead extends Document
   {

       protected $__stylesheets = array();
       protected $__javascripts = array();
       protected $__title = null;
       protected $__refresh = null;

       public function transform(){
           $iM = $this->__getServiceObject('extensions::htmlheader::biz','HtmlHeaderManager');
           $this->__stylesheets = $iM->getStylesheets();
           $this->__javascripts = $iM->getjavascripts();
           $this->__title = $iM->title;
           $this->__refresh = $iM->getRefresh();

           $output = '';
           if($this->__title !== null){
               $output .= '<title>' . $this->__title . '</title>
   ';
           }
           if($this->__refresh !== null){
               $output .= $this->__refresh->transform();
           }
           foreach($this->__stylesheets as $stylesheet){
               $output = $stylesheet->transform() . $output;
           }
           foreach($this->__javascripts as $script){
               $output = $script->transform() . $output;
           }
           return $output;
       }

   }
?>