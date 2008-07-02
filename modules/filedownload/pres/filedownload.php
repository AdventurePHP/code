<?php
   /**
   *  filedownload.php
   *  Repräsentiert die Ausgabe-Datei für Datei-Downloads.
   *
   *  Christian Schäfer
   *  Version 0.1, 12.02.2006
   */
   require_once(APPS__PATH.'/core/applicationmanager/ApplicationManager.php');


   import('tools::variablen','variablenHandler');
   import('modules::filedownload::biz','fileDownloadManager');


   $_LOCALS = variablenHandler::registerLocal(array('Datei','Pfad' => 'MEDIA_PATH'));


   $iRM = new fileDownloadManager($_LOCALS['Pfad']);
   $Datei = $iRM->erzeugeDateiPfad($_LOCALS['Datei']);

   if(strlen($Datei) > 0){

      header('Content-type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.basename($Datei));
      @readfile($Datei);

    // end if
   }
   else{
?>
<html>
<head>
</head>
<body style="margin: 10px;">
<div style="width: 100%; height: 100%; text-align: center;">
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <br />
  <font style="font-family: Arial, Helvetica, sans-serif; font-size: 14px;">
    <font style="color: red; font-weight: bold;">[ Fehler ]</font> Sie haben versucht eine gesperrte Datei zu öffnen!
  </font>
</div>
</body>
</html>
<?php
    // end else
   }
?>