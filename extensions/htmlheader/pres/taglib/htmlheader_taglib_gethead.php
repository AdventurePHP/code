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
    * @package extensions::htmlheader::pres::taglib
    * @class htmlheader_taglib_gethead
    *
    * Taglib for receiving the complete htmlheader.
    *
    * @example
    * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" prefix="htmlheader" class="gethead" />
    * <htmlheader:gethead />
    *
    * @author Ralf Schubert
    * @version 0.1, 20.09.2009<br />
    * @version 0.2, 27.09.2009<br />
    * @version 0.3, 17.08.2010 (Added meta nodes)<br />
    */
   class htmlheader_taglib_gethead extends Document {

      /**
       * @var string[] Defines the node types, that should be included before the textual nodes.
       */
      private static $jsFileNodes = array('StaticJsNode','DynamicJsNode');

      public function transform() {

         $iM = &$this->__getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');
         /* @var $iM HtmlHeaderManager */

         $output = '';

         $title = $iM->getTitle();
         if ($title !== null) {
            $output .= $title->transform().PHP_EOL;
         }

         $baseNodes = $iM->getBaseNodes();
         foreach ($baseNodes as $base) {
            $output .= $base->transform().PHP_EOL;
         }

         $metaNodes = $iM->getMetaNodes();
         foreach ($metaNodes as $metaNode) {
            $output .= $metaNode->transform().PHP_EOL;
         }

         $stylesheets = $iM->getStylesheetNodes();
         foreach ($stylesheets as $stylesheet) {
            $output .= $stylesheet->transform().PHP_EOL;
         }

         // sort js files according to their dynamic or static character to not
         // generate js errors by accessing functionality that has not been included
         // this is done, by queing the static ones for later transformation but with
         // respect to the order the scrips were added!
         $javascripts = $iM->getJavascriptNodes();
         $queue = array();
         foreach ($javascripts as $script) {
            if(in_array(get_class($script),self::$jsFileNodes)){
               $output .= $script->transform().PHP_EOL;
            } else {
               $queue[] = $script;
            }
         }
         foreach ($queue as $script) {
            $output .= $script->transform().PHP_EOL;
         }

         return $output;
      }

   }
?>