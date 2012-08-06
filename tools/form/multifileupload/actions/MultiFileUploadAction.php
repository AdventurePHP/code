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
 * @class multifileuploadAction
 *
 * Diese Action ist dazu da, die mittels multifileupload Formular raufgeladenen Dateien entgegen zu nehmen zu speichern
 *
 * @param string $name - Name des Formularfeldes
 * @param string $formname -Name des Formulares
 * @return json $fileinfo
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
 */
class MultiFileUploadAction extends AbstractFrontcontrollerAction {

   public function run() {
      // Header modifizieren, damit nichts gecached wird.
      HeaderManager::send('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      HeaderManager::send('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
      HeaderManager::send('Cache-Control: no-cache, must-revalidate');
      HeaderManager::send('Pragma: no-cache');

      try {
         //Formular und Feldnamen erhalten.
         $Fieldname = $this->getInput()->getAttribute('name');
         $formname = $this->getInput()->getAttribute('formname');
         // Anhand des Feldnamen auf die Datei zugreifen und sie in die Session eintragen.
         $MultifileuploadManager = &$this->getAndInitServiceObject('tools::form::multifileupload::biz', 'MultiFileUploadManager', array('formname' => $formname, 'name' => $Fieldname));
         if (!empty($_FILES[$Fieldname])) {
            $fileinfo = $MultifileuploadManager->addFile($_FILES[$Fieldname]);
            echo json_encode($fileinfo);
         } else {
            throw new FormException('[' . get_class($this) . '::run()] No file was transmitted to the server!', E_USER_ERROR);
         }
         exit(0);
      } catch (FormException $e) {
         throw $e;
      }
   }

}

?>