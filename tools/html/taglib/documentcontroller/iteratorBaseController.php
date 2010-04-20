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

   /**
    * @package tools::html::taglib::documentcontroller
    * @class iteratorBaseController
    *
    * Implements a document controller to be used with the iterator tag. Allows
    * you to access an iterator similar to normal templates.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.06.2008<br />
    */
   class iteratorBaseController extends base_controller {

      public function iteratorBaseController(){
      }

      /**
       * @protected
       *
       * Returns a reference on the desired iterator.
       *
       * @param string $name Name of the iterator.
       * @return html_taglib_iterator The desired iterator.
       * @throws IncludeException In case the iterator taglib is not loaded.
       * @throws Exception In case the desired iterator cannot be returned.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 02.06.2008<br />
       */
      protected function &__getIterator($name){

         $tagLibClass = 'html_taglib_iterator';

         if(!class_exists($tagLibClass)){
            throw new IncludeException('['.get_class($this).'::__getIteratorTemplate()] TagLib module "'.$tagLibClass.'" is not loaded!',E_USER_ERROR);
         }

         $children = &$this->__Document->getChildren();
         if(count($children) > 0){

            foreach($children as $objectId => $DUMMY){

               // check, whether the desired node is the iterator we want to have
               if(get_class($children[$objectId]) == $tagLibClass){
                  if($children[$objectId]->getAttribute('name') == $name){
                     return $children[$objectId];
                  }
               }

            }

          // end if
         }
         else{
            throw new Exception('['.get_class($this).'::__getIteratorTemplate()] No iterator object with name "'.$name.'" composed in current document for document controller "'.get_class($this).'"! Perhaps tag library html:iterator is not loaded in current template!',E_USER_ERROR);
            exit();
          // end else
         }

         throw new Exception('['.get_class($this).'::__getIteratorTemplate()] Iterator with name "'.$name.'" cannot be found!',E_USER_ERROR);
         exit();

       // end function
      }

    // end class
   }
?>