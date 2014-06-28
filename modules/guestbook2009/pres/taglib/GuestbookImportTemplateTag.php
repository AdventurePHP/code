<?php
namespace APF\modules\guestbook2009\pres\taglib;

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
use APF\core\pagecontroller\ImportTemplateTag;
use APF\modules\guestbook2009\biz\GuestbookModel;
use InvalidArgumentException;

/**
 * Implements the taglib class to include the guestbook and to fill the model
 * with the appropriate information.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.05.2009<br />
 */
class GuestbookImportTemplateTag extends ImportTemplateTag {

   /**
    * Fills the model information and includes the guest book's main template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2009<br />
    */
   public function onParseTime() {

      /* @var $model GuestbookModel */
      $model = & $this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
      $guestbookId = $this->getAttribute('gbid');

      // do not include the guestbook, if gbid is not set/existent
      if ($guestbookId == null || ((int) $guestbookId) == 0) {
         throw new InvalidArgumentException('[GuestbookImportTemplateTag::onParseTime()] The attribute '
               . '"gbid" is empty or not present or the value is not an id. Please specify the '
               . 'attribute correctly in order to include the guestbook module!');
      }

      $model->setGuestbookId($guestbookId);

      $this->setAttribute('namespace', 'APF\modules\guestbook2009\pres\templates');
      $this->setAttribute('template', 'guestbook');
      parent::onParseTime();

   }

}