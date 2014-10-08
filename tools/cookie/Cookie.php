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
namespace APF\tools\cookie;

use APF\core\http\Cookie as CoreCookie;

/**
 * The Cookie is a tool, that provides sophisticated cookie handling. The methods included allow you to
 * create, update and delete cookies using a clean API. Usage:
 * <pre>$c = new Cookie('my_cookie');
 * $c->setValue('my_value');
 * $c->delete();</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2008<br />
 * Version 0.2, 10.01.2009 (Finished implementation and testing)<br />
 */
class Cookie extends CoreCookie {

   protected $useResponse = false;

}
