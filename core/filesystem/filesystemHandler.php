<?php
   import('tools::string','stringAssistant');
   import('core::logging','Logger');


   /**
   *  @package core::filesystem
   *  @class filesystemHandler
   *
   *  Kapselt Zugriffsmethoden f�r das Filesystem.<br />
   *
   *  @author Christian Sch�fer
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 20.09.2004<br />
      *  Version 0.2, 01.12.2004<br />
      *  Version 0.3, 17.08.2007 (�berpr�fung des Arbeitsverzeichnisses eingef�hrt)<br />
      */
      function filesystemHandler($WorkDir = '.'){

         // Instanz des Loggers holen
         $this->__fsLog = &Singleton::getInstance('Logger');

         // Status-Cache leeren
         clearstatcache();

         // Arbeitsverzeichnis pr�fen
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 20.09.2004<br />
      *  Version 0.2, 17.08.2007 (�berpr�fung des Arbeitsverzeichnisses eingef�hrt)<br />
      */
      function changeWorkDir($WorkDir){

         // Status-Cache leeren
         clearstatcache();

         // Arbeitsverzeichnis pr�fen
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
      *  @author Christian Sch�fer
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

         // Sonderzeichen l�schen
         $NewFileName = stringAssistant::replaceSpecialCharacters($NewFileName);


         // Pr�fen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
         if(!filesystemHandler::isFileNameUnique($this->__WorkDir,$NewFileName)){

            // 5 Zeichen anh�ngen
            $Temp = $this->showFileAttributes($this->__WorkDir.'/'.$FileName);
            $NewFileName = $Temp['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$Temp['Extension'];

          // end if
         }

         // Umbenennen
         $result = rename($this->__WorkDir.'/'.$FileName,$this->__WorkDir.'/'.$NewFileName);

         // Logging
         $this->__fsLog->logEntry('filesystem','Datei '.$this->__WorkDir.'/'.$FileName.' zu '.$this->__WorkDir.'/'.$NewFileName.' umbenennen.');

         // Ergebniszeiger zur�ckgeben
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
      *  Gibt die an der letzten Filesystem-Aktion beteiligten Dateien und Ordner zur�ck, und setzt<br />
      *  die interne Liste zur�ck.<br />
      *
      *  @return array $AffectedFiles; Array mit Dateien und Ordnern
      *
      *  @author Christian Sch�fer
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
      *  L�scht alle Ordner und Datein unterhalb des angegeben Pfads.<br />
      *
      *  @param string $Folder; Absolute oder relative Pfadangabe
      *  @return bool $Status; true, falls L�schung erfolgreich, false, falls der angegebene Ordner nicht existiert
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      */
      function deleteFolderRecursive($Folder = ''){

         // Status-Cache leeren
         clearstatcache();

         // Pr�fen, ob Verzeichnis existiert
         if(!is_dir($Folder)){
            return false;
          // end if
         }
         else{

            // Verzeichnis �ffnen
            $DirHandle = opendir($Folder);

            // Dateien und Ordner rektursiv l�schen
            while($DirContent = readdir($DirHandle)){

               if($DirContent != '.' && $DirContent != '..'){

                  // Pr�fen, ob Inhalt ein Verzeichnis ist
                  if(is_dir($Folder.'/'.$DirContent)){

                     // Ordner im Unterverzeichnis rekursiv l�schen
                     $this->deleteFolderRecursive($Folder.'/'.$DirContent);

                   // end if
                  }
                  else{

                     // Datei zur AffectedFiles-Liste hinzuf�gen
                     $this->__AffectedFiles[] = $Folder.'/'.$DirContent;

                     // Datei l�schen
                     unlink($Folder.'/'.$DirContent);

                   // end else
                  }

                // end if
               }


               // Status-Cache leeren
               clearstatcache();

             // end while
            }

            // Verzeichnis schlie�en
            closedir($DirHandle);


            // Ordner zur AffectedFiles-Liste hinzuf�gen
            $this->__AffectedFiles[] = $Folder.'/';


            // Verzeichnis selbst l�schen
            rmdir($Folder);

          // end else
         }

         // Status-Cache leeren
         clearstatcache();

         // True zur�ckgeben
         return true;

       // end function
      }


      /**
      *  @public
      *
      *  L�scht die �bergebene Dateien.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 30.09.2004<br />
      *  Version 0.2, 23.11.2004<br />
      *  Version 0.3, 10.12.2004<br />
      *  Version 0.4, 21.01.2006<br />
      *  Version 0.5, 28.03.2007 (Code aufger�umt)<br />
      */
      function deleteFiles($Files = array()){

         // Verzeichnis �ffnen
         $Stream = opendir($this->__WorkDir);

         while($Datei = readdir($Stream)){

            if($Datei != '.' && $Datei != '..'){

               // Pr�fen, ob aktuelle Datei gel�scht werden soll
               if(in_array($Datei,$Files)){

                  // Pr�fen, ob "Datei" kein Verzeichnis ist
                  if(!is_dir($this->__WorkDir.'/'.$Datei)){

                     // Datei in die AffectedFiles-Liste schreiben
                     $this->__AffectedFiles[] = $this->__WorkDir.'/'.$Datei;

                     // Datei l�schen
                     unlink($this->__WorkDir.'/'.$Datei);

                     // Logging
                     $this->__fsLog->logEntry('filesystem','Datei '.$Datei.' aus '.$this->__WorkDir.' l�schen.');

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

         // Verzeichnis schlie�en
         closedir($Stream);

       // end function
      }


      /**
      *  @public
      *
      *  Kopiert eine Quell-Datei nach Ziel-Datei. Falls im Ziel-Verzeichnis<br />
      *  eine Datei mit selbem Namen existiert wird die Datei umbenannt.<br />
      *  R�ckgabewert ist die ZielDatei (Name und Pfad).<br />
      *  <br />
      *  <font style="color: red; font-weight; bold;">!!! EXPERIMENTELL !!!</font><br />
      *  <br />
      *
      *  @param string $SourceFile; Quell-Datei incl. Pfad
      *  @param string $TargetFile; gew�nschte Ziel-Datei incl. Pfad
      *  @return string $TargetFile; Ziel-Datei incl. Pfad
      *
      *  @author Christian Sch�fer
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


         // Pr�fen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
         if(!filesystemHandler::isFileNameUnique($TargetFileInfo['Path'],$TargetFileName)){
            $TargetFileName = $TargetFileInfo['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$TargetFileInfo['Extension'];
          // end if
         }


         // Datei kopieren
         $result = copy($SourceFileInfo['Path'].'/'.$SourceFileInfo['FileName'],$TargetFileInfo['Path'].'/'.$TargetFileName);


         // Logging
         $this->__fsLog->logEntry('filesystem','Datei '.$TargetFileName.' von '.$SourceFileInfo['Path'].' nach '.$TargetFileInfo['Path'].' kopiert.');


         // ZielDatei mit Pfad zur�ckgeben
         return $TargetFileInfo['Path'].'/'.$TargetFileName;

       // end function
      }


      /**
      *  @public
      *
      *  L�d eine Datei in das angegebene Ziel-Verzeichnis hoch. Es wird �berpr�ft , ob<br />
      *  sich bereits eine gleichnamige Datei dort befindet und benennt dieses ggf. um.<br />
      *  Es werden dar�ber hinaus max. zul�ssige Dateigr��en und zul�ssige MIME-Typen<br />
      *  �berpr�ft. Bei unzul�ssigen Eingaben wird eine Fehler-Meldung ausgegeben.<br />
      *  Die Funktion gibt absoluten Pfad des Bildes zur�ck.<br />
      *  <br />
      *  ERRORCODES:<br />
      *  - error: Datei konnte nicht gefunden werden.<br />
      *  - error_mime_size: Datei ist zu gro� oder besitzt nicht den erwartetet MIME-Typen.<br />
      *
      *  @param string $dir; Zielverzeichnis
      *  @param string $temp_file; Tempor�re Datei mit Pfadangabe
      *  @param string $file_name; Name der Zieldatei
      *  @param string $file_size; Tats�chliche Gr��e der Datei
      *  @param string $file_max_size; Maximal zul�ssige Dateigr��e in Bytes
      *  @param string $file_type; MIME-Type der Datei
      *  @param array $allowed_mime_types; Array mit den hier zul�ssigen MIME-Typen
      *  @return string $FileName | ERRORCODE; Den absoluten Pfad zur Datei oder einen ERRORCODE
      *
      *  @author Christian Sch�fer
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

         // Dateieigenschaften f�r Vorgaben (Gr��e- und MIME-Typen-Vorgaben) geeignet?
         if(in_array($file_type,$allowed_mime_types) && ($file_size < $file_max_size)){

            // Dateinamen von Sonderzeichen befreien
            $file_name = stringAssistant::replaceSpecialCharacters($file_name);

            // Falls Datei mit FORM gesendet wurde, dann uploaden
            if(is_uploaded_file($temp_file)){

               // Pr�fen, ob Datei im aktuellen Arbeitsverzeichnis eindeutig ist
               if(!filesystemHandler::isFileNameUnique($dir,$file_name)){

                  // Datei umbenennen
                  $FileInfo = $this->showFileAttributes($this->__WorkDir.'/'.$file_name);
                  $file_name = $FileInfo['FileBaseName'].'_'.($this->__generateAlternativeFileSuffix()).'.'.$FileInfo['Extension'];

                // end if
               }

               // Link f�r Ausgabe:
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


            // Pr�fen, ob kopieren erfolgreich war
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
            $Fehler = 'Die Datei '.$file_name.' (Tats. Gr��e: '.$file_size.' kB <-> Max. Gr��e: '.$file_max_size.' kB) ist entweder zu gro� oder hat keinen zul�ssigen Dateityp (Tats. Typ: '.$file_type.' <-> Erl. Typen: '.implode(',',$allowed_mime_types).')!';
            $this->__fsLog->logEntry('filesystem',$Fehler);
            return 'error_mime_size';
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Generiert ein Datei-Namen-Suffix f�r den Upload und das Umbenennen von Dateien.<br />
      *
      *  @return string $FileSuffix; Eindeutiges Suffix
      *
      *  @author Christian Sch�fer
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
      *  Gibt die Dateigr��e der einer �bergebenen Datei zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 18.08.2006<br />
      *  Version 0.2, 30.03.2007 (In "filesystemHandler" eingebunden)<br />
      */
      function showFileSize($File){

         if(!file_exists($File)){
            trigger_error('[filesystemHandler::showFileSize()] File "'.$File.'" does not exist!',E_USER_NOTICE);
            return (string)'0 B';
          // end if
         }

         // Dateigr��e bestimmen
         $size = filesize($File);

         // Status-Cache l�schen
         clearstatcache();

         // Gr��e zusammensetzen
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

          // Ergebnis gerundet zur�ckgeben
          return round($size, 2).' '.$suffix;

       // end function
      }


      /**
      *  @public
      *
      *  Liest das bei der Initialisierung angegebene Verzeichnis aus und gibt in einem Array die
      *  Namen der Dateien zur�ck.<br />
      *
      *  @author Christian Sch�fer
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

         // Verzeichnis schlie�en
         closedir($stream);

         // Ergebnis zur�ckgeben
         return $Content;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Gr��e des Verzeichnises aus, mit dem die Klasse initialisiert wurde.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2005<br />
      *  Version 0.2, 22.05.2005 (Erweiterung auf Gr��en von Unterverz.)<br />
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

            // Statecache leeren (sonst fehlerhafte Verzeichnis- und Dateigr��en!)
            clearstatcache();

          // end for
         }

         // Gr��e zur�ckgeben
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
      *  @author Christian Sch�fer
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.04.2005<br />
      *  Version 0.2, 13.02.2006<br />
      *  Version 0.3, 31.03.2007 (Refactoring erledigt)<br />
      *  Version 0.4, 17.08.2007 (Attribut "ModifyingTime" hinzugef�gt)<br />
      */
      function showFileAttributes($File){

         // Status-Cache l�schen
         clearstatcache();

         // Informationen �ber die Datei holen
         $PI = pathinfo($File);

         // Bereitstehende Infos f�r R�ckgabe aufgereiten
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

         // Status-Cache l�schen
         clearstatcache();

         // Infos zur�ckgeben
         return $attributes;

       // end function
      }


      /**
      *  @public
      *
      *  L�scht das momentan angegebene Verzeichnis.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.05.2005<br />
      *  Version 0.2, 15.06.2006 ($Return wird nun vorbelegt)<br />
      */
      function deleteWorkDir(){

         // Falls Verzeichnis existiert, Verzeichnis l�schen
         if(is_dir($this->__WorkDir)){
            $this->changeWorkDir('.');
            return rmdir($this->__WorkDir);
          // end if
         }

         // Falls nicht, oder Verzeichnis nicht gel�scht werden kann false zur�ckgeben.
         return false;

       // end function
      }


      /**
      *  @private
      *  @static
      *
      *  �berpr�ft, ob ein Dateinamen im aktuellen Arbeitsverzeichnis<br />
      *  eindeutig ist. Liefert 'false', falls der Dateinamen bereits<br />
      *  vergeben ist und 'true', falls der Dateinamen eindeutig ist.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.01.2006<br />
      *  Version 0.2, 13.02.2006 (Rechtschreibfehler in Variablen $Stream und $File berichtigt)<br />
      */
      function isFileNameUnique($Verzeichnis,$DateiName){

         // R�ckgabe-Wert vorbelegen
         $isUnique = true;

         // Verzeichnis �ffnen
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

         // Verzeichnis �ffnen
         @closedir($Dir);

         // Ergebnis zur�ckgeben
         return $isUnique;

       // end function
      }

    // end class
   }
?>