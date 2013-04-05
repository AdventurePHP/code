<?php
namespace APF\tools\string;

/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
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

/**
 * @package APF\tools\string
 * @class StringEncryptor
 *
 * Providers password encryption services using a configured salt.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 24.06.2006<br />
 * Version 0.2, 02.06.2007 (Now inherits from APFObject to be able to use as service object)<br />
 */
class StringEncryptor extends APFObject {

   /**
    * @public
    *
    * Creates a password hash with a static salt. The salt is read from a configuration
    * names <em>{ENVIRONMENT}_encryption.ini</em> located under
    * <em>config::tools::string::{CONTEXT}</em>.
    *
    * @param string $password The clear text password.
    * @param string $section he name of the configuration section.
    * @return string The password hash.
    * @throws ConfigurationException In case the config is not present.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.06.2006<br />
    * Version 0.2, 02.06.2007 (Switched to ConfigurationManager)<br />
    */
   public function getPasswordHash($password, $section = 'Standard') {
      $config = $this->getConfiguration('tools::string', 'encryption.ini');
      return crypt($password, $config->getSection($section)->getValue('PasswordSalt'));
   }

}
