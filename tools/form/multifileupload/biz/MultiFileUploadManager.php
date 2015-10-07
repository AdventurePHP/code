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
namespace APF\tools\form\multifileupload\biz;

use APF\core\http\mixins\GetRequestResponse;
use APF\core\http\Session;
use APF\core\loader\RootClassLoader;
use APF\core\pagecontroller\APFObject;
use APF\tools\filesystem\File;
use APF\tools\filesystem\Folder;
use APF\tools\form\FormException;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Business component for the multi file upload extension.
 *
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 */
class MultiFileUploadManager extends APFObject {

   use GetRequestResponse;

   private $sessionNamespace;
   private static $DEFAULT_SESSION_NAMESPACE = 'APF\tools\form\multifileupload';

   private $maxFileSize = null;
   private $mimeTypes = array();
   private $settingsLoaded = false;
   private $formName;
   private $name;

   private $tmpUploadPath = 'APF\tools\form\multifileupload\uploaddir';

   /**
    * @var Session $session
    */
   private $session;

   /**
    * Initializes the service. Required parameters are <em>formname</em> and <em>name</em> as associative array.
    *
    * @param array $param ('formname'=>'Formularname', 'name'=>'Name_mit_dem_der_Taglib_eingebaut_wurde')
    *
    * @throws FormException In case the init params are missing/incomplete.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
    * @version 1.2, 14.08.2012 (Change to new File-/Folder-class)<br>
    */
   public function __construct(array $param = []) {
      if (!isset($param['formname']) || !isset($param['name'])) {
         throw new FormException('[' . get_class($this) . '::init()] MultiFileUpload init params are not correct!', E_USER_ERROR);
      }
      $this->formName = $param['formname'];
      $this->name = $param['name'];
      $this->sessionNamespace = self::$DEFAULT_SESSION_NAMESPACE . '\\' . $param['formname'] . '\\' . $param['name'];
      $this->session = $this->getRequest()->getSession($this->sessionNamespace);

      // Temporäres Upload-Verzeichnis erstellen, falls es vorhanden ist, wird der Pfad zurück gegeben.
      $createFolder = new Folder();
      $uploadPath = $this->getUploadPath();
      $createFolder->create($uploadPath);

      if (!$createFolder) {
         throw new FormException('[' . get_class($this) . '::init()] The desired folder "'
               . $this->getContext() . '" could not be created under "'
               . $uploadPath . '"! Please create the folder manually.', E_USER_ERROR);
      }
   }

   /**
    * Funktion liefert das temporäre Uplaodverzeichnis zurück
    *
    * @return string Uplaodpath
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getUploadPath() {
      return $this->getRootPath() . '/' . str_replace('\\', '/', $this->tmpUploadPath) . '/' . $this->getContext();
   }

   /**
    * Funktion liefert alle Dateien die mit dem Formular übertragen wurden.
    *
    * @return array Files
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getFiles() {
      $this->checkSessionFiles();

      return $this->session->load('files');
   }

   /**
    * Entfernt Dateien die älter als die angegebene Zeit sind (ausgegangen wird vom CreationTimestamp - Dieser kann je nach
    * Betriebsystem variieren!)
    *
    * @param int $seconds Dateien die älter sind als dieser Wert werden gelöscht. (Default: 86400 Sekunden => 1 Tag)
    *
    * @throws \APF\tools\filesystem\FilesystemException In case anything goes wrong.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version
    * 1.0, 14.3.2011<br>
    * 1.1, 14.08.2012 (Change to new File-/Folder-class)<br>
    * 1.2, 17.08.2012 (Completely refactored method for full functionality)
    */
   public function deleteOldFiles($seconds = 86400) {

      $Folder = new Folder();
      $files = $Folder->open($this->getUploadPath())->getContent();

      foreach ($files as $file) {
         //Erstellungsdatum ermittlen
         $CreationTime = $file->getCreationTime()->format('Y-m-d H:i:s');
         $Zeitstempel = strtotime($CreationTime);

         //Ist die Datei bereits älter als gewünscht? -> Delete!
         if (($Zeitstempel + $seconds) < time()) {
            $file->delete();
         }
      }
      $this->checkSessionFiles();
   }

