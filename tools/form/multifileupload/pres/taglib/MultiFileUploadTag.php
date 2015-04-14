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
namespace APF\tools\form\multifileupload\pres\taglib;

use APF\core\configuration\Configuration;
use APF\extensions\htmlheader\biz\CssContentNode;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;
use APF\extensions\htmlheader\biz\JsContentNode;
use APF\tools\form\multifileupload\biz\MultiFileUploadManager;
use APF\tools\form\taglib\AbstractFormControl;

/**
 * Taglib der ein Multifileupload Feld zur Verfügung stellt. Damit es mit allen Funktionen genutzt werden kann,
 * müssen die Notwendigen JS und CSS Dateien eingebunden werden.
 *
 * @param string $name - Name des Uploadfeldes
 * @param string $maxFileSize - Maximale Dateigröße (in Byte) (default: 10 MB)
 * @param string $MimeTypes - "application/pdf,image/gif" - hier können, kommagetrennt, alle erlaubten mimetypen angegeben werden.
 *
 * @return integer
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 */
class MultiFileUploadTag extends AbstractFormControl {

   /**
    * @var MultiFileUploadManager $manager
    */
   private $manager;

   /**
    * Language-dependent labels.
    *
    * @var Configuration $languageConfig
    */
   private $languageConfig;

   /**
    * Upload configuration.
    *
    * @var Configuration $MFUConfig
    */
   private $MFUConfig;

   private $formName;
   private $uploadFieldName;

   public function onParseTime() {

      $this->formName = $this->getForm()->getAttribute('name');
      $this->presetValue();

      // get Settings
      $this->uploadFieldName = $this->getAttribute('name');
      $maxFileSize = $this->getAttribute('max-file-size');
      $mimeTypes = $this->getAttribute('allowed-mime-types');

      $this->manager = &$this->getServiceObject(
            'APF\tools\form\multifileupload\biz\MultiFileUploadManager',
            ['formname' => $this->formName, 'name' => $this->uploadFieldName]
      );
      $this->manager->setSettings($maxFileSize, explode(',', $mimeTypes));
   }

   /**
    * Es wird sichergestellt, dass das Formular im multipart/form-data modus vorliegt und die id gesetzt ist.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function onAfterAppend() {
      $form = &$this->getForm();
      $form->setAttribute('enctype', 'multipart/form-data');

      // ensure form has an id (required for the java script stuff)
      $form->setAttribute('id', $this->formName);
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
      $this->manager->deleteOldFiles();

      // Zugriff auf Sprachconfig:
      $this->languageConfig = $this->getConfiguration('APF\tools\form\multifileupload', 'language.ini')->getSection($this->language);

      // Zugriff auf MultiFileUploadConfig
      $this->MFUConfig = $this->getConfiguration('APF\tools\form\multifileupload', 'multifileupload.ini')->getSection($this->uploadFieldName);

      // Zugriff auf HTML-Header-Manager
      /* @var $HHM HtmlHeaderManager */
      $HHM = $this->getServiceObject('APF\extensions\htmlheader\biz\HtmlHeaderManager');


      /*       * ********** Upload Button erstellen Anfang *********** */
      $return = $this->createUploadButton();
      /*       * ********** Upload Button erstellen Ende *********** */


      /*       * ********** Dialoge erstellen Anfang *********** */
      $return .= $this->createDialogFileDelete();
      $return .= $this->createDialogFileSize();
      $return .= $this->createDialogFileType();
      /*       * ********** Dialoge erstellen Ende *********** */


      /*       * ********** Upload Tabellen erstellen Anfang *********** */
      $return .= '<table id="' . $this->uploadFieldName . '_files_upload_table"></table>';
      $return .= '<table id="' . $this->uploadFieldName . '_files_download_table">' . $this->createFileTable($this->manager->getFiles()) . '</table>';
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

