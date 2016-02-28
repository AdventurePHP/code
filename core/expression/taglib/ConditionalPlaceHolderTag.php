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
namespace APF\core\expression\taglib;

use APF\core\expression\TemplateCondition;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\PlaceHolder;

/**
 * Implements a conditional place holder tag that only displays itself in case the
 * content matches the given condition.
 * <p/>
 * Example:
 * <code>
 * <cond:placeholder name="" [condition=""]>
 *    <h3 class="...">${content}</h3>
 * </cond:placeholder>
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.02.2016 (ID#283: added first version of tag implementation)<br />
 */
class ConditionalPlaceHolderTag extends Document implements PlaceHolder {

   const PLACE_HOLDER_NAME = 'content';

   /**
    * @var string Content of the place holder set by e.g. controllers.
    */
   protected $placeHolderContent;

   /**
    * @var bool Indicates whether onParseTime() has passed to store the "real" place holder content.
    */
   protected $parsingDone = false;

   public function onParseTime() {
      parent::onParseTime();
      $this->parsingDone = true;
   }

   public function transform() {

      // The condition defines whether or not the content is displayed. Can be overwritten
      // by the "condition" attribute according to the TemplateCondition capabilities.
      $condition = $this->getAttribute('condition', 'notEmpty()');

      if (!TemplateCondition::applies($condition, $this->placeHolderContent)) {
         return '';
      }

      // inject place holder content into data attribute to make it accessible within the tag for displaying
      if (is_string($this->placeHolderContent)) {
         // Automatically replace simple place holders.
         // Allow default content output by omitting place holder in case not necessary.
         $this->setPlaceHolder(self::PLACE_HOLDER_NAME, $this->placeHolderContent);
      } elseif (is_array($this->placeHolderContent) || is_object($this->placeHolderContent)) {
         // inject content into a data attribute for further access
         $this->setData(self::PLACE_HOLDER_NAME, $this->placeHolderContent);
      }

      return $this->transformChildrenAndPreserveContent();

   }

   public function setContent($content) {

      if ($this->parsingDone) {
         // re-direct place holder value separate field to allow injection into data attribute.
         $this->placeHolderContent = $content;
      } else {
         // set "normal" content to real DOM node content to allow markup generation.
         parent::setContent($content);
      }

      return $this;
   }

}
