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
import('core::session', 'SessionManager');
import('tools::filesystem', 'FilesystemManager');
import('tools::link', 'LinkGenerator');
import('tools::filesystem', 'FilesystemManager');
import('tools::form', 'FormException');
/**
 * @class multifileupload
 *
 * Businesskomponente der Multifileupload Erweiterung
 *
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 */
class MultiFileUploadManager extends APFObject {

   private $sessionnamespace;
   private $sessionnamespace_default = 'tools::form::multifileupload';
   private $maxFileSize = null;
   private $MimeTypes = array();
   private $settingsloaded = false;
   private $formname;
   private $name;
   private $tmpuploadpath = 'tools::form::multifileupload::uploaddir';

   /**
    * @var SessionManager
    */
   private $sessionmanager;

   /**
    * Initiert das Service. Es werden die nötigen Parameter name und formname erwartet.
    * <p/>
    * The init param is as follows: <em>$param('formname' => 'Formularname', 'name' => 'Name_mit_dem_der_Taglib_eingebaut_wurde')</em>.
    *
    * @param array $param
    * @throws FormException In case the form name cannot be evaluated.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
    */
   public function init($param) {
      if (!isset($param['formname']) || !isset($param['name'])) {
         throw new FormException('[' . get_class($this) . '::addFile()] MultiFileUpload init params are not correct!', E_USER_ERROR);
      }
      $this->formname = $param['formname'];
      $this->name = $param['name'];
      $this->sessionnamespace = $this->sessionnamespace_default . ':' . $param['formname'] . ':' . $param['name'];
      $this->sessionmanager = new SessionManager($this->sessionnamespace);

      //Check: Existiert das Verzeichnis bereits?
      if (!is_dir($this->getUploadPath())) {
         FilesystemManager::createFolder($this->getUploadPath());
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
      return str_replace('::', '/', APPS__PATH . '::' . $this->tmpuploadpath . '::' . $this->getContext());
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
      return $this->sessionmanager->loadSessionData('files');
   }

   /**
    * Entfernt Dateien die älter als einen Tag sind. Damit soll verhindert werden dass kurz nach mitternacht neue dateien gelöscht werden.
    *
    * @params int $seconds - Dateien die älter sind als dieser Wert werden gelöscht. (Default: 3600 Sekunden => 1 Stunde)
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function deleteOldFiles($seconds = 3600) {

      $files = FilesystemManager::getFolderContent($this->getUploadPath());

      foreach ($files as $file) {
         $attributes = FilesystemManager::getFileAttributes($file);
         $dateArr = explode('-', $attributes['modificationdate']);
         $dateInt = mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);
         if (($dateInt - time()) > $seconds) {
            unlink($file);
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
      $files = $this->sessionmanager->loadSessionData('files');
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
         $this->sessionmanager->saveSessionData('files', $files_buff);
      }
   }

   /**
    * Speichert eine neue Datei in Ordner der Temporären Dateien.
    * Es wird die neue Datei in die Session geschrieben, damit sie nachher leicht wieder ausgelesen werden kann.
    *
    * @param array $file
    * @param bool $js
    * @return bool
    * @throws FormException
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
    */
   public function addFile($file, $js = true) {
      // Einstellungen laden.
      if ($this->settingsloaded == false) {
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

      $moved = FilesystemManager::uploadFile($uploadPath, $file['tmp_name'], $file['uploadname'], $file['size'], $this->maxFileSize, $file['type'], $this->MimeTypes);

      if ($moved == true) {
         $files = $this->sessionmanager->loadSessionData('files');
         $files[] = $file;
         $this->sessionmanager->saveSessionData('files', $files);

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
    * @return bool True in case of success, false otherwise.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function moveFile($uploadFileName, $dir, $name) {
      $sourceFile = $this->getUploadPath() . '/' . $uploadFileName;
      $targetFile = $dir . '/' . $name;
      $this->deleteFileFromSession($uploadFileName);
      return FilesystemManager::renameFile($sourceFile, $targetFile);
   }

   /**
    * Liefert das Array der angeforderten Datei
    *
    * @param string $uploadname - Dateiname
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
    * @version 1.0, 14.3.2011<br>
    */
   public function deleteFile($uploadname = null) {
      $uploadpath = $this->getUploadPath();
      if ($uploadname === null) {
         $files = FilesystemManager::getFolderContent($uploadpath);
         foreach ($files as $file) {
            if (file_exists($file)) {
               unlink($file);
            }
         }
      } else {
         if (file_exists($uploadpath . '/' . $uploadname)) {
            unlink($uploadpath . '/' . $uploadname);
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
               #unlink($this->tmpuploadpath . '/' . $files[$i]['uploadname']);
               unset($file);
               continue;
            } elseif ($file['uploadname'] == $uploadname) {
               #unlink($this->tmpuploadpath . '/' . $files[$i]['uploadname']);
               unset($file);
               continue;
            }
            $files_buff[] = $file;
         }
         $this->sessionmanager->saveSessionData('files', $files_buff);
      }
   }

   /**
    * Speichert alle über den Taglib erstellten Einstellungen.
    *
    * @param string $filesize - Byte Wert der Maximalen Dateigröße (default: 10 MB)
    * @param array $mimeTypes - Array mit allen MimeTypes die erlaubt sind. (default: pdf,gif,jpeg,png)
    * @return integer
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function setSettings($filesize = null, array $mimeTypes = array()) {

      $settings = array();
      $settings['max'] = 10485760; // 10485760 entspricht 10 MB

      if ($filesize !== null) {
         $settings['max'] = intval($filesize);
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

      $this->MimeTypes = $settings['mime'];
      $this->maxFileSize = $settings['max'];
      $this->sessionmanager->saveSessionData('settings', $settings);
   }

   /**
    * Läd alle Einstellungen aus der Session
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function loadSettings() {
      $settings = $this->sessionmanager->loadSessionData('settings');
      $this->MimeTypes = $settings['mime'];
      $this->maxFileSize = $settings['max'];
      $this->settingsloaded = true;
   }

   /**
    * Erstellt den uploadLink
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function link() {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(false);
      $link = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'tools::form::multifileupload', 'multifileupload', array(
         'formname' => $this->formname,
         'name' => $this->name), $scheme);
      return $link;
   }

   /**
    * Erstellt den Link um anhand des übergebenen Dateinamen diese zu löschen
    *
    * @param string $uploadname
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getDeleteLink($uploadname) {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(false);
      $link = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'tools::form::multifileupload', 'multifiledelete', array(
         'formname' => $this->formname,
         'name' => $this->name,
         'uploadname' => $uploadname), $scheme);
      return $link;
   }

   /**
    * Funktion die die übermittelte Größe in Byte auf eine komfortable einheit umrechnet.
    *
    * @param integer $size
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
      return $this->MimeTypes;
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
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function getFileLink($uploadname) {
      $scheme = LinkGenerator::cloneLinkScheme();
      $scheme->setEncodeAmpersands(true);

      return LinkGenerator::generateActionUrl(
         Url::fromCurrent(), 'tools::form::multifileupload', 'multifilegetfile', array(
         'formname' => $this->formname,
         'name' => $this->name,
         'uploadname' => $uploadname), $scheme);
   }

   /**
    * Liest die Datei ein und gibt sie zurück.
    *
    * @param string $uploadFileName
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function readfile($uploadFileName) {
      return readfile($this->getUploadPath() . '/' . $uploadFileName);
   }

}
