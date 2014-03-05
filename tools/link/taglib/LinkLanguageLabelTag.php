<?php
namespace APF\tools\link\taglib;

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
use APF\core\pagecontroller\LanguageLabelTag;

/**
 * @package APF\tools\link\taglib
 * @class LinkLanguageLabelTag
 *
 * Re-implements the language label tag for the link tags.
 *
 * @author Werner Liemberger wpublicmail [at] gmail DOT com
 * @version
 * Version 0.1, 25.11.2012<br />
 */
class LinkLanguageLabelTag extends LanguageLabelTag {

   public function onAfterAppend() {
      /* @var $parent HtmlLinkTag */
      $parent = $this->getParentObject();
      foreach ($parent->getChildren() as $child) {
         if ($child instanceof LinkLanguageLabelActiveTag && $parent->isActive()) {
            return;
         }
      }
      $parent->setContent(parent::transform());
   }

   public function transform() {
      return '';
   }

}