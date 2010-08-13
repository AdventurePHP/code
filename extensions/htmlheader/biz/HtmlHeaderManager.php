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
 *  @namespace extensions::htmlheader::biz
 *  @class HtmlHeaderManager
 *
 *  Container for htmlheader objects.
 *
 *  @author Ralf Schubert <ralf.schubert@the-screeze.de>
 *  @version 0.1, 20.09.2009<br />
 *  @version 0.2, 27.09.2009<br />
 *  @version 0.3, 20.03.2010 Added package support<br />
 */
class HtmlHeaderManager extends APFObject {
    /**
     * Contains title of page.
     * @var string title
     */
    public $title = null;

    /**
     * Contains stylesheet nodes.
     * @var array __stylesheets
     */
    protected $__stylesheets = array();

    /**
     * Contains javascript nodes.
     * @var array __javascripts
     */
    protected $__javascripts = array();

    /**
     * Contains package nodes.
     * @var array __packages
     */
    protected $__packages = array();

    /**
     * Contains meta refresh node.
     * @var array
     */
    protected $__refresh = null;

    /**
     * This function adds a stylesheet-node at the end of the list.
     * @param object Stylesheet node
     */
    public function addCss(CssNode $CssNode) {
        if($this->__findDuplicate($CssNode, $this->__stylesheets) === false) {
            $this->__stylesheets[] = $CssNode;
        }
    }

    /**
     * This function adds a javascript-node at the end of the list
     * @param object Javascript node
     */
    public function addJs(JsNode $JsNode) {
        if($this->__findDuplicate($JsNode, $this->__javascripts) === false) {
            $this->__javascripts[] = $JsNode;
        }
    }

    /**
     * Adds a meta refresh node to the head.
     * @param string $targetURL Target Url.
     * @param int $time Seconds of refresh delay.
     * @param array $parameter Optional. Array of url parameters.
     */
    public function addRefresh($targetURL, $time, $parameter = array()) {
        import('extensions::htmlheader::biz','RefreshNode');
        $this->__refresh = new RefreshNode($targetURL, $time, $parameter);
    }

    /**
     * Adds a JsCssPackager-Node to the head.
     * @param PackageNode $PackageNode The package node.
     */
    public function addPackage(PackageNode $PackageNode) {
        if($this->__findDuplicate($PackageNode, $this->__packages) === false) {
            $this->__packages[] = $PackageNode;
        }
    }

    /**
     * Compares already saved nodes with new node, in order to find duplicates.
     * Returns true if duplicate was found.
     *
     * @param object $node New node
     * @param array $objects Array with saved nodes
     * @return bool Returns true if duplicate was found.
     */
    protected function __findDuplicate($node, $objects) {
        $checksum = $node->getChecksum();

        foreach($objects as $object) {
            if($object->getChecksum() === $checksum) {
                return true;
            }

        }
        return false;
    }

    public function getStylesheets() {
        return $this->__stylesheets;
    }
    public function getJavascripts() {
        return $this->__javascripts;
    }
    public function getRefresh() {
        return $this->__refresh;
    }
    public function getPackages(){
        return $this->__packages;
    }
}
?>