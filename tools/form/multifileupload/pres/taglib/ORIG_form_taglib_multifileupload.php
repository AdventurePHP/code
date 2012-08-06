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
class form_taglib_multifileupload extends form_control {

   /**
    * @var multifileupload
    */
   private $MultifileuploadManager = null; //Multifileupload biz referenz
   private $config; // config referenz
   private $formname; //Formularname
   private $name;

   public function onParseTime() {

      $this->formname = $this->getParentObject()->getAttribute('name');
      $this->__presetValue();
      // get Settings
      $this->name = $this->getAttribute('name');
      $maxFileSize = $this->getAttribute('maxFileSize');
      $MimeTypes = $this->getAttribute('MimeTypes');
      // Einstellungen speichern.
      $this->MultifileuploadManager = &$this->getAndInitServiceObject('dev::multifileupload::biz', 'MultiFileUploadManager', array('formname' => $this->formname, 'name' => $this->name), 'SINGLETON');
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
    * @return string
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function transform() {

      //Zugriff auf Config:
      $config = $this->getConfiguration('dev::multifileupload', 'language.ini');
      $this->config = $config->getSection($this->__Language);
      $config = &$this->config;

      // Zugriff auf headermanager
      $HHM = $this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');

      $name = &$this->name;
      $formname = &$this->formname;

      $ajaxurl = $this->MultifileuploadManager->link();
      $files = $this->MultifileuploadManager->getFiles();
      $mimeTypes = $this->MultifileuploadManager->getMimeTypes();
      $MaxfileSize = $this->MultifileuploadManager->getMaxFileSize();
      $MaxfileSizeWithUnit = $this->MultifileuploadManager->getMaxFileSizeWithUnit();

      //Html Code für Tag erzeugen
      $htmlCode = '<div id="' . $name . '_file_upload_container"><input type="file" name="' . $name . '" id="' . $name . '" multiple>
         <button>' . $config->getValue('upload.button.label') . '</button>
         <div class="uploadlabel">' . $config->getValue('upload.label') . '</div>
         </div>';

      // Uploadtabellen erzeugen
      $uploadtable = '<table id="' . $name . '_files_upload_table"></table>';
      $downloadtable = '<table id="' . $name . '_files_download_table">' . $this->createFileTable($files) . '</table>';

      $JSCode = '';


      $uploadtableJS = 'uploadTable: $(\'#' . $name . '_files_upload_table\'),';
      $builduploadtableJS = 'buildUploadRow: function (files, index) {
return $(\'<tr><td class="file_upload_preview"><\/td><td>\' + files[index].name +
\'<\/td><td class="file_upload_progress"><div><\/div><\/td>\'+
\'<td class="value">0 %<\/td>\'+
\'<\/tr>\');
                        },';
//TODO: Cancel Button einbauen und testen
//        \'<td class="delete"><button class="ui-state-default ui-corner-all" title="Cancel">\'+
//\'<span class="ui-icon ui-icon-cancel">Cancel<\/span>\'+
//\'<\/button>\'+
//\'<\/td>

      $downloadtableJS = 'downloadTable: $(\'#' . $name . '_files_download_table\'),';
      $builddownloadtableJS = 'buildDownloadRow: function (file) {
var bild="";
var regexp=new RegExp(/^image\/(gif|jpeg|png|jpg)$/);
if (regexp.test(file.type)) {
            bild=\'<img src=\"\'+file.filelink+\'\" alt=\"\'+file.name+\'\" \/>\';
            }
            return $(\'<tr><td class="file_upload_preview">\'+bild+\' <\/td><td><a href=\"\'+file.filelink+\'\" target="_blank" >\' + file.name + \'<\/a><\/td>\'+
            \'<td>\'+file.filesize+\'<\/td>\'+
            \'<td><a href=\"\'+file.deletelink+\'\" target="_blank" onclick="return deletefile(\\\'\'+file.deletelink+\'\\\',this)" ><div class="ui-state-default ui-corner-all" title="' . $config->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $config->getValue('delete.label') . '<\/span><\/div><\/a ><\/td>\'+
\'<\/tr>\');
        },';

      // Erlaube MimeTypen entsprechend aufbereiten damti sie im JS verwendet werden können:
      $endungen = '';
      // Die erlaubten MimeTypen für den Fehler Dialog aufbereiten.
      $dateitypen = '';
      foreach ($mimeTypes as $mimeType) {

         $endung = substr(strrchr($mimeType, "/"), 1);

         if ($endungen == '') {
            $endungen = '/\.(' . $endung . ')';
            $dateitypen = $endung;
         } else {
            $endungen.='|(' . $endung . ')';
            $dateitypen.=', ' . $endung;
         }
      }
      $endungen.='$/i';

      // Mime Code erzeugen
      $mime = '
var regexp = ' . $endungen . ';
if (!regexp.test(files[index].name)) {
    $( ".filetype_dialog" ).dialog({
    modal: true,height:220,width:300,
    buttons: {    "' . $config->getValue('filetype.ok') . '": function() { $( this ).dialog( "close" ); }}
    });
    handler.uploadRow.remove();
    return;
}';

      // Dateigrößen JS Code erzeugen
      $filesize = '
if (files[index].size > ' . $MaxfileSize . ') {
$( ".filesize_dialog" ).dialog({
modal: true,height:220,width:300,
buttons: {    "' . $config->getValue('filesize.ok') . '": function() { $( this ).dialog( "close" ); }}
});
handler.uploadRow.remove();
return;}';


// Auszugebenden JS Code erzeugen
      $JSCode.= '<script>
$(".uploadlabel").css("display", "block");

function deletefile(link, elem){

$( ".confirm_delete" ).dialog( "destroy" );
$( ".confirm_delete" ).dialog({
modal: true,height:240,width:400,
buttons: {
    "' . $config->getValue('delete.ok') . '": function() {
    $( this ).dialog( "close" );
    var jqxhr = $.ajax({ url: link })
    .success(function() { $(elem).parent().parent().remove(); })
    },
   "' . $config->getValue('delete.no') . '" : function() {
    $( this ).dialog( "close" );
    return false;
    }
}
});
return false;}

$(function () {
    $(\'#' . $formname . '\').fileUploadUI({
    url: "' . $ajaxurl . '",
    fieldName: "' . $name . '",
    ' . $uploadtableJS . "\n"
              . $downloadtableJS . "\n"
              . $builduploadtableJS . "\n"
              . $builddownloadtableJS . '
    dropZone: $("#' . $name . '_file_upload_container"),
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
    handler.uploadRow.find(".value").text(value+"%");
    handler.progressbar.progressbar(\'value\',value);
}
},
beforeSend : function (event, files, index, xhr, handler, callBack) {
' . $filesize . $mime . '
callBack();
}});
});</script>';

