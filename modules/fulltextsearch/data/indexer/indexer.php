<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace modules::fulltextsearch::data
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