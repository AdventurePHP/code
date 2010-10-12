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

import('tools::http','HeaderManager');

/**
 *  @namespace extensions::jscsspackager::biz::actions
 *  @class JsCssPackagerAction
 *
 *  Implements an FC-action which returns multiple .css and .js files in one package
 *
 *  @example
 *  Javascript:
 *  /APF/sites/test/index.php?extensions_jscsspackager_biz-action:jcp=package:form_clientvalidators_all.js
 *  Css:
 *  /APF/sites/test/index.php?extensions_jscsspackager_biz-action:jcp=package:form_clientvaltest.css
 *
 *  @author Ralf Schubert <ralf.schubert@the-screeze.de>
 *  @version 1.0, 18.03.2010<br>
 */
final class JsCssPackagerAction extends AbstractFrontcontrollerAction {

    /**
     * @var Configuration Contains the configuration for packages
     */
    protected $__Cfg = null;

    public function JsCssPackagerAction() {
    }

    public function run() {
        $package = $this->getInput()->getAttribute('package');

        // Check if all required attributes are given
        if(empty($package)) {
            throw new InvalidArgumentException('[JSPackagerAction::run()] The attribute
                "package" is empty or not present.');
            exit();
        }

        $packageExpl = explode('.', $package);
        if(count($packageExpl) !== 2) {
            throw new InvalidArgumentException('[JSPackagerAction::run()] The attribute
                 "package" has to be like "packagename.type", with type
                 beeing "js" or "css".');
            exit();
        }

        $packName = $packageExpl[0];
        $packType = $packageExpl[1];
        // check if correct type is given. If not exit() for security reasons.
        switch($packType) {
            case 'css':
                $mimeType = 'text/css';
                break;
            case 'js':
                $mimeType = 'text/javascript';
                break;
            default:
                throw new InvalidArgumentException('[JSPackagerAction::run()] The attribute
                 "package" has to be like "packagename.type", with type
                 beeing "js" or "css".');
                exit();
        }
        
        // Check if gzip is supported
        $acceptGzip = false;
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
            $acceptGzip = true;
            // send gzip header
            HeaderManager::send("Content-Encoding: gzip");
        }
        
        /* @var $packager JsCssPackager */
        $packager = $this->__getAndInitServiceObject('extensions::jscsspackager::biz', 'JsCssPackager', null);
        $output = $packager->getPackage($packName, $acceptGzip);
        // Get ClientCachePeriod (in days), and convert to seconds
        $clientCachePeriod = $packager->getClientCachePeriod($packName)*86400;

        // send headers to allow caching
        HeaderManager::send('Cache-Control: public; max-age='.$clientCachePeriod, true);
        $modifiedDate = date('D, d M Y H:i:s \G\M\T', time());
        HeaderManager::send('Last-Modified: '.$modifiedDate, true);
        $expiresDate = date('D, d M Y H:i:s \G\M\T', time() + $clientCachePeriod);
        HeaderManager::send('Expires: '.$expiresDate, true);
        HeaderManager::send('Content-type: '.$mimeType);
        
        echo $output;
        exit();
    }
}
?>