      // Dialoge definieren
      $deleteDialog = '<div class="confirm_delete ui-dialog-content ui-widget-content dialog_confirm_delete"  title="' . $config->getValue('delete.title') . '" >' . $config->getValue('delete.message') . '</div>';
      $filesizeDialog = '<div class="filesize_dialog ui-dialog-content ui-widget-content"  title="' . $config->getValue('filesize.title') . '" >' . $config->getValue('filesize.message') . $MaxfileSizeWithUnit . '</div>';
      $filetypeDialog = '<div class="filetype_dialog ui-dialog-content ui-widget-content"  title="' . $config->getValue('filetype.title') . '" >' . $config->getValue('filetype.message') . $dateitypen . '</div>';
      // Alles zusammenbauen und ausgeben.
      return $deleteDialog . $filesizeDialog . $filetypeDialog . $htmlCode . $JSCode . $downloadtable . $uploadtable;
   }

   /**
    * Diese Funktion baut aus allen vom User auf den Server geladenen Dateien eine Downloadtabelle.
    * Dies ist vor allem für jene Leute wichtig, die kein JS aktiviert haben.
    *
    * @param array $files - Array mit allen Dateien kommend aus der biz.
    * @return string
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   private function createFileTable($files) {
      $buffer = '';
      $config = $this->config;
      if (count($files) > 0) {

         $uploadPath = $this->MultifileuploadManager->getUploadPath();
         foreach ($files as $file) {
            $image = '';
            if (strpos($file['type'], 'image') !== false) {
               $image = '<img src="' . $file['filelink'] . '" alt="' . $file['name'] . '" /> ';
            }

            $buffer.='<tr><td class="file_upload_preview">' . $image . '</td><td><a href="' . $file['filelink'] . '" target="_blank">' . $file['name'] . '</a></td>' .
                    '<td>' . $file['filesize'] . '</td>' .
                    '<td class="delete"><a href="' . $file['deletelink'] . '" target="_blank" onclick="return deletefile(\'' . $file['deletelink'] . '\',this)"><div class="ui-state-default ui-corner-all" title="' . $config->getValue('delete.label') . '"><span class="ui-icon ui-icon-trash">' . $config->getValue('delete.label') . '</span></div> </a></td>' .
                    '</tr>';
         }
      }
      return $buffer;
   }

   /**
    * Läd die Dateien hinauf, wenn das Formular ohne JS ausgeführt wurde.
    *
    * @return boolean erfolg - Falls es möglich war dateien hinzuzufügen liefert die funktion true, sonst false
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
    * @author Werner Liemberger <wpublicmail@gmail.com>
    * @version 1.0, 14.3.2011<br>
    */
   public function moveFile($uploadname, $dir, $name) {
      return $this->MultifileuploadManager->moveFiles($uploadname, $dir, $name);
   }

}

?>