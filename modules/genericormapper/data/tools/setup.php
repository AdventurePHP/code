<?php
   // PageController einbinden
   require('../../apps/core/pagecontroller/pagecontroller.php');

   // Ggf. Werte der Registry anpassen
   $Reg = &Singleton::getInstance('Registry');
   $Reg->register('apf::core','Environment',{ENVIRONMENT});

   // SetupMapper einbinden
   import('modules::genericormapper::data::tools','GenericORMapperSetup');

   // SetupMapper instanziieren
   $SetupMapper = new GenericORMapperSetup();

   // Context der Applikation bekannt geben (wichtig f&uuml;r die Konfigurationsdateien!)
   $SetupMapper->set('Context',{CONTEXT});

   // Ggf. MySQL Storage-Engine anpassen (Standard is MyISAM)
   $SetupMapper->set('StorageEngine','...');

   // Datenbanklayout erstellen
   $SetupMapper->setupDatabase({CONFIG_NAMESPACE},{CONFIG_NAME_AFFIX},{CONNECTION_NAME});

   // Datenbanklayout lediglich anzeigen
   $SetupMapper->setupDatabase({CONFIG_NAMESPACE},{CONFIG_NAME_AFFIX});
?>