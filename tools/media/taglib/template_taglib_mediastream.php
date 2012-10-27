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
 * @class template_taglib_mediastream
 *
 *  Implements the template:mediastream tag. See class ui_mediastream for more details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.11.2008<br />
 * Version 0.2, 10.11.2008 (Bugfix: tag was not transformed within a template)<br />
 * Version 0.3, 10.11.2008 (Removed the onParseTime() method, because the registerTagLibModule() function now is obsolete)<br />
 */
class template_taglib_mediastream extends ui_mediastream {
}
