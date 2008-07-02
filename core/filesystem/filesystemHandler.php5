<?php
   import('tools::string','stringAssistant');
   import('core::logging','Logger');


   /**
   *  @package core::filesystem
   *  @class filesystemHandler
   *
   *  Kapselt Zugriffsmethoden für das Filesystem.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 20.09.2004<br />
   *  Version 0.2, 01.12.2004<br />
   *  Version 0.3, 28.03.2007 (Pfade werden immer OHNE endenden "/" erwartet!)<br />
   */
   class filesystemHandler
   {

      /**
      *  @private
      *  Liste von bei Filesystem-Aktionen beeinflusste Dateien und Ordner.
      */
      var $__AffectedFiles = array();


      /**
      *  @private
      *  Arbeits-Verzeichnis.
      */
      var $__WorkDir;


      /**
      *  @private
      *  Instanz des Loggers.
      */
      var $__fsLog;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert das Arbeitsverzeichnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 20.09.2004<br />
      *  Version 0.2, 01.12.2004<br />
      *  Version 0.3, 17.08.2007 (Überprüfung des Arbeitsverzeichnisses eingeführt)<br />
      */
      function filesystemHandler($WorkDir = '.'){

         // Instanz des Loggers holen
         $this->__fsLog = &Singleton::getInstance('Logger');

         // Status-Cache leeren
         clearstatcache();

         // Arbeitsverzeichnis prüfen
         if(!is_dir($WorkDir)){
            trigger_error('[filesystemHandler::filesystemHandler()] Given directory "'.$WorkDir.'" is not a valid directory! Please check your working directory!',E_USER_ERROR);
            exit();
          // end if
         }
         else{
            $this->__WorkDir = $WorkDir;
          // end else
         }

         // Status-Cache leeren
         clearstatcache();

       // end function
      }


      /**
      *  @public
      *
      *  Wechselt das Arbeitsverzeichnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 20.09.2004<br />
      *  Version 0.2, 17.08.2007 (Überprüfung des Arbeitsverzeichnisses eingeführt)<br />
      */
      function changeWorkDir($WorkDir){

         // Status-Cache leeren
         clearstatcache();

         // Arbeitsverzeichnis prüfen
         if(!is_dir($WorkDir)){
            trigger_error('[filesystemHandler::changeWorkDir()] Can not change working directory to "'.$WorkDir.'", because this is not a valid directory!',E_USER_ERROR);
            exit();
          // end if
         }
         else{
            $this->__WorkDir = $WorkDir;
          // end else
         }

         // Status-Cache leeren
         clearstatcache();

       // end function
      }


      /**
      *  @public
      *
      *  Benennt eine Datei im angegebenen Verzeichnis um.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 23.11.2004<br />
      *  Version 0.2, 01.12.2004<br />
      *  Version 0.3, 07.12.2004<br />
      *  Version 0.4, 08.04.2005<br />
      *  Version 0.5, 21.01.2006<br />
      *  Version 0.6, 13.01.2006 (Bug beim Umbenennen behoben (falsches Verzeichnis wurden angegeben))<br />
      *  Version 0.7, 16.07.2006 (Altlast MyStringClass bereinigt)<br />
      */
      function renameFile($FileName,$NewFileName){

         // Sonderzeichen löschen
         $NewFileName = stringAssistant::replaceSpecialCharacters($NewFileName);


         // Prüfen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
         if(!filesystemHandler::isFileNameUnique($this->__WorkDir,$NewFileName)){

            // 5 Zeichen anhängen
            $Temp = $this->showFileAttributes($this->__WorkDir.'/'.$FileName);
            $NewFileName = $Temp['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$Temp['Extension'];

          // end if
         }

         // Umbenennen
         $result = rename($this->__WorkDir.'/'.$FileName,$this->__WorkDir.'/'.$NewFileName);

         // Logging
         $this->__fsLog->logEntry('filesystem','Datei '.$this->__WorkDir.'/'.$FileName.' zu '.$this->__WorkDir.'/'.$NewFileName.' umbenennen.');

         // Ergebniszeiger zurückgeben
         if($result){
            return $this->__WorkDir.'/'.$NewFileName;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die an der letzten Filesystem-Aktion beteiligten Dateien und Ordner zurück, und setzt<br />
      *  die interne Liste zurück.<br />
      *
      *  @return array $AffectedFiles; Array mit Dateien und Ordnern
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      */
      function getAffectedFiles(){
         $AffectedFiles = $this->__AffectedFiles;
         $this->__AffectedFiles = array();
         return $AffectedFiles;
       // end function
      }


      /**
      *  @public
      *
      *  Löscht alle Ordner und Datein unterhalb des angegeben Pfads.<br />
      *
      *  @param string $Folder; Absolute oder relative Pfadangabe
      *  @return bool $Status; true, falls Löschung erfolgreich, false, falls der angegebene Ordner nicht existiert
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      */
      function deleteFolderRecursive($Folder = ''){

         // Status-Cache leeren
         clearstatcache();

         // Prüfen, ob Verzeichnis existiert
         if(!is_dir($Folder)){
            return false;
          // end if
         }
         else{

            // Verzeichnis öffnen
            $DirHandle = opendir($Folder);

            // Dateien und Ordner rektursiv löschen
            while($DirContent = readdir($DirHandle)){

               if($DirContent != '.' && $DirContent != '..'){

                  // Prüfen, ob Inhalt ein Verzeichnis ist
                  if(is_dir($Folder.'/'.$DirContent)){

                     // Ordner im Unterverzeichnis rekursiv löschen
                     $this->deleteFolderRecursive($Folder.'/'.$DirContent);

                   // end if
                  }
                  else{

                     // Datei zur AffectedFiles-Liste hinzufügen
                     $this->__AffectedFiles[] = $Folder.'/'.$DirContent;

                     // Datei löschen
                     unlink($Folder.'/'.$DirContent);

                   // end else
                  }

                // end if
               }


               // Status-Cache leeren
               clearstatcache();

             // end while
            }

            // Verzeichnis schließen
            closedir($DirHandle);


            // Ordner zur AffectedFiles-Liste hinzufügen
            $this->__AffectedFiles[] = $Folder.'/';


            // Verzeichnis selbst löschen
            rmdir($Folder);

          // end else
         }

         // Status-Cache leeren
         clearstatcache();

         // True zurückgeben
         return true;

       // end function
      }


      /**
      *  @public
      *
      *  Löscht die übergebene Dateien.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.09.2004<br />
      *  Version 0.2, 23.11.2004<br />
      *  Version 0.3, 10.12.2004<br />
      *  Version 0.4, 21.01.2006<br />
      *  Version 0.5, 28.03.2007 (Code aufgeräumt)<br />
      */
      function deleteFiles($Files = array()){

         // Verzeichnis öffnen
         $Stream = opendir($this->__WorkDir);

         while($Datei = readdir($Stream)){

            if($Datei != '.' && $Datei != '..'){

               // Prüfen, ob aktuelle Datei gelöscht werden soll
               if(in_array($Datei,$Files)){

                  // Prüfen, ob "Datei" kein Verzeichnis ist
                  if(!is_dir($this->__WorkDir.'/'.$Datei)){

                     // Datei in die AffectedFiles-Liste schreiben
                     $this->__AffectedFiles[] = $this->__WorkDir.'/'.$Datei;

                     // Datei löschen
                     unlink($this->__WorkDir.'/'.$Datei);

                     // Logging
                     $this->__fsLog->logEntry('filesystem','Datei '.$Datei.' aus '.$this->__WorkDir.' löschen.');

                   // end if
                  }

                  // Status-Cache leeren
                  clearstatcache();

                // end if
               }

             // end if
            }

          // end while
         }

         // Verzeichnis schließen
         closedir($Stream);

       // end function
      }


      /**
      *  @public
      *
      *  Kopiert eine Quell-Datei nach Ziel-Datei. Falls im Ziel-Verzeichnis<br />
      *  eine Datei mit selbem Namen existiert wird die Datei umbenannt.<br />
      *  Rückgabewert ist die ZielDatei (Name und Pfad).<br />
      *  <br />
      *  <font style="color: red; font-weight; bold;">!!! EXPERIMENTELL !!!</font><br />
      *  <br />
      *
      *  @param string $SourceFile; Quell-Datei incl. Pfad
      *  @param string $TargetFile; gewünschte Ziel-Datei incl. Pfad
      *  @return string $TargetFile; Ziel-Datei incl. Pfad
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.09.2004<br />
      *  Version 0.2, 29.09.2004<br />
      *  Version 0.3, 10.12.2004<br />
      *  Version 0.4, 21.01.2006<br />
      *  Version 0.5, 16.07.2006 (Altlast "MyStringClass" bereinigt)<br />
      *  Version 0.6, 31.03.2007 (Refactoring)<br />
      */
      function copyFile($SourceFile,$TargetFile){

         // Pfade extrahieren
         $SourceFileInfo = $this->showFileAttributes($SourceFile);
         $TargetFileInfo = $this->showFileAttributes($TargetFile);


         // Dateinamen von Sonderzeichen befreien
         $TargetFileName = stringAssistant::replaceSpecialCharacters($TargetFileInfo['FileName']);


         // Prüfen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
         if(!filesystemHandler::isFileNameUnique($TargetFileInfo['Path'],$TargetFileName)){
            $TargetFileName = $TargetFileInfo['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$TargetFileInfo['Extension'];
          // end if
         }


         // Datei kopieren
         $result = copy($SourceFileInfo['Path'].'/'.$SourceFileInfo['FileName'],$TargetFileInfo['Path'].'/'.$TargetFileName);


         // Logging
         $this->__fsLog->logEntry('filesystem','Datei '.$TargetFileName.' von '.$SourceFileInfo['Path'].' nach '.$TargetFileInfo['Path'].' kopiert.');


         // ZielDatei mit Pfad zurückgeben
         return $TargetFileInfo['Path'].'/'.$TargetFileName;

       // end function
      }


      /**
      *  @public
      *
      *  Läd eine Datei in das angegebene Ziel-Verzeichnis hoch. Es wird überprüft , ob<br />
      *  sich bereits eine gleichnamige Datei dort befindet und benennt dieses ggf. um.<br />
      *  Es werden darüber hinaus max. zulässige Dateigrößen und zulässige MIME-Typen<br />
      *  überprüft. Bei unzulässigen Eingaben wird eine Fehler-Meldung ausgegeben.<br />
      *  Die Funktion gibt absoluten Pfad des Bildes zurück.<br />
      *  <br />
      *  ERRORCODES:<br />
      *  - error: Datei konnte nicht gefunden werden.<br />
      *  - error_mime_size: Datei ist zu groß oder besitzt nicht den erwartetet MIME-Typen.<br />
      *
      *  @param string $dir; Zielverzeichnis
      *  @param string $temp_file; Temporäre Datei mit Pfadangabe
      *  @param string $file_name; Name der Zieldatei
      *  @param string $file_size; Tatsächliche Größe der Datei
      *  @param string $file_max_size; Maximal zulässige Dateigröße in Bytes
      *  @param string $file_type; MIME-Type der Datei
      *  @param array $allowed_mime_types; Array mit den hier zulässigen MIME-Typen
      *  @return string $FileName | ERRORCODE; Den absoluten Pfad zur Datei oder einen ERRORCODE
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 20.04.2003<br />
      *  Version 0.2, 29.09.2003<br />
      *  Version 0.3, 30.09.2003<br />
      *  Version 0.4, 29.09.2004<br />
      *  Version 0.5, 01.12.2004<br />
      *  Version 0.6, 02.12.2004<br />
      *  Version 0.7, 16.07.2005<br />
      *  Version 0.8, 21.01.2006 (Text entfernt)<br />
      *  Version 0.9, 13.01.2006 (Bug entfernt, mit dem das falsche Verzeichnis und der falsche Dateiname adressiert wurde)<br />
      *  Version 1.0, 31.03.2007 (Refactoring nach Umstellung des WorkDir-Handlings)<br />
      */
      function uploadFile($dir,$temp_file,$file_name,$file_size,$file_max_size,$file_type,$allowed_mime_types){

         // Dateieigenschaften für Vorgaben (Größe- und MIME-Typen-Vorgaben) geeignet?
         if(in_array($file_type,$allowed_mime_types) && ($file_size < $file_max_size)){

            // Dateinamen von Sonderzeichen befreien
            $file_name = stringAssistant::replaceSpecialCharacters($file_name);

            // Falls Datei mit FORM gesendet wurde, dann uploaden
            if(is_uploaded_file($temp_file)){

               // Prüfen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
               if(!filesystemHandler::isFileNameUnique($dir,$file_name)){

                  // Datei umbenennen
                  $FileInfo = $this->showFileAttributes($this->__WorkDir.'/'.$file_name);
                  $file_name = $FileInfo['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$FileInfo['Extension'];

                // end if
               }

               // Link für Ausgabe:
               $link = $dir.'/'.$file_name;

               // Bild hochladen:
               $result = move_uploaded_file($temp_file,$dir.'/'.$file_name);
               $this->__fsLog->logEntry('filesystem','Datei '.$file_name.' wurde nach '.$dir.' hochgeladen.');

             // end if
            }
            else{
               $Meldung = 'Die Datei konnte nicht hochgeladen werden, da keine Benutzereingabe vorhanden war.';
               trigger_error($Meldung);
               $this->__fsLog->logEntry('filesystem',$Meldung);
             // end else
            }


            // Prüfen, ob kopieren erfolgreich war
            if($result == true){
               return $link;
             // end if
            }
            else{
               return 'error';
             // end else
            }

          // end if
         }
         else{
            $Fehler = 'Die Datei '.$file_name.' (Tats. Größe: '.$file_size.' kB <-> Max. Größe: '.$file_max_size.' kB) ist entweder zu groß oder hat keinen zulässigen Dateityp (Tats. Typ: '.$file_type.' <-> Erl. Typen: '.implode(',',$allowed_mime_types).')!';
            $this->__fsLog->logEntry('filesystem',$Fehler);
            return 'error_mime_size';
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Generiert ein Datei-Namen-Suffix für den Upload und das Umbenennen von Dateien.<br />
      *
      *  @return string $FileSuffix; Eindeutiges Suffix
      *
      *  @author Christian Schäfer
      *  @Version
      *  Version 0.1, 31.03.2007<br />
      */
      function __generateAlternativeFileSuffix(){
         return xmlParser::generateUniqID();
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt die Dateigröße der einer übergebenen Datei zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 18.08.2006<br />
      *  Version 0.2, 30.03.2007 (In "filesystemHandler" eingebunden)<br />
      */
      static function showFileSize($File){

         if(!file_exists($File)){
            trigger_error('[filesystemHandler::showFileSize()] File "'.$File.'" does not exist!',E_USER_NOTICE);
            return (string)'0 B';
          // end if
         }

         // Dateigröße bestimmen
         $size = filesize($File);

         // Status-Cache löschen
         clearstatcache();

         // Größe zusammensetzen
         switch(true){
            case ($size > 1000000000000):
               $size /= 1000000000000;
               $suffix = 'TB';
               break;
            case ($size > 1000000000):
               $size /= 1000000000;
               $suffix = 'GB';
               break;
            case ($size > 1000000):
               $size /= 1000000;
               $suffix = 'MB';
               break;
            case ($size > 1000):
               $size /= 1000;
               $suffix = 'KB';
               break;
            default:
               $suffix = 'B';
           // end switch
          }

          // Ergebnis gerundet zurückgeben
          return round($size, 2).' '.$suffix;

       // end function
      }


      /**
      *  @public
      *
      *  Liest das bei der Initialisierung angegebene Verzeichnis aus und gibt in einem Array die
      *  Namen der Dateien zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.12.2004<br />
      */
      function showDirContent(){

         $stream = opendir($this->__WorkDir);
         $Content = array();

         while($file = readdir($stream)){

            if($file != '.' && $file != '..'){

               $Content[] = $file;

             // end if
            }

          // end while
         }

         // Verzeichnis schließen
         closedir($stream);

         // Ergebnis zurückgeben
         return $Content;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Größe des Verzeichnises aus, mit dem die Klasse initialisiert wurde.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2005<br />
      *  Version 0.2, 22.05.2005 (Erweiterung auf Größen von Unterverz.)<br />
      *  Version 0.3, 28.03.2007 (Code-Redesign)<br />
      */
      function showDirSize(){

         $Files = $this->showDirContent();

         $Size = (int) 0;

         for($i = 0; $i < count($Files); $i++){

            if(is_dir($this->__WorkDir.'/'.$Files[$i])){

               $RootFolder = $this->__WorkDir;
               $this->changeWorkDir($this->__WorkDir.'/'.$Files[$i]);
               $DirSize = $this->showDirSize();
               $Size = $Size + $DirSize['Byte'];
               $this->changeWorkDir($RootFolder);

             // end if
            }
            else{
               $Size = $Size + filesize($this->__WorkDir.'/'.$Files[$i]);
             // end else
            }

            // Statecache leeren (sonst fehlerhafte Verzeichnis- und Dateigrößen!)
            clearstatcache();

          // end for
         }

         // Größe zurückgeben
         return array('Byte' => $Size,
                      'kByte' => str_replace('.',',',$Size/1000),
                      'MByte' => str_replace('.',',',$Size/1000000)
                      );

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Anzahl der Dateien des Verzeichnises aus.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2005<br />
      */
      function showDirContentCount(){
         $Files = $this->showDirContent();
         return count($Files);
       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Zeigt die Attribute 'Extension', 'FileName', 'Path' und 'FileBaseName'.<br />
      *  Falls die Datei eine auf dem Filesystem befindliche Datei ist werden auch<br />
      *  'Size', 'ModifyingDate', 'DownloadTime',... ausgegeben<br />
      *
      *  @param string $File; Datei incl. Pfad
      *  @return array $FileAttributes; Array mit Datei-Attributen
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.04.2005<br />
      *  Version 0.2, 13.02.2006<br />
      *  Version 0.3, 31.03.2007 (Refactoring erledigt)<br />
      *  Version 0.4, 17.08.2007 (Attribut "ModifyingTime" hinzugefügt)<br />
      */
      static function showFileAttributes($File){

         // Status-Cache löschen
         clearstatcache();

         // Informationen über die Datei holen
         $PI = pathinfo($File);

         // Bereitstehende Infos für Rückgabe aufgereiten
         $attributes['Extension'] = $PI['extension'];
         $attributes['FileName'] = $PI['basename'];
         $attributes['Path'] = $PI['dirname'];
         $attributes['FileBaseName'] = str_replace('.'.$attributes['Extension'],'',$attributes['FileName']);

         // Falls Datei existiert, weiter Infos sammeln
         if(file_exists($File)){

            $attributes['ModifyingDate'] = date('d.m.Y',filemtime($File));
            $attributes['ModifyingTime'] = date('H:i:s',filemtime($File));
            $attributes['Size']['kB'] = round((intval(filesize($File))/1024),2);
            $attributes['Size']['MB'] = round((intval($attributes['Size']['kB'])/1024),2);
            $attributes['DownloadTime'] = round((0.2798 * $attributes['Size']['kB']),2);

          // end if
         }

         // Status-Cache löschen
         clearstatcache();

         // Infos zurückgeben
         return $attributes;

       // end function
      }


      /**
      *  @public
      *
      *  Löscht das momentan angegebene Verzeichnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.05.2005<br />
      *  Version 0.2, 15.06.2006 ($Return wird nun vorbelegt)<br />
      */
      function deleteWorkDir(){

         // Falls Verzeichnis existiert, Verzeichnis löschen
         if(is_dir($this->__WorkDir)){
            $this->changeWorkDir('.');
            return rmdir($this->__WorkDir);
          // end if
         }

         // Falls nicht, oder Verzeichnis nicht gelöscht werden kann false zurückgeben.
         return false;

       // end function
      }


      /**
      *  @private
      *  @static
      *
      *  Überprüft, ob ein Dateinamen im aktuellen Arbeitsverzeichnis<br />
      *  eindeutig ist. Liefert 'false', falls der Dateinamen bereits<br />
      *  vergeben ist und 'true', falls der Dateinamen eindeutig ist.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2006<br />
      *  Version 0.2, 13.02.2006 (Rechtschreibfehler in Variablen $Stream und $File berichtigt)<br />
      */
      static function isFileNameUnique($Verzeichnis,$DateiName){

         // Rückgabe-Wert vorbelegen
         $isUnique = true;

         // Verzeichnis öffnen
         $Dir = @opendir($Verzeichnis);

         // Verzeichnis nach gleichen Dateien durchsuchen
         while($File = @readdir($Dir)) {

            if($File != '.' && $File != '..') {

               if($File == $DateiName){

                  $isUnique = false;

                // end if
               }

             // end if
            }

          // end while
         }

         // Verzeichnis öffnen
         @closedir($Dir);

         // Ergebnis zurückgeben
         return $isUnique;

       // end function
      }

    // end class
   }
?>