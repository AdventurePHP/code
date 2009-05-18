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

   import('tools::link','frontcontrollerLinkHandler');

   class backend_base_controller extends baseController {

      protected function __displayEntrySelection($adminView){

         // fill the select list
         $form = &$this->__getForm('selectentry');
         $select = &$form->getFormElementByName('entryid');

         $gS = &$this->__getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
         $entriesList = $gS->loadEntryListForSelection();
         $entry = new Entry();
         foreach($entriesList as $entry){
            $select->addOption($entry->getTitle().' (#'.$entry->getId().')',$entry->getId());
         }

         // define form action url concerning the view it is rendered in
         $action = frontcontrollerLinkHandler::generateLink(
            $_SERVER['REQUEST_URI'],
            array(
               'gbview' => 'admin',
               'adminview' => $adminView
            )
         );
         $form->setAttribute('action', $action);

         $form->transformOnPlace();

       // end function
      }

    // end class
   }
?>
