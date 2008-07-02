<?php
   require_once(APPS__PATH.'/core/applicationmanager/ApplicationManager.php');

   import('core::pagecontroller','pagecontroller');


   // Page wird IMMER ohne URLRewriting gestartet, damit die übergebenen URL-Parameter
   // richtig aus der URL-Parametern geparst werden
   $Page = new Page('Druck-Ansicht',false);
   $Page->set('Context',$Context);
   $Page->loadDesign('modules::footer::pres::templates','print');
   echo $Page->transform();
?>
