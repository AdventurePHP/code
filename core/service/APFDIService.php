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

/**
 * Defines the structure of an APF service that is initialized with the <em>DIServiceManager</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.08.2011<br />
 */
interface APFDIService extends APFService {

   /**
    * Marks the service as initialized.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function markAsInitialized();

   /**
    * Marks the service as *not* initialized or *no more* initialized. This causes the DIServiceManager
    * to initialize or re-initialize the service on next access.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function markAsPending();

   /**
    * Returns the initialization of the present service. In case the service is initialized,
    * the DIServiceManager to omit the call of the setup method defined within the DI service
    * object definition section.
    *
    * @return bool The initialization status (true = initialized, false = not initialized).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function isInitialized();

}
