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
namespace APF\modules\usermanagement\biz\provider;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\modules\usermanagement\biz\UmgtManager;
use Exception;

/**
 * This is the default PasswordHashProvider for the user management manager. It implements
 * init method, because of access the config file.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 04.04.2011<br />
 */
abstract class DefaultPasswordHashProvider extends APFObject implements PasswordHashProvider, APFService {

   /**
    * Indicates the default hard-coded salt.
    *
    * @var string $DEFAULT_HARDCODED_SALT
    */
   private static $DEFAULT_HARDCODED_SALT = 'AdventurePHPFramework';

   /**
    * @throws Exception
    * @return string The hard-coded salt contained within the configuration
    */
   protected function getHardCodedSalt() {
      $config = $this->getConfiguration('APF\modules\usermanagement\biz', 'umgtconfig.ini');
      if ($config->hasSection(UmgtManager::CONFIG_SECTION_NAME)) {
         return $config->getSection(UmgtManager::CONFIG_SECTION_NAME)->getValue('Salt', self::$DEFAULT_HARDCODED_SALT);
      }

      throw new Exception('[DefaultPasswordHashProvider::init()] No section with name"'
            . UmgtManager::CONFIG_SECTION_NAME . '" found in the umgtconfig.ini!',
            E_USER_ERROR);
   }
}
