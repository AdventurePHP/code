<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\modules\genericormapper\data\tools;

use APF\core\loader\RootClassLoader;
use APF\modules\genericormapper\data\BaseMapper;
use APF\modules\genericormapper\data\GenericDomainObject;
use APF\tools\filesystem\File;
use APF\tools\filesystem\Folder;

/**
 * Automatically generates DomainObjects for the GenericORMapper
 * which are defined in <em>*_domainobjects.ini</em>.
 *
 * @author Ralf Schubert
 * @version 0.1, 15.01.2011<br />
 */
class GenericORMapperDomainObjectGenerator extends BaseMapper {

   const EOL = "\n";

   protected static $DEFAULT_BASE_CLASS = GenericDomainObject::class;

   /**
    * Generates all service objects which are defined in *_domainobjects.ini
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function generateServiceObjects() {

      // Only create mapping/relation/objects table when mapping has been
      // configured directly via
      //
      // conf.namespace.method = "setConfigNamespace"
      // conf.namespace.value = "..."
      // conf.affix.method = "setConfigNameAffix"
      // conf.affix.value = "..."
      //
      // or mixed setup (DI service + basic config). Otherwise - this is
      // when configuring mappings with DI services -, there is no need
      // to do so. Regarding caching/performance, the import cache already
      // ensures, that the mapping/objects table is only created once per
      // config!
      if (!empty($this->configNamespace) && !empty($this->configNameAffix)) {
         $this->addMappingConfiguration($this->configNamespace, $this->configNameAffix);
         $this->addDomainObjectsConfiguration($this->configNamespace, $this->configNameAffix);
      }

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

      // DO class file
      $fileName = $this->getFileName($name);

      // base class file
      $baseFileName = $this->getBaseFileName($name);

      // check if we need to update an old or create a new definition
      if (file_exists($fileName) && file_exists($baseFileName)) {
         $this->updateServiceObject($name, $baseFileName);
      } else {
         $this->createNewServiceObject($name, $baseFileName, $fileName);
      }

   }

   /**
    * @param string $name The name of the domain object class.
    *
    * @return string The name of the corresponding class file.
    */
   protected function getFileName($name) {
      return $this->getFileNameByClass($this->domainObjectsTable[$name]['Class']);
   }

   protected function getFileNameByClass($class) {
      $loader = RootClassLoader::getLoaderByClass($class);
      $vendor = $loader->getVendorName();
      $rootPath = $loader->getRootPath();

      // first part of the namespace must be dropped to not double the vendor name
      return $rootPath . '/' . str_replace('\\', '/', str_replace($vendor . '\\', '', $class)) . '.php';
   }

   /**
    * @param string $name The name of the domain object class.
    *
    * @return string The name of the corresponding base class file.
    */
   protected function getBaseFileName($name) {
      return $this->getFileNameByClass($this->domainObjectsTable[$name]['Class'] . 'Base');
   }

   /**
    * Updates an existing base-model from the object with the given name.
    * Will not change anything on the object itself, only the base-model is changed.
    *
    * @param string $name The object's name.
    * @param string $baseFileName The file name the base class will be written to.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    * Version 0.2, 15.05.2013 (Update start- and end tag with class name instead of class [Tobias Lückel|Megger])<br />
    */
   protected function updateServiceObject($name, $baseFileName) {

      $content = file_get_contents($baseFileName);
      $newCode = $this->generateBaseObjectCode($name);

      // replace only base object area, don't change anything else!
      // <<< *IMPORTANT* There seems to be a bug in preg_replace() which
      // causes a crash when trying to use the php-code from the old file
      // as subject as shown here:
      /* $content = preg_replace(
        '%//<\*' . $class.'Base:start\*>(.)+<\*' . $class.'Base:end\*>%s',
        $newcode,
        $content
        ); */
      // *WORKAROUND* with preg_* functions not found, used some string functions instead:
      $class = $this->domainObjectsTable[$name]['Class'];
      $className = RootClassLoader::getClassName($class);
      $startTag = '//<*' . $className . 'Base:start*>';
      $endTag = '<*' . $className . 'Base:end*>';
      $start = strpos($content, $startTag);
      $length = strpos($content, $endTag, $start) + strlen($endTag) - $start;
      $content = substr_replace($content, $newCode, $start, $length);
      // If anyone has further information or a solution for this, please
      // write a post in the APF-forum. PHP-version: found at 5.3.5  >>>

      $file = new File();
      $file->open($baseFileName)->writeContent($content);
   }

   /**
    * Generates the PHP code for the base object for the object with the given name.
    *
    * @param string $name The object's name.
    *
    * @return string The base object's PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateBaseObjectCode($name) {
      $class = $this->domainObjectsTable[$name]['Class'];
      $className = RootClassLoader::getClassName($class);

      $code = '//<*' . $className . 'Base:start*> DO NOT CHANGE THIS COMMENT!' . self::EOL .
            '/**' . self::EOL .
            ' * Automatically generated BaseObject for ' . $className . '. !!DO NOT CHANGE THIS BASE-CLASS!!' . self::EOL .
            ' * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!' . self::EOL .
            ' * You can change class "' . $className . '" which extends this base-class.' . self::EOL .
            ' */' . self::EOL;

