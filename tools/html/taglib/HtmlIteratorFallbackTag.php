<?php
namespace APF\tools\html\taglib;

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
use APF\core\pagecontroller\TagLib;
use APF\core\pagecontroller\TemplateTag;

/**
 * @package APF\tools\html\taglib
 * @class HtmlIteratorFallbackTag
 *
 * Encapsulates a TemplateTag for usage within an iterator tag. Re-declaration
 * due to re-naming of tag names.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.05.2014 (ID#105: added fallback content support)<br />
 */
class HtmlIteratorFallbackTag extends TemplateTag {

   public function __construct() {
      self::addTagLib(new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'fallback', 'placeholder'));
      self::addTagLib(new TagLib('APF\core\pagecontroller\AddTaglibTag', 'fallback', 'addtaglib'));
      self::addTagLib(new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'fallback', 'getstring'));
   }

}
