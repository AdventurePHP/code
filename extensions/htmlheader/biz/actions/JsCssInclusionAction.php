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
namespace APF\extensions\htmlheader\biz\actions;

use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\http\HeaderImpl;
use APF\core\http\ResponseImpl;
use APF\extensions\htmlheader\biz\JsCssPackager;
use InvalidArgumentException;

/**
 * Implements an FC-action which returns .css and .js files
 *
 * @example
 * Javascript:
 * /APF/sites/test/index.php?extensions_htmlheader-action:JsCss=path:sites_test_pres_frontend_static_js_example|type:js|file:examplejavascript
 * Css:
 * /APF/sites/test/index.php?extensions_htmlheader-action:JsCss=path:sites_test_pres_frontend_static_css_example|type:css|file:examplecss
 *
 * @author Ralf Schubert
 * @version 1.0, 20.09.2009<br />
 * @version 1.0.1, 21.09.2009 Set the TimeToLive variable as class member<br />
 * @version 1.1, 27.09.2009 Renamed Tool<br />
 * @version 1.2, 13.09.2011 Merged JsCssPackager, JsCssInclusion to HtmlHeader<br />
 */
final class JsCssInclusionAction extends AbstractFrontcontrollerAction {

   /**
    * TimeToLive for cache headers in seconds
    *
    * 60 = 1 minute
    * 60 * 60 (3600) = 1 hour
    * 60 * 60 * 24 (86400) = 1 day
    * 60 * 60 * 24 * 7 (604800) = 7 days
    *
    * @var int $ttl
    */
   protected $ttl = 604800;

   public function run() {
      if ($this->getRequestedType() === 'package') {
         $this->sendPackage();
      } else {
         $this->sendFile();
      }
   }

   protected function getRequestedType() {
      $PackageName = $this->getInput()->getParameter('package');
      if (!empty($PackageName)) {
         return 'package';
      }

      return 'file';
   }

   protected function gzipIsSupported() {
      return self::getRequest()->isGzipSupported();
   }

   /**
    * @param string $type
    *
    * @return string The desired MIME type.
    */
   protected function getMimeType($type) {
      // check if correct type is given. If not exit for security reasons.
      switch ($type) {
         case 'css':
            return 'text/css';
         case 'js':
            return 'text/javascript';
         default:
            throw new InvalidArgumentException('[JsCssInclusionAction::getMimeType()] The attribute '
                  . '"type" must be either "css" or "js".');
      }
   }

   protected function getSanitizedNamespace($namespace) {
      $namespace = str_replace('_', '\\', // resolve url notation for namespaces
            preg_replace('/[^A-Za-z0-9\-_\.]/', '', $namespace)
      );

      // Changing to higher directories is not allowed, either!
      while (preg_match('/\.\./', $namespace) > 0) {
         $namespace = preg_replace('/\.\./', '', $namespace);
      }

      return $namespace;
   }

   private function getSanitizedFileBody($fileBody) {
      return preg_replace('/[^A-Za-z0-9\-_\.]/', '', $fileBody);
   }

   protected function sendPackage() {
      $package = $this->getInput()->getParameter('package');

      $packageExpl = explode('.', $package);
      if (count($packageExpl) !== 2) {
         throw new InvalidArgumentException('[JsCssInclusionAction::sendPackage()] The attribute
                 "package" has to be like "packagename.type", with type
                 being "js" or "css".');
      }

      $packName = $packageExpl[0];
      $packType = $packageExpl[1];

      $mimeType = $this->getMimeType($packType);

      /* @var $packager JsCssPackager */
      $packager = $this->getServiceObject('APF\extensions\htmlheader\biz\JsCssPackager');
      $output = $packager->getPackage($packName, $this->gzipIsSupported());

      $response = &self::getResponse();

      // Get ClientCachePeriod (in days), and convert to seconds
      $clientCachePeriod = $packager->getClientCachePeriod($packName) * 86400;
      $this->addHeaders($response, $clientCachePeriod, $mimeType);

      $response->setBody($output);

      $response->send();
   }

   protected function addHeaders(ResponseImpl &$response, $clientCachePeriod, $mimeType) {

      // send gzip header if supported
      if ($this->gzipIsSupported()) {
         $response->setHeader(new HeaderImpl('Content-Encoding', 'gzip'));
      }

      // send headers for caching
      $response->setHeader(new HeaderImpl('Cache-Control', 'public; max-age=' . $clientCachePeriod));

      $modifiedDate = date('D, d M Y H:i:s \G\M\T', time());
      $response->setHeader(new HeaderImpl('Last-Modified', $modifiedDate));

      $expiresDate = date('D, d M Y H:i:s \G\M\T', time() + $clientCachePeriod);
      $response->setHeader(new HeaderImpl('Expires', $expiresDate));

      $response->setHeader(new HeaderImpl('Content-Type', $mimeType));
   }

   protected function sendFile() {
      $namespace = $this->getSanitizedNamespace($this->getInput()->getParameter('path'));
      $file = $this->getSanitizedFileBody($this->getInput()->getParameter('file'));
      $type = $this->getInput()->getParameter('type');

      // Check if all required attributes are given
      if (empty($namespace)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::sendFile()] The attribute "path" '
               . 'is empty or not present.');
      }
      if (empty($file)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::sendFile()] The attribute "file" '
               . 'is empty or not present.');
      }
      if (empty($type)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::SendFile()] The attribute "type" '
               . 'is empty or not present.');
      }

      // get MIME type and verify correct extension
      $mimeType = $this->getMimeType($type);

      $response = &self::getResponse();

      $this->addHeaders($response, $this->ttl, $mimeType);

      /* @var $packager JsCssPackager */
      $packager = $this->getServiceObject('APF\extensions\htmlheader\biz\JsCssPackager');
      $response->setBody($packager->getFile($namespace, $file, $type, $this->gzipIsSupported()));

      $response->send();
   }
}
