<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\Document;
use APF\extensions\apfelsms\biz\sites\SMSSite;
use APF\tools\string\StringAssistant;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (10.08.12)
 *
 */
class SMSSiteTitleTag extends Document {


   public function transform() {


      /** @var $SMSS SMSSite */
      $SMSS = $this->getDIServiceObject('APF\extensions\apfelsms', 'Site');

      return StringAssistant::escapeSpecialCharacters($SMSS->getWebsiteTitle());

   }
}
