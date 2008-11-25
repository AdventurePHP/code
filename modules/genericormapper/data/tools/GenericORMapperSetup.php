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

   import('modules::genericormapper::data','BaseMapper');


   /**
   *  @namespace modules::genericormapper::data
   *  @classs
   *
   *  Tool to setup the database automatically. Changes to the database layout cannot be applied.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.05.2008<br />
   */
   class GenericORMapperSetup extends BaseMapper
   {

      /**
      *  @private
      *  Indicators for the datatype mapping.
      */
      var $__RowTypeMappingFrom = array(
                                        '/^VARCHAR\(([0-9]+)\)$/i',
                                        '/^TEXT$/i',
                                        '/^DATE$/i'
                                       );

      /**
      *  @private
      *  Replace strings for the datatype mapping.
      */
      var $__RowTypeMappingTo = array(
                                      'VARCHAR($1) character set utf8 NOT NULL default \'\'',
                                      'TEXT character set utf8 NOT NULL',
                                      'DATE NOT NULL default \'0000-00-00\''
                                     );


      /**
      *  @private
      *  Stores the MySQL storage engine type.
      */
      var $__StorageEngine = 'MyISAM';


      function GenericORMapperSetup(){
      }


      /**
      *  @public
      *
      *  Setups the database. Uses the connectionManager, hence a valid database connection is required.
      *  If the third parameter ($ConnectionName) is left blank, the statements are displayed only.
      *
      *  @param string $ConfigNamespace; namespace, where the desired mapper configuration is located
      *  @param string $ConfigNameAffix; name affix of the object and relation definition files
      *  @param string $ConnectionName; name of the connection, that the mapper should use to acces the database
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.05.2008<br />
      *  Version 0.2, 23.06.2008 (Improvements (configurable storage engine, ...) and addition of display only feature)<br />
      */
      function setupDatabase($ConfigNamespace,$ConfigNameAffix,$ConnectionName = null){

         // set the config namespace
         $this->__ConfigNamespace = $ConfigNamespace;

         // set the config name affix
         $this->__ConfigNameAffix = $ConfigNameAffix;

         // setup object layout
         $Objects = $this->__generateObjectLayout();

         // setup relation layout
         $Relations = $this->__generateRelationLayout();

         // display only
         if($ConnectionName === null){

            // display object structure
            echo '<pre>';
            foreach($Objects as $Object){
               echo PHP_EOL.PHP_EOL.$Object;
             // end function
            }

            // display relation structure
            foreach($Relations as $Relation){
              echo PHP_EOL.PHP_EOL.$Relation;
             // end function
            }
            echo '</pre>';

          // end else
         }
         else{

            // get connection manager
            $cM = &$this->__getServiceObject('core::database','connectionManager');

            // initialize connection
            $this->__DBDriver = &$cM->getConnection($ConnectionName);

            // create object structure
            foreach($Objects as $Object){
               $this->__DBDriver->executeTextStatement($Object);
             // end function
            }

            // create relation structure
            foreach($Relations as $Relation){
               $this->__DBDriver->executeTextStatement($Relation);
             // end function
            }

          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Creates the setup statements for the object persistance.<br />
      *
      *  @return string $Setup; sql setup script
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 31.05.2008 (Code completed and refactored due to changes on the mapping table)<br />
      */
      function __generateObjectLayout(){

         // create mapping table
         $this->__createMappingTable();

         // generate tables for objects
         $setup = array();
         foreach($this->__MappingTable as $Name => $Attributes){

            // header
            $create = 'CREATE TABLE IF NOT EXISTS `'.$Attributes['Table'].'` ('.PHP_EOL;

            // id row
            $create .= '  `'.$Attributes['ID'].'` TINYINT(5) NOT NULL auto_increment,'.PHP_EOL;

            // object properties
            foreach($Attributes as $Key => $Value){
               if($Key != 'ID' && $Key != 'Table'){
                  $Value = preg_replace($this->__RowTypeMappingFrom,$this->__RowTypeMappingTo,$Value);
                  $create .= '  `'.$Key.'` '.$Value.','.PHP_EOL;
                // end if
               }
             // end if
            }

            // creation and modification information
            $create .= '  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,'.PHP_EOL;
            $create .= '  `ModificationTimestamp` timestamp NOT NULL default \'0000-00-00 00:00:00\','.PHP_EOL;

            // primary key
            $create .= '  PRIMARY KEY (`'.$Attributes['ID'].'`)'.PHP_EOL;

            // footer
            $create .= ') ENGINE='.$this->__StorageEngine.' DEFAULT CHARSET=utf8;';

            // print statement
            $setup[] = $create.PHP_EOL.PHP_EOL;

          // end foreach
         }

         // return statement string
         return $setup;

       // end function
      }


      /**
      *  @private
      *
      *  Creates the setup statements for the relation persistence.<br />
      *
      *  @return string $Setup; sql setup script
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.05.2008<br />
      */
      function __generateRelationLayout(){

         // create relation table
         $this->__createRelationTable();

         // generate tables for objects
         $setup = array();
         foreach($this->__RelationTable as $Name => $Attributes){

            // header
            $create = 'CREATE TABLE IF NOT EXISTS `'.$Attributes['Table'].'` ('.PHP_EOL;

            // id row
            if($Attributes['Type'] == 'COMPOSITION'){
               $PKName = 'CMPID';
             // end if
            }
            else{
               $PKName = 'ASSID';
             // end if
            }
            $create .= '  `'.$PKName.'` TINYINT(5) NOT NULL auto_increment,'.PHP_EOL;

            // source id
            $create .= '  `'.$Attributes['SourceID'].'` TINYINT(5) NOT NULL default \'0\','.PHP_EOL;

            // target id
            $create .= '  `'.$Attributes['TargetID'].'` TINYINT(5) NOT NULL default \'0\','.PHP_EOL;

            // indices
            $create .= '  PRIMARY KEY  (`'.$PKName.'`),'.PHP_EOL;
            $create .= '  KEY `JOININDEX` (`'.$Attributes['SourceID'].'`,`'.$Attributes['TargetID'].'`)'.PHP_EOL;

            // footer
            $create .= ') ENGINE='.$this->__StorageEngine.' DEFAULT CHARSET=utf8;';

            // print statement
            $setup[] = $create.PHP_EOL.PHP_EOL;

          // end foreach
         }

         // return statement string
         return $setup;

       // end function
      }

    // end class
   }
?>