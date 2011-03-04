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
            $this->ConfigNamespace = $configNamespace;
            $this->ConfigNameAffix = $configNameAffix;

            $this->createMappingTable();
            $this->createServiceObjectsTable();
            foreach($this->ServiceObjectsTable as $name => $DUMMY){
                $this->generateServiceObject($name);
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
        protected function generateServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->ServiceObjectsTable[$name]['Namespace']).'/'.$this->ServiceObjectsTable[$name]['Class'].'.php';

            // check if we need to update an old or create a new definition
            if(file_exists($filename)){
                $this->updateServiceObject($name, $filename);
            }
            else {
                $this->createNewServiceObject($name, $filename);
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
        protected function createNewServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->ServiceObjectsTable[$name]['Namespace']).'/'.$this->ServiceObjectsTable[$name]['Class'].'.php';

            $content = '<?php'.PHP_EOL . PHP_EOL.
                    $this->generateBaseObjectCode($name) . PHP_EOL . PHP_EOL .
                    $this->generateObjectCode($name) . PHP_EOL . PHP_EOL.
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
        protected function updateServiceObject($name){
            $filename = APPS__PATH.'/'.str_replace('::','/',$this->ServiceObjectsTable[$name]['Namespace']).'/'.$this->ServiceObjectsTable[$name]['Class'].'.php';

            $content = file_get_contents($filename);
            $newcode = $this->generateBaseObjectCode($name);
            
            // replace only base object area, don't change anything else!

            // <<< *IMPORTANT* There seems to be a bug in preg_replace() which
            // causes a crash when trying to use the php-code from the old file
            // as subject as shown here:
            /*$content = preg_replace(
                    '%//<\*'.$this->ServiceObjectsTable[$name]['Class'].'Base:start\*>(.)+<\*'.$this->ServiceObjectsTable[$name]['Class'].'Base:end\*>%s',
                    $newcode,
                    $content
            );*/
            // *WORKAROUND* with preg_* functions not found, used some string funtions instead:
            $starttag = '//<*'.$this->ServiceObjectsTable[$name]['Class'].'Base:start*>';
            $endtag = '<*'.$this->ServiceObjectsTable[$name]['Class'].'Base:end*>';
            $start = strpos($content, $starttag);
            $length = strpos($content, $endtag, $start) + strlen($endtag) - $start;
            $content = substr_replace($content, $newcode, $start, $length);
            // If anyone has further information or a solution for this, please
            // write a post in the APF-forum. PHP-version: found at 5.3.5  >>>
            
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
        protected function generateBaseObjectCode($name){
            $code = '//<*'.$this->ServiceObjectsTable[$name]['Class'].'Base:start*> DO NOT CHANGE THIS COMMENT!'. PHP_EOL.
                    '/**'. PHP_EOL .
                    ' * Automatically generated BaseObject for '. $this->ServiceObjectsTable[$name]['Class']. '. !!DO NOT CHANGE THIS BASE-CLASS!!'. PHP_EOL .
                    ' * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!'. PHP_EOL .
                    ' * You can change class "'.$this->ServiceObjectsTable[$name]['Class'].'" which will extend this base-class.'.PHP_EOL.
                    ' */'. PHP_EOL;

            if(isset($this->ServiceObjectsTable[$name]['Base'])){
                $BaseNamespace = $this->ServiceObjectsTable[$name]['Base']['Namespace'];
                $BaseClass = $this->ServiceObjectsTable[$name]['Base']['Class'];
            }
            else {
                $BaseNamespace = $this->DefaultBaseNamespace;
                $BaseClass = $this->DefaultBaseClass;
            }
            
            $code.= 'import(\''.$BaseNamespace.'\', \''.$BaseClass.'\');'.PHP_EOL.
                    'class '. $this->ServiceObjectsTable[$name]['Class'] . 'Base extends '.$BaseClass . ' {'.PHP_EOL.PHP_EOL.
                    '    public function __construct($objectName = null){'.PHP_EOL.
                    '        parent::__construct(\''.$name.'\');'.PHP_EOL.
                    '    }'.PHP_EOL.
                    PHP_EOL;

            foreach($this->MappingTable[$name] as $Key => $DUMMY){
                if($Key === 'ID' || $Key === 'Table')
                    continue;
                $code .= $this->generateGetterCode($Key);
                $code .= $this->generateSetterCode($Key);
            }

            $code.= '}'.PHP_EOL.
                    '// DO NOT CHANGE THIS COMMENT! <*'.$this->ServiceObjectsTable[$name]['Class'].'Base:end*>';
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
        protected function generateGetterCode($name){
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
        protected function generateSetterCode($name){
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
        protected function generateObjectCode($name){
            return
                '/**'.PHP_EOL.
                ' * Domain object for "' . $this->ServiceObjectsTable[$name]['Class'] . '"'.PHP_EOL.
                ' * Use this class to add your own functions.'.PHP_EOL.
                ' */'.PHP_EOL.
                'class '.$this->ServiceObjectsTable[$name]['Class'].' extends '.$this->ServiceObjectsTable[$name]['Class'].'Base {'.PHP_EOL.
                '    /**'.PHP_EOL.
                '     * Call parent\'s function because the objectName needs to be set.'.PHP_EOL.
                '     */'.PHP_EOL.
                '    public function __construct($objectName = null){'.PHP_EOL.
                '        parent::__construct();'.PHP_EOL.
                '    }'.PHP_EOL.
                PHP_EOL.
                '}';
        }

     // end class
    }
?>
