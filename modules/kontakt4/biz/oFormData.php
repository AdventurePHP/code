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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace modules::kontakt4::biz
   *  @class oFormData
   *
   *  Implementiert das Domänenobjekt FormData, das alle Daten des Formulars hält.<br />
   *  Dient als Schnittstelleobjekt zwischen pres und biz.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2006<br />
   */
   class oFormData extends coreObject
   {
      var $__RecipientID;
      var $__SenderName;
      var $__SenderEMail;
      var $__Subject;
      var $__Text;


      function oFormData(){

         $this->__RecipientID = (string)'';
         $this->__SenderName = (string)'';
         $this->__SenderEMail = (string)'';
         $this->__Subject = (string)'';
         $this->__Text = (string)'';

       // end function
      }

    // end class
   }
?>
