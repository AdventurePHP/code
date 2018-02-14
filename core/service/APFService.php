<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\core\service;

use APF\core\pagecontroller\ApplicationContext;

/**
 * Defines the structure of an APF service that is initialized with the <em>ServiceManager</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.08.2011<br />
 * Version 0.2, 08.07.2012 (Added constant for service type "CACHED")<br />
 * Version 0.3, 23.07.2013 (Added "APPLICATIONSINGLETON" creation type)<br />
 * Version 0.4, 10.04.2015 (ID#249: introduced constructor injection for object creation)<br />
 */
interface APFService extends ApplicationContext {

   // these constants define the service type of the APF objects
   const SERVICE_TYPE_NORMAL = 'NORMAL';
   const SERVICE_TYPE_CACHED = 'CACHED';
   const SERVICE_TYPE_SINGLETON = 'SINGLETON';
   const SERVICE_TYPE_SESSION_SINGLETON = 'SESSIONSINGLETON';
   const SERVICE_TYPE_APPLICATION_SINGLETON = 'APPLICATIONSINGLETON';

   /**
    * Sets the service type of the current APF object.
    *
    * @param string $serviceType The service type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setServiceType($serviceType);

   /**
    * Returns the service type of the current APF object.
    *
    * @return string The service type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getServiceType();

}
