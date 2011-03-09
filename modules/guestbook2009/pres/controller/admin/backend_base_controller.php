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

   import('tools::link','FrontcontrollerLinkHandler');

   /**
    * @package modules::guestbook2009::pres
    * @class backend_base_controller
    *
    * Provides basic functionality to display the selection menu for
    * editing or deleting guestbook entries.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.05.2009<br />
    */
   abstract class backend_base_controller extends base_controller {

      /**
       * Displays the select field using the choose.html template imported into
       * the desired views.
       *
       * @param string $adminView The name of the admin view to display (edit|delete)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 18.05.2009<br />
       */
      protected function __displayEntrySelection($adminView){

         // fill the select list
         $form = &$this->__getForm('selectentry');
         $select = &$form->getFormElementByName('entryid');

         $gS = &$this->getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
         $entriesList = $gS->loadEntryListForSelection();
         $entry = new Entry();
         foreach($entriesList as $entry){
            $select->addOption($entry->getTitle().' (#'.$entry->getId().')',$entry->getId());
         }

         // define form action url concerning the view it is rendered in
         $action = FrontcontrollerLinkHandler::generateLink(
            $_SERVER['REQUEST_URI'],
            array(
               'gbview' => 'admin',
               'adminview' => $adminView
            )
         );
         $form->setAttribute('action', $action);

         $form->transformOnPlace();

      }

   }
?>