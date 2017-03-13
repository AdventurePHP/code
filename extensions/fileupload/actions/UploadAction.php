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

namespace APF\extensions\fileupload\actions;

use APF\core\configuration\Configuration;
use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\extensions\fileupload\biz\FileUploadHandler;
use APF\extensions\fileupload\biz\UploadHandler;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

class UploadAction extends AbstractFrontcontrollerAction {

   protected $options;

   public function run() {

      // get file upload name out of url
      $name = $this->getInput()->getParameter('name');

      // load config
      $config = $this->getConfiguration('APF\extensions\fileupload', 'config.php');
      $completeConfig = $this->resolveStructure($config);
      $fileConfig = $completeConfig[$name];

      // create dynamic part of config
      $dynConfig = array(
         //'script_url' => $this->get_full_url().'/'.$this->basename($this->get_server_var('SCRIPT_NAME')),
            'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $fileConfig['UploadPath'] . '/',
            'upload_url' => LinkGenerator::generateUrl(Url::fromCurrent(true)->setPath('/' . $fileConfig['UploadPath'] . '/')),
            'param_name' => $name,
      );

      // merge options
      $this->options = array_merge($fileConfig, $dynConfig);

      if ($fileConfig['randomize']) {
         new FileUploadHandler($this->options, true);  // initialize the handler
      } else {
         // wrapper statt Datei manipulieren!!!
         new UploadHandler($this->options, true);
      }

      $response = $this->getResponse();
      $response->send();
   }

   /**
    * Parse the config file to use it as array
    * Source: http://adventure-php-framework.org/Seite/134-Konfiguration#Chapter-7-2-Implementierung-Provider
    *
    * @param $config Configuration config.php of FileUpload extension
    * @return array Config section of desired upload-form
    *
    * TODO Check whether we can implement this in a more effective way.
    *
    * @author dave
    * @version
    * Version 0.1, 30.01.2017<br />
    */
   private function resolveStructure($config) {

      $rawConfig = array();
      foreach ($config->getSectionNames() as $name) {
         $rawConfig[$name] = $this->resolveSection($config->getSection($name));
      }
      return $rawConfig;
   }

   /**
    * Parse the config file to use it as array
    * Source: http://adventure-php-framework.org/Seite/134-Konfiguration#Chapter-7-2-Implementierung-Provider
    *
    * TODO Check whether we can implement this in a more effective way.
    *
    * @param $config Configuration config.php of FileUpload extension
    * @return array Config section of desired upload-form
    *
    * @author dave
    * @version
    * Version 0.1, 30.01.2017<br />
    */
   private function resolveSection($config) {

      $rawConfig = array();
      foreach ($config->getValueNames() as $name) {
         $rawConfig[$name] = $config->getValue($name);
      }
      foreach ($config->getSectionNames() as $name) {
         $rawConfig[$name] = $this->resolveSection($config->getSection($name));
      }
      return $rawConfig;
   }
}