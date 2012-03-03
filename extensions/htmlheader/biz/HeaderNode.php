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

/**
 * @package extensions::htmlheader::biz
 * @class HeaderNode
 *
 * This interface specifies a tag, that is included in the <em>&lt;head /&gt;</em>
 * tag of the HTML page. It is the basis for all further node definitions and
 * implementations.
 * <p/>
 * At present, the subsequent tags are represented by the listed node types (sub-interfaces):
 * <ul>
 * <li>BaseNode: BaseUrlNode</li>
 * <li>MetaNode: SimpleMetaNode, HttpMetaNode, RefreshNode</li>
 * <li>CssNode: StaticCsdsNode, CssContentNode, CssPackageNode, ConditionalDynamicCssNode, ConditionalStaticCssNode</li>
 * <li>JsNode: DynamicJsNode, JsContentNode, JsPackageNode, StaticJsNode</li>
 * <li>TitleNode: SimpleTitleNode</li>
 * </ul>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2010<br />
 * Version 0.2, 10.05.2011 (Introduced the global sorting feature)<br />
 */
interface HeaderNode {

   /**
    * This checksum allows to compare nodes, in order to find duplicates.
    * Must be filled by constructor.
    *
    * @return string Md5 checksum.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.08.2010<br />
    */
   public function getChecksum();

   /**
    * Defines the priority of the header node needed for sorting.
    *
    * @param int $priority The priority to apply to the header node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.05.2011<br />
    */
   public function setPriority($priority);

   /**
    * Returns the priority defined for the current header node.
    *
    * @return int The priority of the header node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.05.2011<br />
    */
   public function getPriority();

   /**
    * Defines if the tag should be placed in gethead- or getjsbody taglib.
    *
    * @param bool $value Set to true if it should be append to body.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.09.2011<br />
    */
   public function setAppendToBody($value);

   /**
    * Defines if the tag should be placed in gethead- or getjsbody taglib.
    *
    * @return Returns true if it should be append to body.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.09.2011<br />
    */
   public function getAppendToBody();
}

?>