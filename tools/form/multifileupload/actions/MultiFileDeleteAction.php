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
import('tools::form', 'FormException');

/**
 * @package tools::form::multifileupload::actions
 * @class MultiFileDeleteAction
 *
 * This action deletes the given file physically and from session.
 *
 * @param string $name - Name des Formularfeldes
 * @param string $formname - Name des Formulars
 * @return boolean erfolgreich - true|false
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
 */
class MultiFileDeleteAction extends AbstractFrontcontrollerAction {

   public function run() {
      // Header modifizieren, damit nichts gecached wird.
      HeaderManager::send('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      HeaderManager::send('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
      HeaderManager::send('Cache-Control: no-cache, must-revalidate');
      HeaderManager::send('Pragma: no-cache');


      try {
         //Formular, Feldnamen sowie uploadnamen erhalten.
         $Fieldname = $this->getInput()->getAttribute('name');
         $formname = $this->getInput()->getAttribute('formname');
         $uploadname = $this->getInput()->getAttribute('uploadname');

         /* @var $MultifileuploadManager MultiFileUploadManager */
         $MultifileuploadManager = &$this->getAndInitServiceObject('tools::form::multifileupload::biz', 'MultiFileUploadManager', array('formname' => $formname, 'name' => $Fieldname));
         if ($uploadname !== null) {
            $MultifileuploadManager->deleteFile($uploadname);
            echo "Erfolgreich gelöscht!";
         } else {
            throw new FormException('[' . get_class($this) . '::run()] No filename was transmitted to the server!', E_USER_ERROR);
         }
         exit(0);
      } catch (FormException $e) {
         throw $e;
      }
   }

}
