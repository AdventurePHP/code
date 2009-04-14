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

   // include page controller
   require('../../apps/core/pagecontroller/pagecontroller.php');

   // configure the registry if desired
   $Reg = &Singleton::getInstance('Registry');
   $Reg->register('apf::core','Environment','{ENVIRONMENT}');

   // include SetupMapper
   import('modules::genericormapper::data::tools','GenericORMapperSetup');

   // create SetupMapper
   $SetupMapper = new GenericORMapperSetup();

   // set Context (important for the configuration files!)
   $SetupMapper->set('Context','{CONTEXT}');

   // adapt storage engine (default is MyISAM)
   $SetupMapper->set('StorageEngine','...');

   // create database layout
   $SetupMapper->setupDatabase('{CONFIG_NAMESPACE}','{CONFIG_NAME_AFFIX}','{CONNECTION_NAME}');

   // display database only
   $SetupMapper->setupDatabase('{CONFIG_NAMESPACE}','{CONFIG_NAME_AFFIX}');
?>