   /**
    * Alle alten Dateien aus der Session löschen, sofern nicht mehr vorhanden.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function checkSessionFiles() {
      $files = $this->session->load('files');
      if (is_array($files)) {
         $files_buff = null;
         $uploadpath = $this->getUploadPath();
         foreach ($files as $file) {
            if (!file_exists($uploadpath . '/' . $file['uploadname'])) {
               unset($file);
               continue;
            }
            $files_buff[] = $file;
         }
         $this->session->save('files', $files_buff);
      }
   }

   /**
    * Speichert eine neue Datei in Ordner der Temporären Dateien.
    * Es wird die neue Datei in die Session geschrieben, damit sie nachher leicht wieder ausgelesen werden kann.
    *
    * @param array $file
    * @param boolean $js True, in case of java script upload, false otherwise.
    *
    * @return boolean
    * @throws FormException
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.03.2011<br>
    * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
    * @version 1.2, 14.08.2012 (Change to new File-/Folder-class)<br>
    */
   public function addFile($file, $js = true) {
      // Einstellungen laden.
      if ($this->settingsLoaded == false) {
         $this->loadSettings();
      }
      $file['uploadname'] = md5($file['tmp_name']) . '.mfuc';
      $file['filesize'] = $this->formatBytes($file['size']);
      $file['deletelink'] = $this->getDeleteLink($file['uploadname']);
      $file['filelink'] = $this->getFileLink($file['uploadname']);

      $uploadPath = $this->getUploadPath();

      if (!file_exists($uploadPath)) {
         throw new FormException('[' . get_class($this) . '::addFile()] The Upload Path "'
               . $uploadPath . '" does not exist!', E_USER_ERROR);
      }

      $moved = false;

      //File uploaden
      if (is_uploaded_file($file['tmp_name'])) {
         // check if target already exists. if not, upload it
         $target_file = $uploadPath . '/' . $file['uploadname'];
         if (!file_exists($target_file)) {
            move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $file['uploadname']);
            $moved = true;
         }
      }

