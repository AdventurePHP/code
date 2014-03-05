<?php
namespace APF\extensions\htmlheader\biz;

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
use APF\extensions\htmlheader\biz\HeaderNode;

/**
 * @package APF\extensions\htmlheader\biz
 * @Class CssNode
 *
 * This interface specifies a <em>&lt;link /&gt;</em> or <em>&lt;style /&gt;</em> tag.
 *
 * @author Ralf Schubert, Christian Achatz
 * @version
 * Version 0.1, 20.09.2009 <br />
 * Version 0.2, 27.02.2010 (Added external file support)<br />
 * Version 0.3, 20.08.2010 (Class is now an interface)<br />
 */
interface CssNode extends HeaderNode {
}
