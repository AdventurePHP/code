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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::guestbook2009::biz','GuestbookModel');

   /**
    * @package modules::guestbook2009::pres::taglib
    * @class gb_taglib_import
    *
    * Implements the taglib class to include the guestbook and to fill the model
    * with the appropriate information.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2009<br />
    */
   class gb_taglib_import extends core_taglib_importdesign {

      public function __construct(){
         parent::__construct();
      }

      /**
       * @public
       *
       * Fills the model information and includes the guestbook's main template.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.05.2009<br />
       */
      public function onParseTime(){

         $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $guestbookId = $this->getAttribute('gbid');

         // do not include the guestbook, if gbid is not set/existent
         if($guestbookId == null || ((int)$guestbookId) == 0){
            throw new InvalidArgumentException('[gb_taglib_import::onParseTime()] The attribute '
               .'"gbid" is empty or not present or the value is not an id. Please specify the '
               .'attribute correctly in order to include the guestbook module!');
         }

         $model->setGuestbookId($guestbookId);

         $this->__Attributes['namespace'] = 'modules::guestbook2009::pres::templates';
         $this->__Attributes['template'] = 'guestbook';
         parent::onParseTime();
         
       // end function
      }

    // end class
   }
?>