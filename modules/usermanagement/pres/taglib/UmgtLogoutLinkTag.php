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
namespace APF\modules\usermanagement\pres\taglib;

use APF\core\pagecontroller\Document;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * @package APF\modules\usermanagement\pres\taglib
 * @class UmgtLogoutLinkTag
 *
 * Creates a logout link that points to the current page but defines the parameters
 * of the logout action for you (especially the application identifier).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.06.2011<br />
 */
class UmgtLogoutLinkTag extends Document {

   public function transform() {
      $params = array('logout' => 'true');
      return LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\modules\usermanagement\biz', 'logout', $params);
   }

}
