<?php
namespace APF\extensions\forwardmessage\pres\taglib;

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
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\TagLib;
use APF\core\service\APFService;
use APF\extensions\forwardmessage\biz\ForwardMessageManager;

/**
 * @package APF\extensions\forwardmessage\pres\taglib
 * @class AddForwardMessageTag
 *
 * Adds a status message for the next page.
 *
 * @author Daniel Seemaier, Werner Liemberger
 * @version
 * Version 0.1, 10.09.2010
 * Version 0.2 25.2.2011, Group option added by Werner Liemberger
 */
class AddForwardMessageTag extends Document {

   public function __construct() {
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'message', 'getstring');
   }

   public function onParseTime() {

      if (!$name = $this->getAttribute('name')) {
         throw new \InvalidArgumentException('[AddForwardMessageTag::onParseTime()] '
               . 'The attribute "name" is empty or not present. Thus message cannot be added!');
      }

      $group = $this->getAttribute('group');

      if ($group === null || $group == '') {
         $group = 'message';
      }

      $show = false;
      if ($this->getAttribute('show') == 'true') {
         $show = true;
      }

      // analyze the message sub tag and make them feel to be in a "normal" environment
      $this->extractTagLibTags();

      // add message that is the essence of the tag's transformed content

      /* @var $forwardMessageMgr ForwardMessageManager */
      $forwardMessageMgr = & $this->getServiceObject('APF\extensions\forwardmessage\biz\ForwardMessageManager', APFService::SERVICE_TYPE_SESSION_SINGLETON);
      $forwardMessageMgr->addMessage($name, parent::transform(), $show, $group);
   }

   public function transform() {
      return '';
   }

}