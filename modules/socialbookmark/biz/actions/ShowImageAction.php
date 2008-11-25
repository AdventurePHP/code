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
   *  @namespace modules::socialbookmark::biz::actions
   *  @class ShowImageAction
   *
   *  Action für die Ausgabe der Icons.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 07.09.2007<br />
   */
   class ShowImageAction extends AbstractFrontcontrollerAction
   {

      function ShowImageAction(){
      }


      /**
      *  @public
      *
      *  Implementiert die Interface-Methode "run()" der AbstractFrontcontrollerAction.<br />
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 07.09.2007<br />
      */
      function run(){

         // Bild aus dem Input-Objekt beenden
         $Image = APPS__PATH.'/modules/socialbookmark/pres/image/'.$this->__Input->getAttribute('img').'.'.$this->__Input->getAttribute('imgext');

         // Header senden
         header('Content-type: image/'.$this->__Input->getAttribute('imgext'));
         header('Cache-Control: public');

         // Datei streamen
         readfile($Image);

         // Abarbeitung beenden
         exit();

       // end function
      }

    // end class
   }
?>