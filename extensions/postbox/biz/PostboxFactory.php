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
namespace APF\extensions\postbox\biz;

use APF\core\pagecontroller\APFObject;
use APF\modules\genericormapper\data\GenericORMapperDataObject;

/**
 * This factory generates the Postbox-objects for given users.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
class PostboxFactory extends APFObject {

   /**
    * Returns the Postbox for the given user.
    *
    * @param GenericORMapperDataObject $user
    *
    * @return Postbox
    */
   public function getPostbox(GenericORMapperDataObject &$user) {
      /* @var $postbox Postbox */
      $postbox = $this->getDIServiceObject('APF\extensions\postbox', 'Postbox');

      return $postbox->setUser($user);
   }

}

