<?php
   /**
   *  @file webstat.php
   *
   *  Repräsentiert eine Fliege, die die Hits einer Webseite zählt.<br />
   *  Das Bild wird in das Template einer Webseite per Webstat-Tag eingebunden.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 14.12.2005<br />
   *  Version 0.2, 22.12.2005<br />
   *  Version 0.3, 01.04.2007 (Auf PC V2 umgestellt)<br />
   */
   require_once(APPS__PATH.'/core/applicationmanager/ApplicationManager.php');

   import('modules::webstat::biz','webStatManager');

   // Parameter extrahieren
   $StatParam = unserialize(base64_decode(trim($_REQUEST['StatParameter'])));

   // Statistik schreiben
   $wSM = new webStatManager();
   $wSM->set('Context',$Context);
   $wSM->writeStatistic($StatParam['Seite'],
                        $StatParam['Benutzer'],
                        $StatParam['RequestURI'],
                        $StatParam['SessionID'],
                        $StatParam['Referrer']
                       );

   // Transparentes 1x1 gif ausliefern
   header('Content-Type: image/gif');
   printf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%',71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59);
?>