      if (isset($this->domainObjectsTable[$name]['Base'])) {
         $baseClass = $this->domainObjectsTable[$name]['Base']['Class'];
      } else {
         $baseClass = self::$DEFAULT_BASE_CLASS;
      }
      $baseClassName = RootClassLoader::getClassName($baseClass);

      $code .= 'use ' . $baseClass . ';' . self::EOL . self::EOL .
            '/**' . self::EOL .
            ' * This class provides the descriptive getter and setter methods for the "' . $class . '" domain object.' . self::EOL .
            ' */' . self::EOL .
            'abstract class ' . $className . 'Base extends ' . $baseClassName . ' {' . self::EOL . self::EOL;

      // generate properties code
      foreach ($this->mappingTable[$name] as $key => $DUMMY) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $code .= $this->generatePropertiesCode($key);
      }

      // add special property ID
      $code .= '   /**' . self::EOL .
            '    * @var int The value for the object\'s ID.' . self::EOL .
            '    */' . self::EOL .
            '   protected $' . $name . 'ID;' . self::EOL . self::EOL;

      // add special timestamp properties
      $code .= '   /**' . self::EOL .
            '    * @var string The creation timestamp.' . self::EOL .
            '    */' . self::EOL .
            '   protected $CreationTimestamp;' . self::EOL . self::EOL;
      $code .= '   /**' . self::EOL .
            '    * @var string The modification timestamp.' . self::EOL .
            '    */' . self::EOL .
            '   protected $ModificationTimestamp;' . self::EOL . self::EOL;

      // add names of properties for further usage
      $properties = [
            '         \'' . $name . 'ID\'',
            '         \'CreationTimestamp\'',
            '         \'ModificationTimestamp\''
      ];
      foreach ($this->mappingTable[$name] as $key => $value) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $properties[] = '         \'' . $key . '\'';
      }

      $code .= '   protected $propertyNames = [' . self::EOL .
            implode(',' . self::EOL, $properties) . self::EOL .
            '   ];' . self::EOL . self::EOL;

      // add constructor
      $code .= '   public function __construct(string $objectName = null) {' . self::EOL .
            '      parent::__construct(\'' . $name . '\');' . self::EOL .
            '   }' . self::EOL . self::EOL;


      // overwrite generic methods to comply w/ generic GORM concept
      $code .= '   public function getProperty(string $name) {' . self::EOL .
            '      if (in_array($name, $this->propertyNames)) {' . self::EOL .
            '         return $this->$name;' . self::EOL .
            '      }' . self::EOL . self::EOL .
            '      return null;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function setProperty(string $name, $value) {' . self::EOL .
            '      if (in_array($name, $this->propertyNames)) {' . self::EOL .
            '         $this->$name = $value;' . self::EOL .
            '      }' . self::EOL .
            self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function getProperties() {' . self::EOL .
            '      $properties = [];' . self::EOL .
            '      foreach ($this->propertyNames as $name) {' . self::EOL .
            '         if ($this->$name !== null) {' . self::EOL .
            '            $properties[$name] = $this->$name;' . self::EOL .
            '         }' . self::EOL .
            '      }' . self::EOL .
            '      return $properties;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function setProperties(array $properties = []) {' . self::EOL .
            '      foreach ($properties as $key => $value) {' . self::EOL .
            '         if (in_array($key, $this->propertyNames)) {' . self::EOL .
            '            $this->$key = $value;' . self::EOL .
            '         }' . self::EOL .
            '      }' . self::EOL .
            self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function deleteProperty(string $name) {' . self::EOL .
            '      if (in_array($name, $this->propertyNames)) {' . self::EOL .
            '         $this->$name = null;' . self::EOL .
            '      }' . self::EOL .
            self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function setObjectId(int $id) {' . self::EOL .
            '      $this->' . $name . 'ID = $id;' . self::EOL .
            self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL .

            '   public function getObjectId() {' . self::EOL .
            '      return $this->' . $name . 'ID;' . self::EOL .
            '   }' . self::EOL . self::EOL;

      // overwrite sleep method to ensure proper serialization
      $properties = [
            '            \'objectName\'',
            '            \'' . $name . 'ID\'',
            '            \'CreationTimestamp\'',
            '            \'ModificationTimestamp\''
      ];
      foreach ($this->mappingTable[$name] as $key => $DUMMY) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $properties[] = '            \'' . $key . '\'';
      }
      $properties[] = '            \'relatedObjects\'';

      $code .= '   public function __sleep() {' . self::EOL .
            '      return [' . self::EOL .
            implode(',' . self::EOL, $properties) . self::EOL .
            '      ];' . self::EOL .
            '   }' . self::EOL . self::EOL;

      // create getter/setter/delete for all properties
      foreach ($this->mappingTable[$name] as $key => $DUMMY) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $code .= $this->generateGetterCode($key);
         $code .= $this->generateSetterCode($key, $class);
         $code .= $this->generateDeleteCode($key, $class);
      }

      $code .= '}' . self::EOL . self::EOL .
            '// DO NOT CHANGE THIS COMMENT! <*' . $className . 'Base:end*>';

      return $code;
   }

   /**
    * Generates the PHP code for a property with the given name.
    *
    * @param string $name The property's name.
    *
    * @return string The PHP code.
    */
   protected function generatePropertiesCode($name) {
      return '   /**' . self::EOL .
            '    * @var string The value for property "' . $name . '".' . self::EOL .
            '    */' . self::EOL .
            '   protected $' . $name . ';' . self::EOL . self::EOL;
   }

   /**
    * Generates the PHP code for a property's getter with the given name.
    *
    * @param string $name The property's name.
    *
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateGetterCode($name) {
      return '   /**' . self::EOL .
            '    * @return string The value for property "' . $name . '".' . self::EOL .
            '    */' . self::EOL .
            '   public function get' . $name . '() {' . self::EOL .
            '      return $this->getProperty(\'' . $name . '\');' . self::EOL .
            '   }' . self::EOL . self::EOL;
   }

   /**
    * Generates the PHP code for a property's setter with the given name.
    *
    * @param string $name The property's name.
    * @param string $class The name of the class.
    *
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateSetterCode($name, $class) {
      return '   /**' . self::EOL .
            '    * @param string $value The value to set for property "' . $name . '".' . self::EOL .
            '    *' . self::EOL .
            '    * @return ' . RootClassLoader::getClassName($class) . ' The domain object for further usage.' . self::EOL .
            '    */' . self::EOL .
            '   public function set' . $name . '($value) {' . self::EOL .
            '      $this->setProperty(\'' . $name . '\', $value);' . self::EOL . self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL;
   }

   /**
    * Generates the PHP code for a property's delete method with the given name.
    *
    * @param string $name The property's name.
    * @param string $class The name of the class.
    *
    * @return string The PHP code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.09.2011<br />
    */
   protected function generateDeleteCode($name, $class) {
      return '   /**' . self::EOL .
            '    * @return ' . RootClassLoader::getClassName($class) . ' The domain object for further usage.' . self::EOL .
            '    */' . self::EOL .
            '   public function delete' . $name . '() {' . self::EOL .
            '      $this->deleteProperty(\'' . $name . '\');' . self::EOL . self::EOL .
            '      return $this;' . self::EOL .
            '   }' . self::EOL . self::EOL;
   }

   /**
    * @param string $name The domain object descriptor.
    *
    * @return string The namespace of the given domain object name.
    */
   protected function getNamespaceByObjectName($name) {
      return RootClassLoader::getNamespace($this->domainObjectsTable[$name]['Class']);
   }

   /**
    * Creates a new file for each the base class and the DO class with the code for
    * the object with the given name. Will overwrite existing file!
    *
    * @param string $name The object's name.
    * @param string $baseFileName The file name the base class will be written to.
    * @param string $fileName The file name the class will be written to.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    * Version 0.2, 24.06.2014 (ID#194: split base class and DO class into separate files to better support auto loading.)<br />
    */
   protected function createNewServiceObject($name, $baseFileName, $fileName) {

      $namespace = $this->getNamespaceByObjectName($name);
      $path = dirname($fileName);
      if (!file_exists($path)) {
         $folder = new Folder();
         $folder->create($path);
      }

      // create base class file
      $content = '<?php' . self::EOL . 'namespace ' . $namespace . ';'
            . self::EOL . self::EOL . $this->generateBaseObjectCode($name) . self::EOL;
      $baseFile = new File();
      $baseFile->create($baseFileName)->writeContent($content);

      // create class file
      $content = '<?php' . self::EOL . 'namespace ' . $namespace . ';'
            . self::EOL . self::EOL . $this->generateObjectCode($name) . self::EOL;
      $file = new File();
      $file->create($fileName)->writeContent($content);

   }

   /**
    * Generates the code for the object, which extends the base object.
    *
    * @param string $name The object's name.
    *
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateObjectCode($name) {
      $class = $this->domainObjectsTable[$name]['Class'];
      $className = RootClassLoader::getClassName($class);

      return
            '/**' . self::EOL .
            ' * This class represents the "' . $class . '" domain object.' . self::EOL .
            ' * <p/>' . self::EOL .
            ' * Please use this class to add your own functionality.' . self::EOL .
            ' */' . self::EOL .
            'class ' . $className . ' extends ' . $className . 'Base {' . self::EOL .
            self::EOL .
            '   /**' . self::EOL .
            '    * Call the parent\'s constructor because the object name needs to be set.' . self::EOL .
            '    * <p/>' . self::EOL .
            '    * To create an instance of this object, just call' . self::EOL .
            '    * <code>' . self::EOL .
            '    * use ' . $class . ';' . self::EOL .
            '    * $object = new ' . $className . '();' . self::EOL .
            '    * </code>' . self::EOL .
            '    *' . self::EOL .
            '    * @param string $objectName The internal object name of the domain object.' . self::EOL .
            '    */' . self::EOL .
            '   public function __construct(string $objectName = null) {' . self::EOL .
            '      parent::__construct();' . self::EOL .
            '   }' . self::EOL .
            self::EOL .
            '}';
   }

}
