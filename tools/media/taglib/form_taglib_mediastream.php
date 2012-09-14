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
import('tools::media::taglib', 'ui_mediastream');

/**
 * @package tools::media::taglib
 * @class form_taglib_mediastream
 *
 * Implements the form:mediastream tag. See class ui_mediastream for more details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.11.2008<br />
 * Version 0.2, 10.08.2010 (Bug 384: added interface methods for form taglibs)<br />
 */
class form_taglib_mediastream extends ui_mediastream {

   public function isValid() {
      return true;
   }

   public function isSent() {
      return false;
   }

}
