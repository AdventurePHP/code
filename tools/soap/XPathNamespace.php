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
 * @package tools::soap
 * @class APFSoapClient
 *
 * Implements a wrapper class for an xpath namespace that can be registered with the APFSoapClient in
 * order to parse the response correctly.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.01.2012<br />
 */
class XPathNamespace extends APFObject {

    /**
     * @var string The prefix (e.g. "S").
     */
    private $prefix;

    /**
     * @var string The namespace (e.g. "http://schemas.xmlsoap.org/soap/envelope/").
     */
    private $namespace;

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    public function getPrefix() {
        return $this->prefix;
    }

}