      return $return;
   }

   /**
    * Diese Funktion baut aus allen vom User auf den Server geladenen Dateien eine Downloadtabelle.
    * Dies ist vor allem für jene Leute wichtig, die kein JS aktiviert haben.
    *
    * @param array $files - Array mit allen Dateien kommend aus der biz.
    *
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
                  . '<td class="delete"><a href="' . $file['deletelink'] . '" target="_blank" onclick="return deletefile(\'' . $file['deletelink'] . '\',this)"><div class="ui-state-default ui-corner-all" title="' . $this->languageConfig->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $this->languageConfig->getValue('delete.label') . '</span></div></a></td>'
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
      $name = &$this->uploadFieldName;
      if (isset($_FILES[$name]) && $_FILES[$name]['name'] != '') {
         $addfile = $this->manager->addFile($_FILES[$name], false);
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
      return $this->manager->getFiles();
   }

   /**
    * Verschiebt die angegebene Datei an den angegeben Ort.
    *
    * @param string $uploadname - md5 Wert der raufgeladenen Datei
    * @param string $dir - Zielverzeichnis
    * @param string $name - Name unter dem die Datei gespeichert wird.
    *
    * @return bool <em>True</em> in case the file move has been successfully, <em>false</em> otherwise.
    *
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    * @version 1.1, 14.09.2012 (removed bug for method moveFile)<br>
    */
   public function moveFile($uploadname, $dir, $name) {
      return $this->manager->moveFile($uploadname, $dir, $name);
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
      return $this->manager->getUploadPath();
   }

   /**
    * Erstellt anhand eines übergebenen Array mimeTypes einen String mit erlaubten Dateiendungen
    *
    * @param array $MimeTypes
    *
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
    * Creates the allowed extension string for the JS code.
    *
    * @param array $mimeTypes The list of allowed MIME types.
    *
    * @return string The JS regexp string.
    *
    * @author dave
    * @version
    * Version 1.0, 14.07.2012<br />
    */
   private function createFileExtensionForJS(array $mimeTypes) {
      $return = '';
      foreach ($mimeTypes as $mimeType) {

         $extension = substr(strrchr($mimeType, '/'), 1);
         if ($return == '') {
            $return = '/\.(' . $extension . ')';
         } else {
            $return .= '|(' . $extension . ')';
         }
      }

      return $return . '$/i';
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
      . $this->languageConfig->getValue('delete.title') . '">'
      . $this->languageConfig->getValue('delete.message') . '</div>';
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
      . $this->languageConfig->getValue('filesize.title') . '">'
      . $this->languageConfig->getValue('filesize.message') . ' ' . $this->manager->getMaxFileSizeWithUnit() . '</div>';
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
      . $this->languageConfig->getValue('filetype.title') . '">'
      . $this->languageConfig->getValue('filetype.message') . ' ' . $this->createFileExtensionFromMimeType($this->manager->getMimeTypes()) . '</div>';
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
      return '<div id="' . $this->uploadFieldName . '_file_upload_container"><input type="file" name="' . $this->uploadFieldName . '" id="' . $this->uploadFieldName . '" multiple="multiple" /><button>'
      . $this->languageConfig->getValue('upload.button.label') . '</button><div class="uploadlabel">'
      . $this->languageConfig->getValue('upload.label') . '</div></div>';
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
      return '$(\'#' . $this->uploadFieldName . '_files_upload_table\'),';
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
      return '$(\'#' . $this->uploadFieldName . '_files_download_table\'),';
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
                  \'<td class="delete"><button class="ui-state-default ui-corner-all" title="' . $this->languageConfig->getValue('cancel.label') . '"><span class="ui-icon ui-icon-cancel">' . $this->languageConfig->getValue('cancel.label') . '<\/span><\/button><\/td>\' +
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
                  \'<td><a href="\' + file.deletelink + \'" target="_blank" onclick="return deletefile(\\\'\' + file.deletelink + \'\\\',this)" ><div class="ui-state-default ui-corner-all" title="' . $this->languageConfig->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $this->languageConfig->getValue('delete.label') . '<\/span><\/div><\/a><\/td>\'+
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
         if (files[index].size > ' . $this->manager->getMaxFileSize() . ') {
            $(".filesize_dialog").dialog({
               modal: true,
               height: 220,
               width: 300,
               buttons: {
                  "' . $this->languageConfig->getValue('filesize.ok') . '": function() {
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
         var regexp = ' . $this->createFileExtensionForJS($this->manager->getMimeTypes()) . ';
         if (!regexp.test(files[index].name)) {
            $(".filetype_dialog").dialog({
               modal: true,height:220,width:300,
               buttons: {
                  "' . $this->languageConfig->getValue('filetype.ok') . '": function() {
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
                     "' . $this->languageConfig->getValue('delete.ok') . '": function() {
                        $(this).dialog("close");
                        var jqxhr = $.ajax({
                           url: link
                        })
                        .success(function() {
                           $(elem).parent().parent().remove();
                        })
                     },
                     "' . $this->languageConfig->getValue('delete.no') . '" : function() {
                        $(this).dialog("close");
                        return false;
                     }
                  }
               });
               return false;
            }

            $(function () {
                $(\'#' . $this->formName . '\').fileUploadUI({
                   url: "' . $this->manager->link() . '",
                   fieldName: "' . $this->uploadFieldName . '",
                   uploadTable: ' . $this->createUploadTable() . '
                   downloadTable: ' . $this->createDownloadTable() . '
                   buildUploadRow: ' . $this->buildUploadRow() . '
                   buildDownloadRow: ' . $this->buildDownloadRow() . '
                   
                   dropZone: $("#' . $this->uploadFieldName . '_file_upload_container"),
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

   public function reset() {
      unset($_FILES[$this->uploadFieldName]);
      $files = $this->manager->getFiles();
      foreach ($files as $file) {
         $this->manager->deleteFile($file);
         $this->manager->deleteFileFromSession($file);
      }
   }

}
