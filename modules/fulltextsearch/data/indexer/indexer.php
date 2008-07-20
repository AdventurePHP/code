<?php
   /**
   *  @package modules::fulltextsearch::data
   *  @file httpindexer.php
   *
   *  Wrapper-Datei für den Indexer.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 10.03.2008<br />
   *  Version 0.2, 16.03.2008<br />
   */

   // Fehlermeldung konfigurieren (für Livebetrieb)
   ini_set('html_errors','off');
   error_reporting(E_ALL);
   ini_set('display_errors','On');
   set_time_limit(0);
   ini_set('memory_limit','300M');

   // FrontController einbinden
   import('core::frontcontroller','Frontcontroller');

   // Indexer einbinden
   import('modules::fulltextsearch::data::indexer','fulltextsearchIndexer');

   // Indexer erstellen
   $fSI = new fulltextsearchIndexer();
   $fSI->set('Context','sites::demosite');

   // Gewünschten Job ausführen
   $nothing2do = false;

   if(isset($_REQUEST['job'])){

      if($_REQUEST['job'] == 'createindex'){
         $fSI->createIndex();
       // end if
      }
      elseif($_REQUEST['job'] == 'importarticles'){
         $fSI->importArticles();
       // end if
      }
      else{
         $nothing2do = true;
       // end else
      }

    // end if
   }
   else{
      $nothing2do = true;
    // end else
   }


   // Kein Job angegeben
   if($nothing2do == true){
      echo 'Parameter "job" not filled, so there\'s nothing to do! Valid jobs are "createindex" and "importarticles".';
    // end if
   }
?>