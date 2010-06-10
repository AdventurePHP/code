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
    * @package modules::usermanagement::pres::documentcontroller::proxy
    * @class umgt_type_list_controller
    *
    * Displays the list of proxy types defined. Offers the possibility to
    * edit and delete a type definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.06.2010<br />
    */
   class umgt_type_list_controller extends umgt_base_controller {

      public function transformContent(){

         $uM = &$this->getManager();

         $buffer = (string)'';
         $template = &$this->__getTemplate('Type');

         $list = $uM->loadVisibilityDefinitionTypes();
         foreach($list as $id => $DUMMY){
            $template->setPlaceHolder('AppObjectName',$list[$id]->getProperty('AppObjectName'));

            $proxyTypeId = $list[$id]->getObjectId();
            $template->setPlaceHolder('type_edit',$this->__generateLink(array('mainview' => 'proxy','proxyview' => 'typeedit','proxytypeid' => $proxyTypeId)));
            $template->setPlaceHolder('type_delete',$this->__generateLink(array('mainview' => 'proxy','proxyview' => 'typedelete','proxytypeid' => $proxyTypeId)));

            $buffer .= $template->transformTemplate();
         }
         $this->setPlaceHolder('TypeList',$buffer);

       // end function
      }
      
    // end class
   }
?>