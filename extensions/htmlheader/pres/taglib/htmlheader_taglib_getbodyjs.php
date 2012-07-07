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
import('extensions::htmlheader::pres::filter', 'HtmlHeaderOutputFilter');

/**
 * @package extensions::htmlheader::pres::taglib
 * @class htmlheader_taglib_getbodyjs
 *
 * Taglib for receiving javascript definitions which should be placed at
 * the end of the body.
 *
 * @example
 * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_getbodyjs" prefix="htmlheader" name="getbodyjs" />
 * <htmlheader:getbodyjs />
 *
 * @author Ralf Schubert <<a href="http://develovision.de/">Develovision</a>>
 * @version 0.1, 21.09.2011<br />
 */
class htmlheader_taglib_getbodyjs extends Document {
   const HTML_BODYJS_INDICATOR = '<!--HTMLHEADER_TAGLIB_GETBODYJS-->';

   public function transform() {

      $FilterChain = OutputFilterChain::getInstance();

      // register filter that replaces the token with real live data if filter isn't already registered
      // (uses the same filter as htmlheader:gethead-Taglib)
      if (!$FilterChain->isFilterRegistered('HtmlHeaderOutputFilter')) {
         $FilterChain->prependFilter(new HtmlHeaderOutputFilter());
      }

      // place marker that will be replaced by the
      return self::HTML_BODYJS_INDICATOR;
   }

}
