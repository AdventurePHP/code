<?php
   /**
   *  @file umgt.php
   *  This file represents a bootstrap file to operate the usermanagement module.
   */
   ob_start();

   // include the pagecontroller (change the path to what ever you want)
   include_once('./apps/core/pagecontroller/pagecontroller.php');

   // import the front controller
   import('core::frontcontroller','Frontcontroller');

   // create the front controller instance
   $fC = &Singleton::getInstance('Frontcontroller');

   // set the current context (change the context to what ever you want)
   $fC->set('Context','sites::demosite');

   // start thze front controller
   $fC->start('modules::usermanagement::pres::templates','main');

   // create the benchmark report
   $T = &Singleton::getInstance('BenchmarkTimer');
   echo $T->createReport();

   ob_end_flush();
?>