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
namespace APF\tools\link\taglib;

use APF\core\pagecontroller\LanguageLabelTag;

/**
 * Re-implements the language label tag for the link tags.
 *
 * @author Werner Liemberger wpublicmail [at] gmail DOT com
 * @version
 * Version 0.1, 25.11.2012<br />
 */
class LinkLanguageLabelActiveTag extends LanguageLabelTag {

   public function onAfterAppend() {
      // if link is active, Taglib will be transformed to set the data from the defined config file
      /* @var $parent HtmlLinkTag */
      $parent = $this->getParentObject();
      if ($parent->isActive()) {
         $parent->setContent(parent::transform());

         return;
      }

      // removes remaining if link is not active
      $count = substr_count($parent->getContent(), '<' . $this->getObjectId() . ' />' . PHP_EOL);
      if ($count > 0) {
         $parent->setContent(str_replace('<' . $this->getObjectId() . ' />' . PHP_EOL, '', $parent->getContent()));
      } else {
         $parent->setContent(str_replace('<' . $this->getObjectId() . ' />', '', $parent->getContent()));
      }
   }

   public function transform() {
      return '';
   }

}