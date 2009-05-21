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

   /**
    * @namespace modules::guestbook2009::pres::controller
    * @class admin_controller
    * 
    * Implements the document controller for the admin main view. Generates the links
    * for the subviews to edit or delete an entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.05.2009<br />
    */
   class admin_controller extends baseController {
      
       public function transformContent(){

          // invoke the service to check, if the current user may request this page
          $gS = &$this->__getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
          $gS->checkAccessAllowed();

          // generate the admin menu links using the fc linkhander to
          // be able to include the module in either page.
          $editLink = frontcontrollerLinkHandler::generateLink(
             $_SERVER['REQUEST_URI'],
             array(
               'gbview' => 'admin',
                'adminview' => 'edit'
             )
          );
          $this->setPlaceHolder('editLink',$editLink);

          $deleteLink = frontcontrollerLinkHandler::generateLink(
             $_SERVER['REQUEST_URI'],
             array(
               'gbview' => 'admin',
                'adminview' => 'delete'
             )
          );
          $this->setPlaceHolder('deleteLink',$deleteLink);

          $logoutLink = frontcontrollerLinkHandler::generateLink(
             $_SERVER['REQUEST_URI'],
             array(
               'gbview' => 'admin',
                'adminview' => 'logout'
             )
          );
          $this->setPlaceHolder('logoutLink',$logoutLink);

        // end function
       }
   
    // end class
   }
?>