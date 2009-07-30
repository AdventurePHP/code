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

   import('modules::genericormapper::data','BaseMapper');


   /**
    * @namespace modules::genericormapper::data
    * @class GenericORMapperSetup
    *
    * Tool to setup the database automatically. <strong>Changes to the database
    * layout cannot be applied.</strong>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    */
   class GenericORMapperSetup extends BaseMapper
   {

      /**
       * @protected
       * Indicators for the datatype mapping.
       */
      protected $__RowTypeMappingFrom = array(
                                        '/^VARCHAR\(([0-9]+)\)$/i',
                                        '/^TEXT$/i',
                                        '/^DATE$/i'
                                       );

      /**
       * @protected
       * Replace strings for the datatype mapping.
       */
      protected $__RowTypeMappingTo = array(
                                      'VARCHAR($1) character set utf8 NOT NULL default \'\'',
                                      'TEXT character set utf8 NOT NULL',
                                      'DATE NOT NULL default \'0000-00-00\''
                                     );


      /**
       * @protected
       * Stores the MySQL storage engine type.
       */
      protected $__StorageEngine = 'MyISAM';


      function GenericORMapperSetup(){
      }


      /**
       * @public
       *
       * Setups the database. Uses the connectionManager, hence a valid database connection is required.
       * If the third parameter ($ConnectionName) is left blank, the statements are displayed only.
       *
       * @param string $configNamespace namespace, where the desired mapper configuration is located
       * @param string $configNameAffix name affix of the object and relation definition files
       * @param string $connectionName name of the connection, that the mapper should use to acces the database
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.05.2008<br />
       * Version 0.2, 23.06.2008 (Improvements (configurable storage engine, ...) and addition of display only feature)<br />
       */
      public function setupDatabase($configNamespace,$configNameAffix,$connectionName = null){

         // set the config namespace
         $this->__ConfigNamespace = $configNamespace;

         // set the config name affix
         $this->__ConfigNameAffix = $configNameAffix;

         // setup object layout
         $Objects = $this->__generateObjectLayout();

         // setup relation layout
         $Relations = $this->__generateRelationLayout();

         // display only
         if($connectionName === null){

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
            $this->__DBDriver = &$cM->getConnection($connectionName);

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
       * @protected
       *
       * Creates the setup statements for the object persistance.
       *
       * @return string Sql setup script
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 31.05.2008 (Code completed and refactored due to changes on the mapping table)<br />
       * Version 0.3, 09.12.2008 (Replaced TINYINT by INT)<br />
       * Version 0.4, 18.05.2009 (Changed primary key columns to UNSIGNED)<br />
       */
      protected function __generateObjectLayout(){

         // create mapping table
         $this->__createMappingTable();

         // generate tables for objects
         $setup = array();
         foreach($this->__MappingTable as $name => $attributes){

            // header
            $create = 'CREATE TABLE IF NOT EXISTS `'.$attributes['Table'].'` ('.PHP_EOL;

            // id row
            $create .= '  `'.$attributes['ID'].'` INT(5) UNSIGNED NOT NULL auto_increment,'.PHP_EOL;

            // object properties
            foreach($attributes as $key => $value){
               if($key != 'ID' && $key != 'Table'){
                  $value = preg_replace($this->__RowTypeMappingFrom,$this->__RowTypeMappingTo,$value);
                  $create .= '  `'.$key.'` '.$value.','.PHP_EOL;
                // end if
               }
             // end if
            }

            // creation and modification information
            $create .= '  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,'.PHP_EOL;
            $create .= '  `ModificationTimestamp` timestamp NOT NULL default \'0000-00-00 00:00:00\','.PHP_EOL;

            // primary key
            $create .= '  PRIMARY KEY (`'.$attributes['ID'].'`)'.PHP_EOL;

            // footer
            $create .= ') ENGINE='.$this->__StorageEngine.' DEFAULT CHARSET=utf8;';

            // print statement
            $setup[] = $create.PHP_EOL.PHP_EOL;

          // end foreach
         }

         return $setup;

       // end function
      }


      /**
       * @protected
       *
       * Creates the setup statements for the relation persistence.
       *
       * @return string Sql setup script.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.05.2008<br />
       * Version 0.2, 09.12.2008 (Replaced TINYINT by INT)<br />
       * Version 0.3, 18.05.2009 (Changed primary key columns to UNSIGNED)<br />
       * Version 0.4, 30.07.2009 (Changed foreign key columns to UNSIGNED)<br />
       */
      protected function __generateRelationLayout(){

         // create relation table
         $this->__createRelationTable();

         // generate tables for objects
         $setup = array();
         foreach($this->__RelationTable as $name => $attributes){

            // header
            $create = 'CREATE TABLE IF NOT EXISTS `'.$attributes['Table'].'` ('.PHP_EOL;

            // id row
            if($attributes['Type'] == 'COMPOSITION'){
               $pkName = 'CMPID';
             // end if
            }
            else{
               $pkName = 'ASSID';
             // end if
            }
            $create .= '  `'.$pkName.'` INT(5) UNSIGNED NOT NULL auto_increment,'.PHP_EOL;

            // source id
            $create .= '  `'.$attributes['SourceID'].'` INT(5) UNSIGNED NOT NULL default \'0\','.PHP_EOL;

            // target id
            $create .= '  `'.$attributes['TargetID'].'` INT(5) UNSIGNED NOT NULL default \'0\','.PHP_EOL;

            // indices
            $create .= '  PRIMARY KEY  (`'.$pkName.'`),'.PHP_EOL;
            $create .= '  KEY `JOININDEX` (`'.$attributes['SourceID'].'`,`'.$attributes['TargetID'].'`)'.PHP_EOL;

            // footer
            $create .= ') ENGINE='.$this->__StorageEngine.' DEFAULT CHARSET=utf8;';

            // print statement
            $setup[] = $create.PHP_EOL.PHP_EOL;

          // end foreach
         }

         return $setup;

       // end function
      }

    // end class
   }
?>