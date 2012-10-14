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
import('modules::genericormapper::data', 'BaseMapper');
import('tools::filesystem', 'Folder');
import('tools::filesystem', 'File');

/**
 * @package modules::genericormapper::data::tools
 * @class GenericORMapperDomainObjectGenerator
 *
 * Automatically generates DomainObjects for the GenericORMapper
 * which are defined in <em>*_domainobjects.ini</em>.
 *
 * @author Ralf Schubert
 * @version 0.1, 15.01.2011<br />
 */
class GenericORMapperDomainObjectGenerator extends BaseMapper {

   protected $DefaultBaseNamespace = 'modules::genericormapper::data';
   protected $DefaultBaseClass = 'GenericDomainObject';

   /**
    * Generates all service objects which are defined in *_domainobjects.ini
    *
    * @param string $configNamespace namespace, where the desired mapper configuration is located
    * @param string $configNameAffix name affix of the object and relation definition files
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   public function generateServiceObjects($configNamespace, $configNameAffix) {
      $this->configNamespace = $configNamespace;
      $this->configNameAffix = $configNameAffix;

      $this->createMappingTable();
      $this->createDomainObjectsTable();
      foreach ($this->domainObjectsTable as $name => $DUMMY) {
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
   protected function generateServiceObject($name) {
      $filename = APPS__PATH . '/' . str_replace('::', '/', $this->domainObjectsTable[$name]['Namespace']) . '/' . $this->domainObjectsTable[$name]['Class'] . '.php';

      // check if we need to update an old or create a new definition
      if (file_exists($filename)) {
         $this->updateServiceObject($name, $filename);
      } else {
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
   protected function createNewServiceObject($name) {
      $filename = APPS__PATH . '/' . str_replace('::', '/', $this->domainObjectsTable[$name]['Namespace']) . '/' . $this->domainObjectsTable[$name]['Class'] . '.php';

      $content = '<?php' . PHP_EOL . PHP_EOL .
            $this->generateBaseObjectCode($name) . PHP_EOL . PHP_EOL .
            $this->generateObjectCode($name) . PHP_EOL;

      $path = dirname($filename);
      if (!file_exists($path)) {
         $folder = new Folder();
         $folder->create($path);
      }

      $file = new File();
      $file->create($filename)->writeContent($content);
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
   protected function updateServiceObject($name) {
      $filename = APPS__PATH . '/' . str_replace('::', '/', $this->domainObjectsTable[$name]['Namespace']) . '/' . $this->domainObjectsTable[$name]['Class'] . '.php';

      $content = file_get_contents($filename);
      $newCode = $this->generateBaseObjectCode($name);

      // replace only base object area, don't change anything else!
      // <<< *IMPORTANT* There seems to be a bug in preg_replace() which
      // causes a crash when trying to use the php-code from the old file
      // as subject as shown here:
      /* $content = preg_replace(
        '%//<\*'.$this->domainObjectsTable[$name]['Class'].'Base:start\*>(.)+<\*'.$this->domainObjectsTable[$name]['Class'].'Base:end\*>%s',
        $newcode,
        $content
        ); */
      // *WORKAROUND* with preg_* functions not found, used some string funtions instead:
      $startTag = '//<*' . $this->domainObjectsTable[$name]['Class'] . 'Base:start*>';
      $endTag = '<*' . $this->domainObjectsTable[$name]['Class'] . 'Base:end*>';
      $start = strpos($content, $startTag);
      $length = strpos($content, $endTag, $start) + strlen($endTag) - $start;
      $content = substr_replace($content, $newCode, $start, $length);
      // If anyone has further information or a solution for this, please
      // write a post in the APF-forum. PHP-version: found at 5.3.5  >>>

      $file = new File();
      $file->open($filename)->writeContent($content);
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
   protected function generateBaseObjectCode($name) {
      $code = '//<*' . $this->domainObjectsTable[$name]['Class'] . 'Base:start*> DO NOT CHANGE THIS COMMENT!' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Automatically generated BaseObject for ' . $this->domainObjectsTable[$name]['Class'] . '. !!DO NOT CHANGE THIS BASE-CLASS!!' . PHP_EOL .
            ' * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!' . PHP_EOL .
            ' * You can change class "' . $this->domainObjectsTable[$name]['Class'] . '" which extends this base-class.' . PHP_EOL .
            ' */' . PHP_EOL;

      if (isset($this->domainObjectsTable[$name]['Base'])) {
         $baseNamespace = $this->domainObjectsTable[$name]['Base']['Namespace'];
         $baseClass = $this->domainObjectsTable[$name]['Base']['Class'];
      } else {
         $baseNamespace = $this->DefaultBaseNamespace;
         $baseClass = $this->DefaultBaseClass;
      }

      $code .= 'import(\'' . $baseNamespace . '\', \'' . $baseClass . '\');' . PHP_EOL .
            PHP_EOL .
            '/**' . PHP_EOL .
            ' * @package ' . $this->domainObjectsTable[$name]['Namespace'] . PHP_EOL .
            ' * @class ' . $this->domainObjectsTable[$name]['Class'] . 'Base' . PHP_EOL .
            ' * ' . PHP_EOL .
            ' * This class provides the descriptive getter and setter methods for the "' . $this->domainObjectsTable[$name]['Class'] . '" domain object.' . PHP_EOL .
            ' */' . PHP_EOL .
            'abstract class ' . $this->domainObjectsTable[$name]['Class'] . 'Base extends ' . $baseClass . ' {' . PHP_EOL . PHP_EOL .
            '   public function __construct($objectName = null){' . PHP_EOL .
            '      parent::__construct(\'' . $name . '\');' . PHP_EOL .
            '   }' . PHP_EOL .
            PHP_EOL;

      foreach ($this->mappingTable[$name] as $key => $DUMMY) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $code .= $this->generateGetterCode($key);
         $code .= $this->generateSetterCode($key, $this->domainObjectsTable[$name]['Class']);
         $code .= $this->generateDeleteCode($key, $this->domainObjectsTable[$name]['Class']);
      }