      if ($moved == true) {
         $files = $this->session->load('files');
         $files[] = $file;
         $this->session->save('files', $files);

         // wenn diese Funktion mittels JS aufgerufen wird, dann liefert sie ein array mit den
         // dateiinfos zurück. ansonsten einfach nur true.
         if ($js == false) {
            return true;
         } else {
            return array('name' => $file['name'], 'type' => $file['type'], 'filesize' => $file['filesize'], 'size' => $file['size'], 'uploadname' => $file['uploadname'], 'deletelink' => $file['deletelink'], 'filelink' => $file['filelink']);
         }
      } else {
         throw new FormException('[' . get_class($this) . '::addFile()] This file "'
               . $file['name'] . '" has not been uploaded!', E_USER_ERROR);
      }
   }

   /**
    * Verschiebt die angegebene Datei in das neue Verzeichnis und nennt sie entsprechend um.
    *
    * @param string $uploadFileName - Name der Datei die verschoben werden soll.
    * @param string $dir - Zielverzeichnis
    * @param string $name - Zieldateiname
    *
    * @return File::moveTo
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.03.2011<br>
    * @version 1.1, 14.08.2012 (Change to new File-/Folder-class)<br>
    * @veriosn 1.2, 14.09.2012 (Removed bug for moving file with File-Folder-Class)<br>
    * @version 1.3, 19.09.2012 (Create the disered directory and open it instead of just open it)<br>
    */
   public function moveFile($uploadFileName, $dir, $name) {
      $File = new File();
      //open File
      $File->open($this->getUploadPath() . '/' . $uploadFileName);
      //rename File
      $File->renameTo($name);
      //delete File from session
      $this->deleteFileFromSession($uploadFileName);
      //move File and return it
      $targetDir = new Folder();
      $targetDir->create($dir); //Create directory if not already done
      return $File->moveTo($targetDir);
   }

   /**
    * Liefert das Array der angeforderten Datei
    *
    * @param string $uploadname - Dateiname
    *
    * @return array
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getFile($uploadname) {
      $files = $this->getFiles();
      if (is_array($files)) {
         $uploadpath = $this->getUploadPath();
         foreach ($files as $file) {
            if ($file['uploadname'] == $uploadname && file_exists($uploadpath . '/' . $uploadname)) {
               return $file;
            }
         }
      }

      return false;
   }

   /**
    * Löscht die Übergebene Datei. Wird nichts übergeben, werden alle Datein gelöscht.
    *
    * @param string $uploadname
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.03.2011<br>
    * @version 1.1, 14.08.2012 (Change to new File-/Folder-class)<br>
    */
   public function deleteFile($uploadname = null) {
      $uploadpath = $this->getUploadPath();
      if ($uploadname === null) {
         $folder = new Folder();
         $files = $folder->open($uploadpath)->getContent();
         foreach ($files as $file) {
            if (file_exists($file)) {
               $file->delete();
            }
         }
      } else {
         if (file_exists($uploadpath . '/' . $uploadname)) {
            $file = new File();
            $file->open($uploadpath . '/' . $uploadname)->delete();
         }
      }
      $this->deleteFileFromSession($uploadname);
   }

   /**
    * Löscht die mittels uploadname übergebene Datei aus der Session.
    * Wenn nichts übergeben wird, werden alle gelöscht.
    *
    * @param string $uploadname
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function deleteFileFromSession($uploadname) {
      $files = $this->getFiles();
      if (is_array($files)) {
         $files_buff = null;
         foreach ($files as $file) {
            if ($uploadname === null) {
               #unlink($this->tmpUploadPath . '/' . $files[$i]['uploadname']);
               unset($file);
               continue;
            } elseif ($file['uploadname'] == $uploadname) {
               #unlink($this->tmpUploadPath . '/' . $files[$i]['uploadname']);
               unset($file);
               continue;
            }
            $files_buff[] = $file;
         }
         $this->session->save('files', $files_buff);
      }
   }

   /**
    * Saves all settings that have applied by the responsible taglib.
    *
    * @param string $fileSize - Byte value of the maximum file size (default: 10 MB)
    * @param string[] $mimeTypes - Array with all allowed MIME types (default: pdf,gif,jpeg,png)
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function setSettings($fileSize = null, array $mimeTypes = array()) {

      $settings = array();
      $settings['max'] = 10485760; // 10485760 is 10 MB

      if ($fileSize !== null) {
         $settings['max'] = intval($fileSize);
      }

      if (count($mimeTypes) > 0 && $mimeTypes[0] != '') {
         $settings['mime'] = $mimeTypes;
      } else {
         $settings['mime'][] = 'application/pdf';
         $settings['mime'][] = 'application/x-download'; // some PDF files have this MIME type for unknown reason!
         $settings['mime'][] = 'image/gif';
         $settings['mime'][] = 'image/jpeg';
         $settings['mime'][] = 'image/jpg';
         $settings['mime'][] = 'image/png';
      }

      $this->mimeTypes = $settings['mime'];
      $this->maxFileSize = $settings['max'];
      $this->session->save('settings', $settings);
   }

   /**
    * Läd alle Einstellungen aus der Session
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function loadSettings() {
      $settings = $this->session->load('settings');
      $this->mimeTypes = $settings['mime'];
      $this->maxFileSize = $settings['max'];
      $this->settingsLoaded = true;
   }

   /**
    * Creates the upload link.
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function link() {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(false);
      $link = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\tools\form\multifileupload', 'multifileupload', array(
            'formname' => $this->formName,
            'name'     => $this->name), $scheme);

      return $link;
   }

   /**
    * Erstellt den Link um anhand des übergebenen Dateinamen diese zu löschen
    *
    * @param string $uploadname
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getDeleteLink($uploadname) {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(false);
      $link = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\tools\form\multifileupload', 'multifiledelete', array(
            'formname'   => $this->formName,
            'name'       => $this->name,
            'uploadname' => $uploadname), $scheme);

      return $link;
   }

   /**
    * Funktion die die übermittelte Größe in Byte auf eine komfortable einheit umrechnet.
    *
    * @param integer $size
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function formatBytes($size) {
      $units = array(' B', ' KB', ' MB', ' GB', ' TB');
      for ($i = 0; $size >= 1024 && $i < 4; $i++)
         $size /= 1024;

      return round($size, 2) . $units[$i];
   }

   /**
    * Liefert alle erlaubten MimeTypen als Array zurück
    *
    * @return array $mimeTypes
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getMimeTypes() {
      return $this->mimeTypes;
   }

   /**
    * Liefert die maximale Dateigröße zurück
    *
    * @return integer $maxFileSize
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getMaxFileSize() {
      return $this->maxFileSize;
   }

   /**
    * Liefert die maximale Dateigröße mit Einheit zurück
    *
    * @return string $maxFileSize
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getMaxFileSizeWithUnit() {
      return $this->formatBytes($this->maxFileSize);
   }

   /**
    * Erzeugt den Link mit dem Dateien angezeigt werden können.
    *
    * @param string $uploadname
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function getFileLink($uploadname) {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(true);

      return LinkGenerator::generateActionUrl(
            Url::fromCurrent(), 'APF\tools\form\multifileupload', 'multifilegetfile', array(
            'formname'   => $this->formName,
            'name'       => $this->name,
            'uploadname' => $uploadname), $scheme);
   }

   /**
    * Reads a file and streams it back to the user.
    *
    * @param string $uploadFileName The file to output.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function deliverFile($uploadFileName) {
      readfile($this->getUploadPath() . '/' . $uploadFileName);
   }

   /**
    * @return string The root path of the APF class loader.
    */
   private function getRootPath() {
      return RootClassLoader::getLoaderByVendor('APF')->getRootPath();
   }

}
