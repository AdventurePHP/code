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

    import('modules::genericormapper::data','BaseMapper');
    /**
     * Automatically generates ServiceObjects for the GenericORMapper
     * which are defined in *_serviceobjects.ini
     *
     * @author Ralf Schubert
     * @version 0.1,  15.01.2011<br />
     */
    class GenericORMapperServiceObjectGenerator extends BaseMapper{

        protected $DefaultBaseNamespace = 'modules::genericormapper::data';
        protected $DefaultBaseClass = 'GenericDomainObject';
        
        /**
         * Generates all service objects which are defined in *_serviceobjects.ini
         *
         * @param string $configNamespace namespace, where the desired mapper configuration is located
         * @param string $configNameAffix name affix of the object and relation definition files
         * 
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        public function generateServiceObjects($configNamespace,$configNameAffix){
            $this->__ConfigNamespace = $configNamespace;
            $this->__ConfigNameAffix = $configNameAffix;

            $this->__createMappingTable();
            $this->__createServiceObjectsTable();
            foreach($this->__ServiceObjectsTable as $name => $DUMMY){
                $this->__generateServiceObject($name);
            }
        }

        /**
         * Generates the service object for the object with the given name.
         *
         * @param string $name The object's name.
         * 
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __generateServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->__ServiceObjectsTable[$name]['Namespace']).'/'.$this->__ServiceObjectsTable[$name]['Class'].'.php';

            // check if we need to update an old or create a new definition
            if(file_exists($filename)){
                $this->__updateServiceObject($name, $filename);
            }
            else {
                $this->__createNewServiceObject($name, $filename);
            }
        }

        /**
         * Creates a new file with the code for the service object with the given name.
         * Will overwrite existing file!
         *
         * @param string $name The object's name.
         * 
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __createNewServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->__ServiceObjectsTable[$name]['Namespace']).'/'.$this->__ServiceObjectsTable[$name]['Class'].'.php';

            $content = '<?php'.PHP_EOL . PHP_EOL.
                    $this->__generateBaseObjectCode($name) . PHP_EOL .
                    $this->__generateObjectCode($name) . PHP_EOL . PHP_EOL.
                    '?>';

            file_put_contents($filename, $content);
        }


        /**
         * Updates an existing base-model from the object with the given name.
         * Will not change anything on the object itself, only the base-model is changed.
         *
         * @param string $name The object's name.
         * 
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __updateServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->__ServiceObjectsTable[$name]['Namespace']).'/'.$this->__ServiceObjectsTable[$name]['Class'].'.php';
            
            $content = file_get_contents($filename);
            
            // replace only base object area, don't change anything else!
            $content = preg_replace(
                    '%//<\*'.$this->__ServiceObjectsTable[$name]['Class'].'Base:start\*>(.)+<\*'.$this->__ServiceObjectsTable[$name]['Class'].'Base:end\*>%s',
                    $this->__generateBaseObjectCode($name),
                    $content
            );
            
            file_put_contents($filename, $content);
        }

        /**
         * Generates the PHP code for the base object for the object with the given name.
         * 
         * @param string $name The object's name.
         * @return string The base object's PHP code.
         *
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __generateBaseObjectCode($name){
            $code = '//<*'.$this->__ServiceObjectsTable[$name]['Class'].'Base:start*> DO NOT CHANGE THIS COMMENT!'. PHP_EOL.
                    '/**'. PHP_EOL .
                    ' * Automatically generated BaseObject for '. $this->__ServiceObjectsTable[$name]['Class']. '. !!DO NOT CHANGE THIS BASE-CLASS!!'. PHP_EOL .
                    ' * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!'. PHP_EOL .
                    ' * You can change class "'.$this->__ServiceObjectsTable[$name]['Class'].'" which will extend this base-class.'.PHP_EOL.
                    ' */'. PHP_EOL;

            if(isset($this->__ServiceObjectsTable[$name]['Base'])){
                $BaseNamespace = $this->__ServiceObjectsTable[$name]['Base']['Namespace'];
                $BaseClass = $this->__ServiceObjectsTable[$name]['Base']['Class'];
            }
            else {
                $BaseNamespace = $this->DefaultBaseNamespace;
                $BaseClass = $this->DefaultBaseClass;
            }
            
            $code.= 'import(\''.$BaseNamespace.'\', \''.$BaseClass.'\');'.PHP_EOL.
                    'class '. $this->__ServiceObjectsTable[$name]['Class'] . 'Base extends '.$BaseClass . ' {'.PHP_EOL.PHP_EOL.
                    '    protected $objectName = \''.$name.'\';'.PHP_EOL.
                    PHP_EOL;

            foreach($this->__MappingTable[$name] as $Key => $DUMMY){
                if($Key === 'ID' || $Key === 'Table')
                    continue;
                $code .= $this->__generateGetterCode($Key);
                $code .= $this->__generateSetterCode($Key);
            }

            $code.= '}'.PHP_EOL.
                    '// DO NOT CHANGE THIS COMMENT! <*'.$this->__ServiceObjectsTable[$name]['Class'].'Base:end*>'.PHP_EOL;
            return $code;
        }

        /**
         * Generates the PHP code for a property's getter with the given name.
         * 
         * @param string $name The property's name.
         * @return string The PHP code.
         *
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __generateGetterCode($name){
            return  '    public function get'.$name.'() {'.PHP_EOL.
                    '        return $this->getProperty(\''.$name.'\');'.PHP_EOL.
                    '    }'. PHP_EOL.PHP_EOL;
        }

        /**
         * Generates the PHP code for a property's setter with the given name.
         *
         * @param string $name The property's name.
         * @return string The PHP code.
         *
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __generateSetterCode($name){
            return  '    public function set'.$name.'($value) {'.PHP_EOL.
                    '        $this->setProperty(\''.$name.'\', $value);'.PHP_EOL.
                    '        return $this;'.PHP_EOL.
                    '    }'. PHP_EOL.PHP_EOL;
        }

        /**
         * Generates the code for the object, which extends the base object.
         * 
         * @param string $name The object's name.
         * @return string The PHP code.
         *
         * @author Ralf Schubert
         * @version 0.1,  15.01.2011<br />
         */
        protected function __generateObjectCode($name){
            return
                '/**'.PHP_EOL.
                ' * Domain object for "' . $this->__ServiceObjectsTable[$name]['Class'] . '"'.PHP_EOL.
                ' * Use this class to add your own functions.'.PHP_EOL.
                ' */'.PHP_EOL.
                'class '.$this->__ServiceObjectsTable[$name]['Class'].' extends '.$this->__ServiceObjectsTable[$name]['Class'].'Base {'.PHP_EOL.
                '    /**'.PHP_EOL.
                '     * Overwrite parent\'s function because the objectName is already set.'.PHP_EOL.
                '     */'.PHP_EOL.
                '    public function __construct($objectName = null){}'.PHP_EOL.
                '    '.PHP_EOL.
                '}';
        }

     // end class
    }
?>
