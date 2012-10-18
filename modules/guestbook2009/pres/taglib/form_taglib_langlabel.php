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
import('modules::guestbook2009::pres::taglib', 'LanguageLabelTag');

/**
 * @package modules::guestbook2009::pres
 * @class form_taglib_langlabel
 *
 * Displays language labels within forms.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.06.2009<br />
 */
class form_taglib_langlabel extends LanguageLabelTag {

   public function __construct() {
      parent::__construct();
   }

   public function isSent() {
      return false;
   }

   public function isValid() {
      return true;
   }

}