      // generate getter for the generic elements, too.
      $code .= $this->generateGetterCode('CreationTimestamp');
      $code .= $this->generateGetterCode('ModificationTimestamp');

      $code .= '}' . PHP_EOL .
            PHP_EOL .
            '// DO NOT CHANGE THIS COMMENT! <*' . $this->domainObjectsTable[$name]['Class'] . 'Base:end*>';
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
   protected function generateGetterCode($name) {
      return '   /**' . PHP_EOL .
            '    * @return string The value for property "' . $name . '".' . PHP_EOL .
            '    */' . PHP_EOL .
            '   public function get' . $name . '() {' . PHP_EOL .
            '      return $this->getProperty(\'' . $name . '\');' . PHP_EOL .
            '   }' . PHP_EOL . PHP_EOL;
   }

   /**
    * Generates the PHP code for a property's delete method with the given name.
    *
    * @param string $name The property's name.
    * @param string $className The name of the class.
    * @return string The PHP code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.09.2011<br />
    */
   protected function generateDeleteCode($name, $className) {
      return '   /**' . PHP_EOL .
            '    * @return ' . $className . ' The domain object for further usage.' . PHP_EOL .
            '    */' . PHP_EOL .
            '   public function delete' . $name . '() {' . PHP_EOL .
            '      $this->deleteProperty(\'' . $name . '\');' . PHP_EOL .
            '      return $this;' . PHP_EOL .
            '   }' . PHP_EOL . PHP_EOL;
   }

   /**
    * Generates the PHP code for a property's setter with the given name.
    *
    * @param string $name The property's name.
    * @param string $className The name of the class.
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateSetterCode($name, $className) {
      return '   /**' . PHP_EOL .
            '    * @param string $value The value to set for property "' . $name . '".' . PHP_EOL .
            '    * @return ' . $className . ' The domain object for further usage.' . PHP_EOL .
            '    */' . PHP_EOL .
            '   public function set' . $name . '($value) {' . PHP_EOL .
            '      $this->setProperty(\'' . $name . '\', $value);' . PHP_EOL .
            '      return $this;' . PHP_EOL .
            '   }' . PHP_EOL . PHP_EOL;
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
   protected function generateObjectCode($name) {
      return
            '/**' . PHP_EOL .
            ' * @package ' . $this->domainObjectsTable[$name]['Namespace'] . PHP_EOL .
            ' * @class ' . $this->domainObjectsTable[$name]['Class'] . PHP_EOL .
            ' * ' . PHP_EOL .
            ' * This class represents the "' . $this->domainObjectsTable[$name]['Class'] . '" domain object.' . PHP_EOL .
            ' * <p/>' . PHP_EOL .
            ' * Please use this class to add your own functionality.' . PHP_EOL .
            ' */' . PHP_EOL .
            'class ' . $this->domainObjectsTable[$name]['Class'] . ' extends ' . $this->domainObjectsTable[$name]['Class'] . 'Base {' . PHP_EOL .
            PHP_EOL .
            '   /**' . PHP_EOL .
            '    * Call the parent\'s constructor because the object name needs to be set.' . PHP_EOL .
            '    * <p/>' . PHP_EOL .
            '    * To create an instance of this object, just call' . PHP_EOL .
            '    * <code>' . PHP_EOL .
            '    * $object = new ' . $this->domainObjectsTable[$name]['Class'] . '();' . PHP_EOL .
            '    * </code>' . PHP_EOL .
            '    *' . PHP_EOL .
            '    * @param string $objectName The internal object name of the domain object.' . PHP_EOL .
            '    */' . PHP_EOL .
            '   public function __construct($objectName = null){' . PHP_EOL .
            '      parent::__construct();' . PHP_EOL .
            '   }' . PHP_EOL .
            PHP_EOL .
            '}';
   }

}
