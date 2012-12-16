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
import('extensions::htmlheader::biz', 'HtmlHeaderManager');
import('extensions::htmlheader::biz', 'DynamicJsNode');
import('extensions::htmlheader::biz', 'CssPackageNode');
import('extensions::htmlheader::biz', 'StaticJsNode');

import('extensions::htmlheader::biz', 'JsContentNode');
import('extensions::htmlheader::biz', 'CssContentNode');
/**
 * @class form_taglib_multifileupload
 *
 * Taglib der ein Multifileupload Feld zur Verfügung stellt. Damit es mit allen Funktionen genutzt werden kann,
 * müssen die Notwendigen JS und CSS Dateien eingebunden werden.
 *
 * @param string $name - Name des Uploadfeldes
 * @param string $maxFileSize - Maximale Dateigröße (in Byte) (default: 10 MB)
 * @param string $MimeTypes - "application/pdf,image/gif" - hier können, kommagetrennt, alle erlaubten mimetypen angegeben werden.
 * @return integer
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 */
class form_taglib_multifileupload extends AbstractFormControl {

   /**
    * @var MultiFileUploadManager
    */
   private $MultifileuploadManager = null; //Multifileupload biz referenz
   private $LanguageConfig; //Sprachkonfiguration referenz
   private $MFUConfig; //MultiFileUploadConfig referenz
   private $formname; //Formularname
   private $name; //Oploadfeld-Name

   public function onParseTime() {

      $this->formname = $this->getParentObject()->getAttribute('name');
      $this->presetValue();

      // get Settings
      $this->name = $this->getAttribute('name');
      $maxFileSize = $this->getAttribute('max-file-size');
      $MimeTypes = $this->getAttribute('allowed-mime-types');

      // Einstellungen speichern.
      $this->MultifileuploadManager = &$this->getAndInitServiceObject('tools::form::multifileupload::biz', 'MultiFileUploadManager', array('formname' => $this->formname, 'name' => $this->name), 'SINGLETON');
      $this->MultifileuploadManager->setSettings($maxFileSize, explode(',', $MimeTypes));
   }

