<?php
   // import registry
   import('core::registry','Registry');

   /////////////////////////////////////////////////////////////////////////////////////////////////
   // mode 1:
   /////////////////////////////////////////////////////////////////////////////////////////////////

   // get an singleton instance of the registry factory
   $regfac = &Singleton::getInstance('RegistryFactory');

   // get the preconfigured registry for the current module
   $reg = $regfac->getRegistry('tools::link');

   // configure url rewriting (used by FrontController, linkHandler and frontcontrollerLinkHandler)
   $reg->register('URLRewriting',true,true); // set entry as readonly!

   // configure logging path
   $reg->register('LogPath','/var/log/appslog',true); // set entry as readonly!

   // configure environment
   $reg->register('LogPath','/var/log/appslog',true); // set entry as readonly!

   // define value, if not defined
   if($reg->retrieve('<name>') === null){
      $reg->register('<name>','<value>');
    // end if
   }

   /////////////////////////////////////////////////////////////////////////////////////////////////
   // mode 2:
   /////////////////////////////////////////////////////////////////////////////////////////////////

   // get an singleton instance of the registry
   $reg = &Singleton::getInstance('Registry');

   // configure a module specific value
   $reg->register('my::module','<name>','<value>');

   // configure a module specific value with write protection
   $reg->retrieve('my::module','<name>','<value>',true);

   // define value, if not defined
   if($reg->retrieve('my::module','<name>') === null){
      $reg->register('my::module','<name>','<value>');
    // end if
   }
?>