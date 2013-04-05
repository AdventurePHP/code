<?php
namespace APF\modules\usermanagement\biz\login;

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

/**
 * @package APF\modules\usermanagement\biz\login
 * @class UmgtRedirectUrlProvider
 *
 * This interface specifies the structure of any url generation provider that is
 * used to create log-in and log-out redirect urls.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011<br />
 */
interface UmgtRedirectUrlProvider {

   /**
    * @public
    *
    * Creates and returns the url that is used to redirect the user to
    * after both log-in and log-out actions.
    *
    * @return string The redirect url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2011<br />
    */
   public function getRedirectUrl();
}
