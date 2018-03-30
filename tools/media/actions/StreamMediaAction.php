<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\media\actions;

session_cache_limiter('none');

use APF\core\configuration\ConfigurationException;
use APF\core\frontcontroller\AbstractFrontControllerAction;
use APF\core\http\HeaderImpl;
use APF\core\loader\RootClassLoader;
use Exception;

/**
 * Implementation of the streamMedia action, that streams various media files (css, image, ...)
 * to the client. This action is the "backend" for the <*:mediastream /> tags.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.11.2008<br />
 */
class StreamMediaAction extends AbstractFrontControllerAction {

   public function run() {

      // Bug 782: read params and sanitize them to avoid security issues
      $namespace = $this->getSanitizedNamespace();
      $fileBody = $this->getSanitizedFileBody();
      $extension = $this->getSanitizedExtension();
      $fileName = $fileBody . '.' . $extension;

      // Bug 782: check for allowed extension to avoid access to configuration files.
      $allowedExtensions = $this->getAllowedExtensions();
      if ($this->isAllowedExtension($allowedExtensions, $extension)) {

         // ID#107: get specific vendor and map to root path instead of APF-only
         $vendor = RootClassLoader::getVendor($namespace);
         $rootPath = RootClassLoader::getLoaderByVendor($vendor)->getRootPath();

         // Re-map namespace since as of 2.0 it contains the vendor that
         // refers to the root path. Keeping the vendor would cause the
         // sub-path to map to the wrong folder.
         $namespace = str_replace($vendor . '\\', '', $namespace);

         $filePath = $rootPath . '/' . str_replace('\\', '/', $namespace) . '/' . $fileName;
         if (file_exists($filePath)) {

            // map extension to known mime type
            $contentType = $this->getMimeType($allowedExtensions, $extension);

            // send desired header
            $response = $this->getResponse();
            $response->setHeader(new HeaderImpl('Content-Type', $contentType));

            // send headers to allow caching
            $delta = 7 * 24 * 60 * 60; // caching for 7 days
            $response->setHeader(new HeaderImpl('Cache-Control', 'public; max-age=' . $delta));

            $modifiedDate = date('D, d M Y H:i:s \G\M\T', time());
            $response->setHeader(new HeaderImpl('Last-Modified', '' . $modifiedDate));

            $expiresDate = date('D, d M Y H:i:s \G\M\T', time() + $delta);
            $response->setHeader(new HeaderImpl('Expires', '' . $expiresDate));

            $response->send(false);

            @readfile($filePath);

            exit();

         } else {
            throw new Exception('File with name "' . $fileName . '" cannot be found under sub-path "' . $namespace . '"!');
         }
      }

      throw new Exception('You are not allowed to request "' . $fileName . '" under sub-path "' . $namespace . '"!');
   }

   /**
    * @param array $extensions The list of allowed extensions.
    * @param string $extension The extension to check.
    *
    * @return bool True in case the given extension is allowed, false otherwise.
    */
   private function isAllowedExtension(array $extensions, $extension) {
      $extension = strtolower($extension);

      return isset($extensions[$extension]);
   }

   /**
    * @param array $extensions The list of allowed extensions.
    * @param string $extension The extension to check
    *
    * @return string Desired mime type,
    */
   private function getMimeType(array $extensions, $extension) {
      $extension = strtolower($extension);

      return $extensions[$extension];
   }

   /**
    * Removes un-allowed parts from the namespace (e.g. config namespace).
    *
    * @return string The namespace of the resource to load.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.07.2011<br />
    */
   private function getSanitizedNamespace() {
      $namespace = str_replace('_', '\\', // resolve url notation for namespaces
            preg_replace('/[^A-Za-z0-9\-_\.]/', '',
                  $this->getParameters()->getParameter('namespace'))

      );

      // Do not allow configuration files to be streamed.
      // Thus replace all occurrences recursively!
      // Further, changing to higher directories is not allowed, either!
      while (preg_match('/config\/|\.\./i', $namespace) > 0) {
         $namespace = preg_replace('/config\/|\.\./i', '', $namespace);
      }

      return $namespace;
   }

   /**
    * Cleans up the file body.
    *
    * @return string The file body of the resource to load.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.07.2011<br />
    */
   private function getSanitizedFileBody() {
      return preg_replace('/[^A-Za-z0-9\-_]/', '', $this->getParameters()->getParameter('filebody'));
   }

   /**
    * Cleans up the file extension parameter.
    *
    * @return string The extension of the resource to load.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.07.2011<br />
    */
   private function getSanitizedExtension() {
      return preg_replace('/[^A-Za-z0-9]/', '', $this->getParameters()->getParameter('extension'));
   }

   /**
    * Returns the list of allowed extensions along with their MIME types.
    * Falls back to internal values in case the optional configuration
    * file is not present.
    *
    * @return array The list of allowed extensions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.07.2011<br />
    */
   private function getAllowedExtensions() {
      try {
         return $this->getExtensions();
      } catch (ConfigurationException $e) {
         return [
               'png'  => 'image/png',
               'jpeg' => 'image/jpg',
               'jpg'  => 'image/jpg',
               'gif'  => 'image/gif',
               'css'  => 'text/css',
               'js'   => 'text/javascript'
         ];
      }
   }

   /**
    * Loads the configuration file that defines the allowed extensions.
    * <p/>
    * In order to define a custom set of allowed file extensions along with their MIME type,
    * please create a configuration file with name <em>{ENVIRONMENT}_allowed_extensions.ini</em>
    * under <em>/config/tools/media/{CONTEXT}</em>.
    * <p/>
    * The content of the configuration file is as follows:
    * <code>
    * [Default]
    * jpg = "image/jpg"
    * foo = "text/foo"
    * bar = "text/bar"
    * </code>
    * This method converts the above content to this:
    * <code>
    * ['jpg' => 'image/jpg', 'foo' => 'text/foo', 'bar' => 'text/bar']
    * </code>
    *
    * @throws ConfigurationException In case of missing configuration.
    * @return array A list of allowed extensions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.07.2011<br />
    */
   private function getExtensions() {

      $config = $this->getConfiguration('APF\tools\media', 'allowed_extensions.ini');
      if (!$config->hasSection('Default')) {
         throw new ConfigurationException('Section "Default" is missing!');
      }

      $section = $config->getSection('Default');

      $extensions = [];
      foreach ($section->getValueNames() as $name) {
         // ID#108: map extensions to lower case to allow case insensitive checks
         $extensions[strtolower($name)] = $section->getValue($name);
      }

      return $extensions;
   }

}