   /**
    * Es wird sichergestellt, dass das Formular im multipart/form-data modus vorliegt und die id gesetzt ist.
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function onAfterAppend() {
      $this->getParentObject()->setAttribute('enctype', 'multipart/form-data');
      $this->getParentObject()->setAttribute('id', $this->formname);
   }

   /**
    * Hier wird der nötige HTML und JS Code für das Formular erzeugt.
    *
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 18.07.2012 (Changed transform-method for final release)<br>
    * @version 1.2, 16.09.2012 (added full functionality to delete fildes, which are older than 1 day)<br>
    */
   public function transform() {

      // Dateien im temporären Verzischnis löschen, die älter als 86400 Sekunden (1 Tag) sind
      $this->MultifileuploadManager->deleteOldFiles();

      // Zugriff auf Sprachconfig:
      $this->LanguageConfig = $this->getConfiguration('tools::form::multifileupload', 'language.ini')->getSection($this->__Language);

      // Zugriff auf MultiFileUploadConfig
      $this->MFUConfig = $this->getConfiguration('tools::form::multifileupload', 'multifileupload.ini')->getSection($this->name);

      // Zugriff auf HTML-Header-Manager
      $HHM = $this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');


      /*       * ********** Upload Button erstellen Anfang *********** */
      $return = $this->createUploadButton();
      /*       * ********** Upload Button erstellen Ende *********** */


      /*       * ********** Dialoge erstellen Anfang *********** */
      $return .= $this->createDialogFileDelete();
      $return .= $this->createDialogFileSize();
      $return .= $this->createDialogFileType();
      /*       * ********** Dialoge erstellen Ende *********** */


      /*       * ********** Upload Tabellen erstellen Anfang *********** */
      $return .= '<table id="' . $this->name . '_files_upload_table"></table>';
      $return .= '<table id="' . $this->name . '_files_download_table">' . $this->createFileTable($this->MultifileuploadManager->getFiles()) . '</table>';
      /*       * ********** Upload Tabellen erstellen Ende *********** */


      /*       * ********** JSCode erstellen Anfang *********** */
      $HHM->addNode(new JsContentNode($this->createJSCode()));
      /*       * ********** JSCode erstellen Ende *********** */


      /*       * ********* CSS-Code Ladbild Angfang *********** */
      $HHM->addNode(new CssContentNode(
         '.file_upload_progress .ui-progressbar-value {
                  background: url(' . $this->MFUConfig->getValue('loadingimage.dir') . '/' . $this->MFUConfig->getValue('loadingimage.name') . ');
               }'));
      /*       * ********* CSS-Code Ladbild Ende *********** */

      // Ausgabe
      return $return;
   }

   /**
    * Diese Funktion baut aus allen vom User auf den Server geladenen Dateien eine Downloadtabelle.
    * Dies ist vor allem für jene Leute wichtig, die kein JS aktiviert haben.
    *
    * @param array $files - Array mit allen Dateien kommend aus der biz.
    * @return string
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function createFileTable($files) {
      $buffer = '';
      if (count($files) > 0) {

         foreach ($files as $file) {
            $image = '';
            if (strpos($file['type'], 'image') !== false) {
               $image = '<img src="' . $file['filelink'] . '" alt="' . $file['name'] . '" /> ';
            }

            $buffer .= '<tr>'
                  . '<td class="file_upload_preview">' . $image . '</td>'
                  . '<td><a href="' . $file['filelink'] . '" target="_blank">' . $file['name'] . '</a></td>'
                  . '<td>' . $file['filesize'] . '</td>'
                  . '<td class="delete"><a href="' . $file['deletelink'] . '" target="_blank" onclick="return deletefile(\'' . $file['deletelink'] . '\',this)"><div class="ui-state-default ui-corner-all" title="' . $this->LanguageConfig->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $this->LanguageConfig->getValue('delete.label') . '</span></div></a></td>'
                  . '</tr>';
         }
      }
      return $buffer;
   }

   /**
    * Lädt die Dateien hinauf, wenn das Formular ohne JS ausgeführt wurde.
    *
    * @return boolean erfolg - Falls es möglich war dateien hinzuzufügen liefert die funktion true, sonst false
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function uploadFiles() {
      $name = &$this->name;
      if (isset($_FILES[$name]) && $_FILES[$name]['name'] != '') {
         $addfile = $this->MultifileuploadManager->addFile($_FILES[$name], false);
         unset($_FILES[$name]);
         return $addfile;
      } else {
         return false;
      }
   }

   /**
    * Liefert das Datei Array des aktuellen Formularnutzers zurück.
    *
    * @return array
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function getFiles() {
      return $this->MultifileuploadManager->getFiles();
   }

   /**
    * Verschiebt die angegebene Datei an den angegeben Ort.
    *
    * @param string $uploadname - md5 Wert der raufgeladenen Datei
    * @param string $dir - Zielverzeichnis
    * @param string $name - Name unter dem die Datei gespeichert wird.
    * @return FilesystemManager::renameFile
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 14.09.2012 (removed bug for method moveFile)<br>
    */
   public function moveFile($uploadname, $dir, $name) {
      return $this->MultifileuploadManager->moveFile($uploadname, $dir, $name);
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
      return $this->MultifileuploadManager->getUploadPath();
   }

   /**
    * Erstellt anhand eines übergebenen Array MimeTypes einen String mit erlaubten Dateiendungen
    *
    * @param array $MimeTypes
    * @return string
    *
    * @author dave
    * @version 1.0, 13.07.2012<br>
    */
   private function createFileExtensionFromMimeType($MimeTypes) {

      $endung = '';
      foreach ($MimeTypes as $mimeType) {
         $endung .= substr(strrchr($mimeType, "/"), 1) . ', ';
      }

      //Letzten 2 Zeichen entfernen
      $endung = substr($endung, 0, -2);

      return $endung;
   }

   /**
    * Erzeugt den String um die erlaubten Dateityopen per JS zu prüfen
    *
    * @param array $MimeTypes
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012
    */
   private function createFileExtensionForJS($MimeTypes) {

      // Erlaube MimeTypen entsprechend aufbereiten damti sie im JS verwendet werden können:
      $endungen = '';
      // Die erlaubten MimeTypen für den Fehler Dialog aufbereiten.
      $dateitypen = '';
      foreach ($MimeTypes as $mimeType) {

         $endung = substr(strrchr($mimeType, "/"), 1);

         if ($endungen == '') {
            $endungen = '/\.(' . $endung . ')';
            $dateitypen = $endung;
         } else {
            $endungen .= '|(' . $endung . ')';
            $dateitypen .= ', ' . $endung;
         }
      }
      return $endungen .= '$/i';
   }

   /**
    * Erstellt den Dialog zum Löschen von Dateien
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 13.07.2012<br>
    */
   private function createDialogFileDelete() {

      return '<div class="confirm_delete ui-dialog-content ui-widget-content dialog_confirm_delete" title="'
            . $this->LanguageConfig->getValue('delete.title') . '">'
            . $this->LanguageConfig->getValue('delete.message') . '</div>';
   }

   /**
    * Erstellt den Dialog für die Dateigrösse
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 13.07.2012<br>
    */
   private function createDialogFileSize() {

      return '<div class="filesize_dialog ui-dialog-content ui-widget-content" title="'
            . $this->LanguageConfig->getValue('filesize.title') . '">'
            . $this->LanguageConfig->getValue('filesize.message') . ' ' . $this->MultifileuploadManager->getMaxFileSizeWithUnit() . '</div>';
   }

   /**
    * Erstellt den Dialog für die erlaubten Dateitypen
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 13.07.2012<br>
    */
   private function createDialogFileType() {

      return '<div class="filetype_dialog ui-dialog-content ui-widget-content" title="'
            . $this->LanguageConfig->getValue('filetype.title') . '">'
            . $this->LanguageConfig->getValue('filetype.message') . ' ' . $this->createFileExtensionFromMimeType($this->MultifileuploadManager->getMimeTypes()) . '</div>';
   }

   /**
    * Erstellt den HTML Code für den Upload-Button
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 13.07.2012<br>
    */
   private function createUploadButton() {

      return '<div id="' . $this->name . '_file_upload_container"><input type="file" name="' . $this->name . '" id="' . $this->name . '" multiple="multiple" /><button>'
            . $this->LanguageConfig->getValue('upload.button.label') . '</button><div class="uploadlabel">'
            . $this->LanguageConfig->getValue('upload.label') . '</div></div>';
   }

   /**
    * Erzeugt den Tag für die Upload-Tabelle
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012<br>
    */
   private function createUploadTable() {

      return '$(\'#' . $this->name . '_files_upload_table\'),';
   }

   /**
    * Erzeugt den Tag für die Downloadtabelle
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012<br>
    */
   private function createDownloadTable() {

      return '$(\'#' . $this->name . '_files_download_table\'),';
   }

   /**
    * Füllt die Uploadtabelle mit Inhalt
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012<br>
    * @verison 1.1, 16.09.2012 (Fixed some smaller problems with escaping of tags)<br>
    */
   private function buildUploadRow() {

      return '
         function (files, index) {
            return $(\'<tr>\' +
                  \'<td class="file_upload_preview"><\/td>\' +
                  \'<td>\' + files[index].name + \'<\/td>\' +
                  \'<td class="file_upload_progress"><div><\/div><\/td>\' +
                  \'<td class="value">0 %<\/td>\' +
                  \'<td class="delete"><button class="ui-state-default ui-corner-all" title="' . $this->LanguageConfig->getValue('cancel.label') . '"><span class="ui-icon ui-icon-cancel">' . $this->LanguageConfig->getValue('cancel.label') . '<\/span><\/button><\/td>\' +
               \'<\/tr>\');
         },';
   }

   /**
    * Füllt die Downloadtabelle mit Inhalt
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012<br>
    * @verison 1.1, 16.09.2012 (Fixed some smaller problems with escaping of tags)<br>
    */
   private function buildDownloadRow() {

      return '
         function (file) {
            var bild = "";
            var regexp = new RegExp(/^image\/(gif|jpeg|png|jpg)$/);
            if (regexp.test(file.type)) {
               bild = \'<img src="\' + file.filelink + \'" alt="\' + file.name + \'" />\';
            }
            return $(\'<tr>\' +
                  \'<td class="file_upload_preview">\' + bild + \' <\/td>\' +
                  \'<td><a href="\' + file.filelink + \'" target="_blank" >\' + file.name + \'<\/a><\/td>\' +
                  \'<td>\'+file.filesize+\'<\/td>\'+
                  \'<td><a href="\' + file.deletelink + \'" target="_blank" onclick="return deletefile(\\\'\' + file.deletelink + \'\\\',this)" ><div class="ui-state-default ui-corner-all" title="' . $this->LanguageConfig->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $this->LanguageConfig->getValue('delete.label') . '<\/span><\/div><\/a><\/td>\'+
               \'<\/tr>\');
         },';
   }

   /**
    * Erzeugt den JS Code zum Prüfen der Dateigrösse
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012
    */
   private function createFileSizeCheck() {

      return '
         if (files[index].size > ' . $this->MultifileuploadManager->getMaxFileSize() . ') {
            $(".filesize_dialog").dialog({
               modal: true,
               height: 220,
               width: 300,
               buttons: {
                  "' . $this->LanguageConfig->getValue('filesize.ok') . '": function() {
                     $(this).dialog("close");
                  }
               }
            });
            handler.uploadRow.remove();
            return;
         }';
   }

   /**
    * Erzeugt den JS Code zum Prüfen des MimeType
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012
    */
   private function createMimeTypeCheck() {

      return '
         var regexp = ' . $this->createFileExtensionForJS($this->MultifileuploadManager->getMimeTypes()) . ';
         if (!regexp.test(files[index].name)) {
            $(".filetype_dialog").dialog({
               modal: true,height:220,width:300,
               buttons: {
                  "' . $this->LanguageConfig->getValue('filetype.ok') . '": function() {
                     $(this).dialog("close");
                  }
               }
            });
         handler.uploadRow.remove();
         return;
         }';
   }

   /**
    * Erstellt den fertigen JSCode, damit dieser über den Hrml-Header-Manager ausgegeben werden kann
    *
    * @return string
    *
    * @author dave
    * @version 1.0, 14.07.2012
    */
   private function createJSCode() {

      $code = '
            $(".uploadlabel").css("display", "block");

            function deletefile(link, elem) {

               $(".confirm_delete").dialog("destroy");
               $(".uploadlabel").show();
               $(".confirm_delete").dialog({
                  modal: true,
                  height: 240,
                  width: 400,
                  buttons: {
                     "' . $this->LanguageConfig->getValue('delete.ok') . '": function() {
                        $(this).dialog("close");
                        var jqxhr = $.ajax({
                           url: link
                        })
                        .success(function() {
                           $(elem).parent().parent().remove();
                        })
                     },
                     "' . $this->LanguageConfig->getValue('delete.no') . '" : function() {
                        $(this).dialog("close");
                        return false;
                     }
                  }
               });
               return false;
            }

            $(function () {
                $(\'#' . $this->formname . '\').fileUploadUI({
                   url: "' . $this->MultifileuploadManager->link() . '",
                   fieldName: "' . $this->name . '",
                   uploadTable: ' . $this->createUploadTable() . '
                   downloadTable: ' . $this->createDownloadTable() . '
                   buildUploadRow: ' . $this->buildUploadRow() . '
                   buildDownloadRow: ' . $this->buildDownloadRow() . '
                   
                   dropZone: $("#' . $this->name . '_file_upload_container"),
                   parseResponse: function (xhr) {
                      if (typeof xhr.responseText !== "undefined") {
                            return $.parseJSON(xhr.responseText);
                      } else {
                            return $.parseJSON(xhr.contents().text());
                      }
                   },
                   onProgress: function (event, files, index, xhr, handler) {
                      if (handler.progressbar) {
                         var value=parseInt(event.loaded / event.total * 100, 10);
                         handler.uploadRow.find(".value").text(value + "%");
                         handler.progressbar.progressbar(\'value\',value);
                      }
                   },
                   beforeSend : function (event, files, index, xhr, handler, callBack) {
                      ' . $this->createFileSizeCheck() . $this->createMimeTypeCheck() . '
                      callBack();
                   }
               });
            });';

      return $code;
   }

}
