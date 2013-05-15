<?php
namespace APF\modules\genericormapper\data\tools;

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
use APF\core\loader\RootClassLoader;
use APF\modules\genericormapper\data\BaseMapper;
use APF\tools\filesystem\Folder;
use APF\tools\filesystem\File;

/**
 * @package APF\modules\genericormapper\data\tools
 * @class GenericORMapperDomainObjectGenerator
 *
 * Automatically generates DomainObjects for the GenericORMapper
 * which are defined in <em>*_domainobjects.ini</em>.
 *
 * @author Ralf Schubert
 * @version 0.1, 15.01.2011<br />
 */
class GenericORMapperDomainObjectGenerator extends BaseMapper {

   protected static $DEFAULT_BASE_CLASS = 'APF\modules\genericormapper\data\GenericDomainObject';

   /**
    * @public
    *
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
    * @param string $name The name of the domain object class.
    * @return string The name of the corresponding file.
    */
   protected function getFileName($name) {
      $class = $this->domainObjectsTable[$name]['Class'];
      $loader = RootClassLoader::getLoaderByClass($class);
      $vendor = $loader->getVendorName();
      $rootPath = $loader->getRootPath();
      return $rootPath
            // first part of the namespace must be dropped to not double the vendor name
            . '/' . str_replace('\\', '/', str_replace($vendor . '\\', '', $class)) . '.php';
   }

   /**
    * @param string $name The domain object descriptor.
    * @return string The namespace of the given domain object name.
    */
   protected function getNamespaceByObjectName($name) {
      return RootClassLoader::getNamespace($this->domainObjectsTable[$name]['Class']);
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

      $fileName = $this->getFileName($name);

      // check if we need to update an old or create a new definition
      if (file_exists($fileName)) {
         $this->updateServiceObject($name, $fileName);
      } else {
         $this->createNewServiceObject($name, $fileName);
      }
   }

   /**
    * @protected
    *
    * Creates a new file with the code for the service object with the given name.
    * Will overwrite existing file!
    *
    * @param string $name The object's name.
    * @param string $fileName The file name the class will be written to.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function createNewServiceObject($name, $fileName) {
      $namespace = $this->getNamespaceByObjectName($name);
      $content = '<?php
namespace ' . $namespace . ';' . PHP_EOL . PHP_EOL .
            $this->generateBaseObjectCode($name, $namespace) . PHP_EOL . PHP_EOL .
            $this->generateObjectCode($name, $namespace) . PHP_EOL;

      $path = dirname($fileName);
      if (!file_exists($path)) {
         $folder = new Folder();
         $folder->create($path);
      }

      $file = new File();
      $file->create($fileName)->writeContent($content);
   }

   /**
    * Updates an existing base-model from the object with the given name.
    * Will not change anything on the object itself, only the base-model is changed.
    *
    * @param string $name The object's name.
    * @param string $fileName The file name the class will be written to.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    * Version 0.2, 15.05.2013 (Update start- and endtag with classname instead of class [Tobias Lückel|Megger])<br />
    */
   protected function updateServiceObject($name, $fileName) {

      $content = file_get_contents($fileName);
      $newCode = $this->generateBaseObjectCode($name, $this->getNamespaceByObjectName($name));

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
      $file->open($fileName)->writeContent($content);
   }

   /**
    * @protected
    *
    * Generates the PHP code for the base object for the object with the given name.
    *
    * @param string $name The object's name.
    * @param string $namespace The namespace of the class to generate.
    * @return string The base object's PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateBaseObjectCode($name, $namespace) {
      $class = $this->domainObjectsTable[$name]['Class'];
      $className = RootClassLoader::getClassName($class);

      $code = '//<*' . $className . 'Base:start*> DO NOT CHANGE THIS COMMENT!' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Automatically generated BaseObject for ' . $className . '. !!DO NOT CHANGE THIS BASE-CLASS!!' . PHP_EOL .
            ' * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!' . PHP_EOL .
            ' * You can change class "' . $className . '" which extends this base-class.' . PHP_EOL .
            ' */' . PHP_EOL;

