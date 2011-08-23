<?php
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
import('modules::usermanagement::biz::provider', 'PasswordHashProvider');

/**
 * This is the default PasswordHashProvider for the user management manager. It implements
 * init method, because of access the config file.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 04.04.2011<br />
 */
abstract class DefaultPasswordHashProvider extends APFObject implements PasswordHashProvider {

   /**
    * @protected
    * @var boolean indicates, if the component is already initialized.
    */
   protected $isInitialized = false;

   /**
    * @protected
    * @var string indicates the salt
    */
   protected $hardcodedSalt = 'AdventurePHPFramework';

   /**
    * Implements the init method, because of access the config file
    *
    * @param string $initParam section name for the umgtconfig.ini
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function init($initParam) {
      if ($this->isInitialized === false) {
         $config = $this->getConfiguration('modules::usermanagement::biz', 'umgtconfig.ini');
         $section = $config->getSection($initParam);
         if ($section === null) {
            throw new Exception('[DefaultPasswordHashProvider::init()] No section
                        with name"' . $initParam . '" found in the umgtconfig.ini!', E_USER_ERROR);
         } else {
            $salt = $section->getValue('Salt');
            if ($salt !== null) {
               $this->hardcodedSalt = $salt;
            }
         }
      }
   }
}

?>