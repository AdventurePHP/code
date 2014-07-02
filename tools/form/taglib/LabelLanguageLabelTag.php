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
namespace APF\tools\form\taglib;

use APF\core\pagecontroller\LanguageLabelTag;

/**
 * @package APF\tools\form\taglib
 * @class LabelLanguageLabelTag
 *
 * Implements a taglib that let's you easily label your form label tag. This makes
 * usage of the document controller obsolete.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.01.2013<br />
 */
class LabelLanguageLabelTag extends LanguageLabelTag {

   public function onAfterAppend() {
      $this->getParentObject()->setContent(parent::transform());
   }

   public function transform() {
      return '';
   }

}