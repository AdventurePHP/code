<?php
namespace APF\tools\form\multifileupload\actions;

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
use APF\tools\http\HeaderManager;
use APF\tools\form\FormException;

/**
 * @package tools::form::multifileupload::actions
 * @class MultiFileGetFileAction
 *
 * This action delivers a file that has been uploaded using the multi-upload feature.
 *
 * @param string $name - Name des Formularfeldes
 * @param string $formname - Name des Formulares
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
 */
class MultiFileGetFileAction extends AbstractFrontcontrollerAction {

   public function run() {
      //Formular, Feldnamen sowie uploadnamen erhalten.
      $name = $this->getInput()->getAttribute('name');
      $formname = $this->getInput()->getAttribute('formname');
      $uploadname = $this->getInput()->getAttribute('uploadname');

      // Wenn alle Variablen vorhanden sind, dann Datei laden.
      if ($name !== null && $formname !== null && $uploadname !== null) {

         /* @var $M MultiFileUploadManager */
         $M = &$this->getAndInitServiceObject('tools::form::multifileupload::biz', 'MultiFileUploadManager', array('formname' => $formname, 'name' => $name));
         $file = $M->getFile($uploadname);
         if (is_array($file)) {
            // Header senden
            HeaderManager::send('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            HeaderManager::send('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
            HeaderManager::send('Cache-Control: no-cache, must-revalidate');
            HeaderManager::send('Pragma: no-cache');
            HeaderManager::send('Content-type: ' . $file['type']);
            HeaderManager::send('Content-Disposition: inline; filename="' . $file['name'] . '"');
            HeaderManager::send('Content-Length: ' . $file['size']);
            $M->deliverFile($uploadname);
         } else {
            throw new FormException('[' . get_class($this) . '::run()] The file was not found on the server!', E_USER_ERROR);
         }
      } else {
         throw new FormException('[' . get_class($this) . '::run()] Parameters are missing!', E_USER_ERROR);
      }
      exit(0);
   }

}
