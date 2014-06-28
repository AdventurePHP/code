<?php
namespace APF\modules\guestbook2009\pres\taglib;

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
 * Implements the wrapper taglib for displaying the lang dependent labels, that can be
 * configured in configuration files.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
class GuestbookLanguageLabelTag extends LanguageLabelTag {

   /**
    * Presets the attributes needed by the GuestbookLanguageLabelTag class to be able to only
    * have to provide the label key in the templates (saves your fingers!).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function __construct() {
      $this->setAttribute('namespace', 'APF\modules\guestbook2009\pres');
      $this->setAttribute('config', 'language.ini');
   }

}
