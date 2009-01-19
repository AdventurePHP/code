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

   import('core::database','connectionManager');


   /**
   *  @namespace modules::pager::data
   *  @class PagerMapper
   *
   *  Represents the data layer of the pager.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   *  Version 0.2, 19.02.2009 (Added the connection key handling)<br />
   */
   class PagerMapper extends coreObject
   {


      /**
      *  @private
      *  Defines the database connection key. Must be filled within the init() method.
      */
      var $__ConnectionKey = null;


      function PagerMapper(){
      }


      /**
      *  @public
      *
      *  Initializes the connection key of the mapper.
      *
      *  @param string $connectionKey the database connection key
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 19.01.2009<br />
      */
      function init($connectionKey){
         $this->__ConnectionKey = $connectionKey;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the number of entries of the current object.
      *
      *  @param string $namespace the namespace of the statement
      *  @param string $statement the name of the statement file
      *  @param array $params additional params for the statement
      *  @return string $entriesCount the number of entries
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 16.08.2006 (Added an argument for further statement params)<br />
      *  Version 0.3, 19.01.2009 (Added the connection key handling)<br />
      */
      function getEntriesCountValue($namespace,$statement,$params = array()){

         $t = &Singleton::getInstance('benchmarkTimer');
         $t->start('PagerMapper::getEntriesCountValue()');
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($this->__ConnectionKey);
         $result = $SQL->executeStatement($namespace,$statement,$params);
         $data = $SQL->fetchData($result);
         $t->stop('PagerMapper::getEntriesCountValue()');
         return $data['EntriesCount'];

       // end function
      }


      /**
      *  @public
      *
      *  Returns a list of the object ids, that should be loaded for the current page.
      *
      *  @param string $namespace the namespace of the statement
      *  @param string $statement the name of the statement file
      *  @param array $params additional params for the statement
      *  @return array $entries a list of entry ids
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 19.01.2009 (Added the connection key handling)<br />
      */
      function loadEntries($namespace,$statement,$params = array()){

         $t = &Singleton::getInstance('benchmarkTimer');
         $t->start('PagerMapper::loadEntries()');
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($this->__ConnectionKey);
         $result = $SQL->executeStatement($namespace,$statement,$params);

         $list = array();

         while($data = $SQL->fetchData($result)){
            $list[] = $data['DB_ID'];
          // end while
         }

         $t->stop('PagerMapper::loadEntries()');
         return $list;

       // end function
      }

    // end class
   }
?>