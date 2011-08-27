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
import('tools::http', 'HeaderManager');
import('extensions::jscssinclusion::filter', 'JsCssInclusionFilterChain');

/**
 * @namespace extensions::jscssinclustion::biz::actions
 * @class JsCssInclusionAction
 *
 * Implements an FC-action which returns .css and .js files
 *
 * @example
 * Javascript:
 * /APF/sites/test/index.php?extensions_jscssinclusion_biz-action:sGCJ=path:sites_test_pres_frontend_static_js_example|type:js|file:examplejavascript
 * Css:
 * /APF/sites/test/index.php?extensions_jscssinclusion_biz-action:sGCJ=path:sites_test_pres_frontend_static_css_example|type:css|file:examplecss
 *
 * @author Ralf Schubert
 * @version 1.0, 20.09.2009<br />
 * @version 1.0.1, 21.09.2009 Set the TimeToLive variable as class member<br />
 * @version 1.1, 27.09.2009 Renamed Tool<br />
 */
final class JsCssInclusionAction extends AbstractFrontcontrollerAction {

   /**
    * 60 = 1 minute
    * 60 * 60 (3600) = 1 hour
    * 60 * 60 * 24 (86400) = 1 day
    * 60 * 60 * 24 * 7 (604800) = 7 days
    *
    * @var int TimeToLive for cache headers in seconds
    */
   protected $ttl = 604800;

   public function run() {
      $namespace = $this->getInput()->getAttribute('path');
      $path = str_replace('_', '/', strip_tags($namespace));
      $file = strip_tags($this->getInput()->getAttribute('file'));
      $type = strip_tags($this->getInput()->getAttribute('type'));

      // Check if all required attributes are given
      if (empty($path)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::run()] The attribute "path" '
                                            . 'is empty or not present.');
      }
      if (empty($file)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::run()] The attribute "file" '
                                            . 'is empty or not present.');
      }
      if (empty($type)) {
         throw new InvalidArgumentException('[JsCssInclusionAction::run()] The attribute "type" '
                                            . 'is empty or not present.');
      }

      // send headers to allow caching
      HeaderManager::send('Cache-Control: public; max-age=' . $this->ttl, true);
      $modifiedDate = date('D, d M Y H:i:s \G\M\T', time());
      HeaderManager::send('Last-Modified: ' . $modifiedDate, true);
      $expiresDate = date('D, d M Y H:i:s \G\M\T', time() + $this->ttl);
      HeaderManager::send('Expires: ' . $expiresDate, true);

      // check if correct type is given. If not exit() for security reasons.
      switch ($type) {
         case 'css':
            $ext = 'css';
            $mimeType = 'text/css';
            break;
         case 'js':
            $ext = 'js';
            $mimeType = 'text/javascript';
            break;
         default:
            throw new InvalidArgumentException('[JsCssInclusionAction::run()] The attribute '
                                               . '"type" must be either "css" or "js".');
      }

      // build full path and return file.
      $libBasePath = Registry::retrieve('apf::core', 'LibPath');
      $filePath = $libBasePath . '/' . $path . '/' . $file . '.' . $ext;

      if (file_exists($filePath)) {
         $this->initFilterChain($ext);
         HeaderManager::send('Content-type: ' . $mimeType);
         echo JsCssInclusionFilterChain::getInstance()->filter(file_get_contents($filePath));
      } else {
         throw new IncludeException('[JsCssInclusionAction::run()] The requested file "' . $file . '.'
                                    . $ext . '" cannot be found in namespace "' . str_replace('_', '::', $namespace) . '". Please '
                                    . 'check your taglib definition for tag &lt;htmlheader:add* /&gt;!',
            E_USER_ERROR);
      }
      exit();
   }

   /**
    * @param $fileType The current file extension (js or css).
    */
   protected function initFilterChain($fileType) {

      try {
         $config = $this->getConfiguration('extensions::jscssinclusion::biz', 'JsCssInclusion.ini');
      } catch (ConfigurationException $e) {
         return;
      }

      $sectionName = ($fileType === 'css') ? 'CssFilter' : 'JsFilter';
      $section = $config->getSection($sectionName);

      if ($section !== null) {
         foreach ($section->getSectionNames() as $key) {
            $FilterInformation = $section->getSection($key);

            $namespace = $FilterInformation->getValue('Namespace');
            $name = $FilterInformation->getValue('Name');

            import($namespace, $name);
            JsCssInclusionFilterChain::getInstance()->appendFilter(new $name());
         }
      }
   }

}

?>