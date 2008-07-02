<?php
   require_once(APPS__PATH.'/core/applicationmanager/ApplicationManager.php');

   import('core::pagecontroller','pagecontroller');

   // Page wird IMMER ohne URLRewriting gestartet, damit die übergebenen URL-Parameter
   // richtig aus der URL-Parametern geparst werden
   $Page = new Page('Webseite',false);
   $Page->loadDesign('modules::dateianzeige::pres::templates','dateianzeige');
   echo $Page->transform();
?>