      if (isset($this->domainObjectsTable[$name]['Base'])) {
         $baseClass = $this->domainObjectsTable[$name]['Base']['Class'];
      } else {
         $baseClass = self::$DEFAULT_BASE_CLASS;
      }
      $baseClassName = RootClassLoader::getClassName($baseClass);

      $code .= 'use ' . $baseClass . ';' . PHP_EOL . PHP_EOL .
            '/**' . PHP_EOL .
            ' * @package ' . $namespace . PHP_EOL .
            ' * @class ' . $className . 'Base' . PHP_EOL .
            ' * ' . PHP_EOL .
            ' * This class provides the descriptive getter and setter methods for the "' . $class . '" domain object.' . PHP_EOL .
            ' */' . PHP_EOL .
            'abstract class ' . $className . 'Base extends ' . $baseClassName . ' {' . PHP_EOL . PHP_EOL .
            '   public function __construct($objectName = null){' . PHP_EOL .
            '      parent::__construct(\'' . $name . '\');' . PHP_EOL .
            '   }' . PHP_EOL .
            PHP_EOL;

      foreach ($this->mappingTable[$name] as $key => $DUMMY) {
         if ($key === 'ID' || $key === 'Table') {
            continue;
         }
         $code .= $this->generateGetterCode($key);
         $code .= $this->generateSetterCode($key, $class);
         $code .= $this->generateDeleteCode($key, $class);
      }

      // generate getter for the generic elements, too.
      $code .= $this->generateGetterCode('CreationTimestamp');
      $code .= $this->generateGetterCode('ModificationTimestamp');

      $code .= '}' . PHP_EOL .
            PHP_EOL .
            '// DO NOT CHANGE THIS COMMENT! <*' . $className . 'Base:end*>';
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
    * @param string $class The name of the class.
    * @return string The PHP code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.09.2011<br />
    */
   protected function generateDeleteCode($name, $class) {
      return '   /**' . PHP_EOL .
            '    * @return ' . RootClassLoader::getClassName($class) . ' The domain object for further usage.' . PHP_EOL .
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
    * @param string $class The name of the class.
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateSetterCode($name, $class) {
      return '   /**' . PHP_EOL .
            '    * @param string $value The value to set for property "' . $name . '".' . PHP_EOL .
            '    * @return ' . RootClassLoader::getClassName($class) . ' The domain object for further usage.' . PHP_EOL .
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
    * @param string $namespace The namespace of the class to generate.
    * @return string The PHP code.
    *
    * @author Ralf Schubert
    * @version 0.1,  15.01.2011<br />
    */
   protected function generateObjectCode($name, $namespace) {
      $class = $this->domainObjectsTable[$name]['Class'];
      $className = RootClassLoader::getClassName($class);
      return
            '/**' . PHP_EOL .
            ' * @package ' . $namespace . PHP_EOL .
            ' * @class ' . $className . PHP_EOL .
            ' * ' . PHP_EOL .
            ' * This class represents the "' . $class . '" domain object.' . PHP_EOL .
            ' * <p/>' . PHP_EOL .
            ' * Please use this class to add your own functionality.' . PHP_EOL .
            ' */' . PHP_EOL .
            'class ' . $className . ' extends ' . $className . 'Base {' . PHP_EOL .
            PHP_EOL .
            '   /**' . PHP_EOL .
            '    * Call the parent\'s constructor because the object name needs to be set.' . PHP_EOL .
            '    * <p/>' . PHP_EOL .
            '    * To create an instance of this object, just call' . PHP_EOL .
            '    * <code>' . PHP_EOL .
            '    * use ' . $class . ';' . PHP_EOL .
            '    * $object = new ' . $className . '();' . PHP_EOL .
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
