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

   import('modules::comments::biz','commentManager');
   import('modules::comments::biz','ArticleComment');


   /**
   *  @namespace modules::comments::pres::documentcontroller
   *  @class commentBaseController
   *
   *  Implementiert den BaseController für die Kommentar-Funktion.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 21.08.2007<br />
   */
   class commentBaseController extends baseController
   {

      /**
      *  @private
      *  Kategorie-Schlüssel.
      */
      var $__CategoryKey;


      function commentBaseController(){
      }


      /**
      *  @private
      *
      *  Läd den CategoryKey vom Eltern-Objekt.<br />
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      */
      function __loadCategoryKey(){

        // AttributCategoryKey vom Parent holen
        $DocParent = &$this->__Document->getByReference('ParentObject');
        $CategoryKey = $DocParent->getAttribute('categorykey');

        if($CategoryKey == null){
           $this->__CategoryKey = 'standard';
         // end if
        }
        else{
           $this->__CategoryKey = $CategoryKey;
         // end else
        }

       // end function
      }

    // end function
   }
?>