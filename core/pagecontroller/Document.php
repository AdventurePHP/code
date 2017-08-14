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
namespace APF\core\pagecontroller;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\http\mixins\GetRequestResponse;
use APF\core\loader\RootClassLoader;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use Exception;
use InvalidArgumentException;

/**
 * Implements a basic node within the APF DOM tree.
 *
 * @author Christian Schäfer, Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 11.08.2014 (ID#230: added support for DOM node access via dom-id attribute)<br />
 * Version 0.3, 14.10.2015 (ID#265: introduces interface for APF DOM node)<br />
 */
class Document extends APFObject implements DomNode {

   use GetRequestResponse;

   /**
    * Defines the attribute to allow direct access with using <em>Document::getNodeById()</em>.
    *
    * @var string ATTRIBUTE_DOM_ID
    */
   const ATTRIBUTE_DOM_ID = 'dom-id';

   /**
    * Attribute name for service name of document controller
    *
    * @var string CONTROLLER_ATTR_SERVICE_NAME
    */
   const CONTROLLER_ATTR_SERVICE_NAME = 'service';

   /**
    * Attribute name for service namespace of document controller
    *
    * @var string CONTROLLER_ATTR_SERVICE_NAMESPACE
    */
   const CONTROLLER_ATTR_SERVICE_NAMESPACE = 'namespace';

   /**
    * Attribute name for fully qualified class name of document controller
    *
    * @var string CONTROLLER_ATTR_CLASS
    */
   const CONTROLLER_ATTR_CLASS = 'class';

   /**
    * The maximum number of parser loops taken to analyze tags within a document. Used to protect against infinite loops.
    *
    * @var int $maxParserLoops
    */
   public static $maxParserLoops = 500;

   /**
    * List of known tags the APF parser uses to create tag instances during analysis phase.
    *
    * @var string[] $knownTags
    */
   protected static $knownTags = [];

   /**
    * List of known expressions used to process APF templates.
    *
    * @var TemplateExpression[]
    */
   protected static $knownExpressions = [];

   /**
    * @var Document[] The list of documents indexed by the <em>dom-id</em> attribute.
    */
   protected static $documentIndex = [];

   /**
    * Unique object identifier.
    *
    * @var string $objectId
    */
   protected $objectId = null;

   /**
    * Reference to the parent object.
    *
    * @var DomNode $parentObject
    */
   protected $parentObject = null;

   /**
    * The attributes of an object (merely the XML tag attributes).
    *
    * @var string[] $attributes
    */
   protected $attributes = [];

   /**
    * The content of the tag. Example:
    * <pre>&lt;foo:bar&gt;This is the content of the tag.&lt;/foo:bar&gt;</pre>
    *
    * @var string $content
    */
   protected $content;

   /**
    * The instance of the document controller to use at transformation time.
    *
    * @var DocumentController $documentController
    */
   protected $documentController = null;

   /**
    * List of the children of the current object.
    *
    * @var DomNode[] $children
    */
   protected $children = [];

   /**
    * Data attributes of the current DOM document (similar to Java Script).
    *
    * @var string[][] $data
    */
   protected $data = [];

   /**
    * List of known tags for a dedicated DOM node the APF parser uses to create tag instances during analysis phase.
    *
    * @var string[] $knownInstanceTags
    */
   protected $knownInstanceTags = [];

   /**
    * ID#287: Stores the place holders set for the current document. As this property is a simple
    * associative array, access is much faster than iterating over the document tree and finding
    * place holders among the list of children.
    *
    * @var string[][] $placeHolders
    */
   protected $placeHolders = [];

   /**
    * Default constructor of an APF document. The APF DOM tree is constructed by objects derived from this class.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
    * Version 0.3, 11.02.2012 (Added html:getstring tag as known tag (refactoring!))<br />
    * Version 0.4, 27.06.2014 (Removed constructor content due to new APF parser allows static tag registration)<br />
    */
   public function __construct() {
   }

   public static function addTagLib($class, $prefix, $name) {
      self::$knownTags[$prefix . ':' . $name] = $class;
   }

   public static function addTemplateExpression($expression) {
      self::$knownExpressions[] = $expression;
   }

   public static function clearTemplateExpressions() {
      self::$knownExpressions = [];
   }

   public function hasAttribute($name) {
      return isset($this->attributes[$name]);
   }

   public function getRequiredAttribute($name) {

      $attribute = $this->getAttribute($name);
      if ($attribute === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getRequiredAttribute()] Attribute "' . $name
               . '" has not been defined but is mandatory! Please re-check your template setup.');
      }

      return $attribute;
   }

   public function getAttribute($name, $default = null) {
      return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
   }

   public function &setAttribute($name, $value) {
      $this->attributes[$name] = $value;

      return $this;
   }

   public function getAttributes() {
      return $this->attributes;
   }

   public function &setAttributes(array $attributes = []) {
      if (count($attributes) > 0) {
         if (!is_array($this->attributes)) {
            $this->attributes = [];
         }
         $this->attributes = array_merge($this->attributes, $attributes);
      }

      return $this;
   }

   public function &deleteAttribute($name) {
      unset($this->attributes[$name]);

      return $this;
   }

   public function &addAttribute($name, $value, $glue = '') {
      if (isset($this->attributes[$name])) {
         if (empty($this->attributes[$name])) { // avoid e.g. starting blanks with CSS classes
            $this->attributes[$name] .= $value;
         } else {
            $this->attributes[$name] .= $glue . $value;
         }
      } else {
         $this->attributes[$name] = $value;
      }

      return $this;
   }

   public function &getChildNodeIfExists($attributeName, $value, $tagLibClass) {
      try {
         return $this->getChildNode($attributeName, $value, $tagLibClass);
      } catch (InvalidArgumentException $e) {
         $null = null;

         return $null;
      }
   }

   public function &getChildNode($attributeName, $value, $tagLibClass) {
      foreach ($this->children as &$child) {
         /* @var $child DomNode */
         if ($child instanceof $tagLibClass
               && $child->getAttribute($attributeName) == $value
         ) {
            return $child;
         }
      }
      throw new InvalidArgumentException('[' . get_class($this) . '::getChildNode()] No child node with type "'
            . $tagLibClass . '" and attribute selector ' . $attributeName . '="' . $value . '" composed in current '
            . 'document!', E_USER_ERROR);
   }

   public function &getChildren() {
      return $this->children;
   }

   public function &getChildNodes($attributeName, $value, $tagLibClass) {
      $result = [];

      foreach ($this->children as &$child) {
         /* @var $child DomNode */
         if ($child instanceof $tagLibClass
               && $child->getAttribute($attributeName) == $value
         ) {
            $result[] = &$child;
         }
      }
      if (count($result) == 0) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getChildNodes()] No child nodes with type "'
               . $tagLibClass . '" and attribute selector ' . $attributeName . '="' . $value . '" composed in current '
               . 'document!', E_USER_ERROR);
      } else {
         return $result;
      }
   }

   public function getPlaceHolders() {
      return $this->placeHolders;
   }

   public function &setPlaceHolders(array $placeHolderValues, $append = false) {
      foreach ($placeHolderValues as $key => $value) {
         $this->setPlaceHolder($key, $value, $append);
      }

      return $this;
   }

   public function &setPlaceHolder($name, $value, $append = false) {
      // false handled first, since most usages don't append --> slightly faster
      if ($append === false || !isset($this->placeHolders[$name])) {
         $this->placeHolders[$name] = $value;
      } else {
         $this->placeHolders[$name] = $this->placeHolders[$name] . $value;
      }

      return $this;
   }

   public function getPlaceHolder($name, $default = null) {
      return isset($this->placeHolders[$name]) ? $this->placeHolders[$name] : $default;
   }

   public function getData($name, $default = null) {
      return isset($this->data[$name]) ? $this->data[$name] : $default;
   }

   public function &setData($name, $data) {
      $this->data[$name] = $data;

      return $this;
   }

   public function &clearPlaceHolders() {
      $this->placeHolders = [];

      return $this;
   }

   public function &getDocumentController() {
      return $this->documentController;
   }

   public function addInstanceTagLib($class, $prefix, $name) {
      $this->knownInstanceTags[$prefix . ':' . $name] = $class;
   }

   /**
    * Loads the initial template for the initial document. Can also be used to load
    * content from files within sub taglibs.
    *
    * @param string $namespace Namespace of the initial templates.
    * @param string $design Name of the initial template.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 15.01.2007 (Now document controller are extracted first)<br />
    */
   public function loadDesign($namespace, $design) {

      // read the content of the template
      $this->loadContentFromFile($namespace, $design);

      // analyze document controller definition
      $this->extractDocumentController();

      // parse known tags
      $this->extractTagLibTags();
   }

   /**
    * Loads a template from a given namespace. The convention says, that the name of the template
    * is equal to the file body plus the ".html" extensions. The namespace is a APF namespace.
    *
    * @param string $namespace The namespace of the template.
    * @param string $name The name of the template (a.k.a. design).
    *
    * @throws IncludeException In case the template file is not found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 01.01.2007<br />
    * Version 0.3, 03.11.2008 (Added code of the responsible template to the error message to ease debugging)<br />
    * Version 0.4, 31.10.2012 (Introduced the append feature to be able to preserve tag content created/added prior calling this method)<br />
    */
   protected function loadContentFromFile($namespace, $name) {

      // sanitize the design name to avoid xss or code injection
      $name = preg_replace('/[^A-Za-z0-9\-_]/', '', $name);

      try {
         $file = $this->getTemplateFilePath($namespace, $name);
      } catch (Exception $e) {
         // rethrow exception with meaningful content (class loader exception would be too misleading)
         throw new IncludeException('[' . get_class($this) . '::loadContentFromFile()] Template "' . $name
               . '" not existent in namespace "' . $namespace . '"!', E_USER_ERROR, $e);
      }

      if (file_exists($file)) {
         // Append the content to the current content buffer. In case the existing content should be
         // overwritten by this method call, please clear it using $this->content = '' prior calling
         // this method (normally not necessary).
         $this->content .= file_get_contents($file);
      } else {
         // get template code from parent object, if the parent exists
         $code = '';
         if ($this->getParentObject() !== null) {
            $code = ' Please check your template code (' . $this->getParentObject()->getContent() . ').';
         }

         throw new IncludeException('[' . get_class($this) . '::loadContentFromFile()] Template "' . $name
               . '" not existent in namespace "' . $namespace . '" (file: "' . $file . '")!' . $code, E_USER_ERROR);

      }
   }

   /**
    * Generates the file path of the desired template.
    * <p/>
    * Overwriting this method allows you to use a different algorithm of creating the
    * path within your custom tag implementations.
    *
    * @param string $namespace The namespace of the template.
    * @param string $name The (file) name of the template.
    *
    * @return string The template file path of the referred APF template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2012<br />
    */
   protected function getTemplateFilePath($namespace, $name) {

      // gather namespace and full(!) template name and use class loader to determine root path

      // ID#152: check whether we have a vendor-only namespace declaration to support
      // Document::getTemplateFileName('APF', 'foo') calls
      $vendorOnly = RootClassLoader::isVendorOnlyNamespace($namespace);
      if ($vendorOnly === true) {
         $classLoader = RootClassLoader::getLoaderByVendor($namespace);
      } else {
         $classLoader = RootClassLoader::getLoaderByNamespace($namespace);
      }

      $rootPath = $classLoader->getRootPath();

      if ($vendorOnly === true) {
         return $rootPath . '/' . $name . '.html';
      } else {
         $vendor = $classLoader->getVendorName();

         return $rootPath . '/' . str_replace('\\', '/', str_replace($vendor . '\\', '', $namespace)) . '/' . $name . '.html';
      }
   }

   public function &getParentObject() {
      return $this->parentObject;
   }

   public function setParentObject(DomNode &$parentObject) {
      $this->parentObject = &$parentObject;

      return $this;
   }

   /**
    * Initializes the document controller class, that is executed at APF DOM node
    * transformation time.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 15.12.2009 (Added check for non existing class attribute)<br />
    * Version 0.2, 28.07.2013 Jan Wiese (Introduced di-service support for document controllers. Moved controller creation here)<br />
    */
   protected function extractDocumentController() {

      // define start and end tag
      $controllerStartTag = '<@controller';
      $controllerEndTag = '@>';

      if (strpos($this->content, $controllerStartTag) === false) {
         // no controller tag found
         return;
      }

      $tagStartPos = strpos($this->content, $controllerStartTag);
      $tagEndPos = strpos($this->content, $controllerEndTag, $tagStartPos);
      $controllerTag = substr($this->content, $tagStartPos + 12, ($tagEndPos - $tagStartPos) - 12); // 12 for <@controller
      $controllerAttributes = XmlParser::getAttributesFromString($controllerTag);

      if (isset($controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAMESPACE])
            && isset($controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAME])
      ) {

         try {
            $docCon = $this->getDIServiceObject(
                  $controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAMESPACE],
                  $controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAME]
            );
         } catch (Exception $e) {
            throw new InvalidArgumentException('[' . get_class($this) . '::extractDocumentController()] Given document'
                  . ' controller  could not be created using the DIServiceManager. Message: '
                  . $e->getMessage(), $e->getCode());
         }

      } elseif (isset($controllerAttributes[self::CONTROLLER_ATTR_CLASS])) {

         // class is loaded via the class loader lazily
         $docCon = new $controllerAttributes[self::CONTROLLER_ATTR_CLASS];
         /* @var $docCon DocumentController */

         // inject APF core attributes to guarantee native environment
         $docCon->setContext($this->getContext());
         $docCon->setLanguage($this->getLanguage());

      } else {

         // no valid document controller definition given, thus interrupt execution here
         throw new ParserException('[' . get_class($this) . '::extractDocumentController()] Document '
               . 'controller specification does not contain a valid controller class or service definition. '
               . 'Please double check the template code and consult the documentation. '
               . 'Template code: ' . $this->getContent());

      }

      $this->documentController = $docCon;

      // inject this document to be able to work on the DOM
      $this->documentController->setDocument($this);

      // remove definition from content to be not displayed
      $this->content = substr_replace($this->content, '', $tagStartPos, ($tagEndPos - $tagStartPos) + 2); // for @>
   }

   public function getContent() {
      return $this->content;
   }

   public function setContent($content) {
      $this->content = $content;

      return $this;
   }

   /**
    * Parses the content of the current APF DOM node. Extracts all tags contained in the current
    * document content. Each tag is converted into a child Document of the current tree element.
    * The tag definition place is remembered by a marker tag using the internal id of the DOM node.
    * <p/>
    * Since release 1.17 nested tag structures are supported. This means, that the APF parser
    * is able to handle symmetric structures like this:
    * <code>
    * <foo:bar>
    *    <foo:bar>
    *    </foo:bar>
    * </foo:bar>
    * </code>
    * Besides, the APF parser is able to handle asymmetric structures like
    * <code>
    * <foo:bar />
    * <foo:bar>
    *    <foo:bar>
    *       <foo:bar />
    *    </foo:bar>
    * </foo:bar>
    * </code>
    * Please note that using nested structures must be supported by the tag implementations.
    * <p/>
    * The APF parser is able to handle nested tag structures with self-containing tags (directly of
    * across multiple hierarchies) as of version 2.2. Thus, you can re-use tags across any hierarchy
    * with the same prefix and name at your convenience or defined tags with different prefix and/or
    * name as desired (e.g. using the same implementation).
    * <p/>
    * To protect against infinite loops with broken tag structures the parser uses <em>self::$maxParserLoops</em>
    * to limit the parser cycles to a configurable amount of times. In case your project requires a
    * higher value, please set <em>Document::$maxParserLoops</em> to an appropriate value.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 21.01.2007 (Bug-fix: a mixture of self- and exclusively closing tags lead to wrong parsing)<br />
    * Version 0.3, 31.01.2007 (Added context injection)<br />
    * Version 0.4, 09.04.2007 (Removed double attributes setting, added language injection)<br />
    * Version 0.5, 02.04.2008 (Bug-fix: the token is now displayed in the HTML error page)<br />
    * Version 0.6, 06.06.2009 (Improvement: content is not copied during parsing any more)<br />
    * Version 0.7, 30.12.2009 (Introduced benchmark marks for the onParseTime() event.)<br />
    * Version 0.8, 25.01.2013 (Re-writing of the tag parser to support nested tags with the same tag prefix and name)<br />
    * Version 0.9, 20.06.2014 (Re-writing entire parser for 2.2 to support nested, self-containing tags across multiple hierarchies)<br />
    */
   protected function extractTagLibTags() {

      /**
       * The list of parsed tags within the current document.
       *
       * @var array
       */
      $tags = [];

      /**
       * The number of tokens within the current document (introduced also for performance reasons).
       *
       * @var int
       */
      $count = 0;

      /* @var $t BenchmarkTimer */
      $t = Singleton::getInstance(BenchmarkTimer::class);

      $benchId = '(' . get_class($this) . ') ' . $this->getObjectId() . '::onParseTime()';
      $t->start($benchId);

      /**
       * Position pointer for tag search. Introduced for performance reasons to skip stuff that we already searched in.
       *
       * @var int
       */
      $offset = 0;

      while (($colon = strpos($this->content, ':', $offset)) !== false) {

         // start tag searching goes as follows
         // - tag sub string starting at the current position of the colon with 12 chars
         // search for last < in that sub-string
         // tag prefixes must not be longer than 10 characters - for security reasons
         $area = $colon >= 12 ? 12 : $colon; // in case the tag starts right at the beginning, the area to search gets smaller!
         $start = strrpos(substr($this->content, $colon - $area, $area), '<');

         // no open tag found --> continue!
         if ($start === false) {
            $offset = $colon + 1;
            continue;
         }

         // do offset correction due to internal
         $start = $colon - $area + $start; // $area (12 by default) for the sub-string part

         // Avoid issue with "<li>FOO:" constructs that will be recognized as tag,
         // AND
         // avoid issues with "<a href="/?:action=logout">Logout</a>" structures
         // to be recognized as a tag (see ID#266).
         $prefix = substr($this->content, $start, $colon - $start);
         if (strpos($prefix, '>') !== false || strpos($prefix, '"') !== false) {
            $offset = $colon + 1;
            continue;
         }

         // avoid issues with parsing HTML comments including colons (e.g. <!-- foo:bar -->)
         if (substr($this->content, $start, 4) == '<!--') {
            $offset = $colon + 1;
            continue;
         }

         // find out whether we have an opening or closing tag
         $end = strpos($this->content, '>', $colon + 1);
         if ($end === false) {
            throw new ParserException('[Document::extractTagLibTags()] Error while parsing: "'
                  . $this->content . '". Invalid markup found. Please check your template code!', E_USER_ERROR);
         }

         if (substr($this->content, $start, 2) !== '</') {

            // ID#253: In case we are using an extended templating expression within a tag attribute
            // (e.g. "model[0]->getFoo()") the ending ">" is contained within the attribute and thus the first
            // strpos() produces wrong results. For this reason, search for the last ">" with an even number of
            // quotes in the string to fix this.
            $parserLoops = 0;
            while (substr_count(substr($this->content, $start, $end + 1 - $start), '"') % 2 !== 0) {

               $parserLoops++;

               if ($parserLoops > XmlParser::$maxParserLoops) {
                  throw new ParserException('[Document::extractTagLibTags()] Error while parsing: "'
                        . $this->content . '". Maximum number of loops ("' . XmlParser::$maxParserLoops
                        . '") exceeded!', E_USER_ERROR);
               }

               $end = strpos($this->content, '>', $end + 1);
               if ($end === false) {
                  throw new ParserException('[Document::extractTagLibTags()] Error while parsing: "'
                        . $this->content . '". Invalid markup found. Please check your template code!', E_USER_ERROR);
               }
            }

            // Determine whether we have a self-closing tag or not. This is important
            // within the following lines how to handle the tag.
            if (substr($this->content, $end - 1, 1) == '/') {
               $selfClosing = true;
            } else {
               $selfClosing = false;
            }

            // s = tag start position
            $tags[$count]['s'] = $start;

            // p = tag prefix (e.g. "foo" with tag "<foo:bar />")
            $tags[$count]['p'] = substr($this->content, $start + 1, $colon - $start - 1);

            // search for next space to gather tag name
            $space = strpos($this->content, ' ', $colon);

            // in case we encounter tag definitions w/o spaces, reset the space position
            // to the next ">".
            if ($space === false) {
               if ($selfClosing === true) {
                  $space = $end - 1;
               } else {
                  $space = $end;
               }
            }

            // in case we encounter a closing bracket first, this may be due to
            // a tag without attributes <foo:bar>...
            if ($end < $space) {
               // reset space indicator to bracket position to support opening
               // tags without attributes (<foo:bar> </foo:bar>)
               if ($selfClosing) {
                  // Correct position by minus one due to "/>" at the end.
                  // This only holds true for "<foo:bar/>" tags (no space after tag name).
                  $space = $end - 1;
               } else {
                  $space = $end;
               }
            }

            // n = tag name (e.g. "bar" with tag "<foo:bar />")
            $tags[$count]['n'] = trim(substr($this->content, $colon + 1, $space - $colon - 1)); // instead of trim, maybe search for a new line instead

            // ID#300: detect new lines in tag definition and throw exception to
            // avoid application crushing w/ max execution time exceeded!
            if (strpos($tags[$count]['n'], "\n") !== false) {
               throw new ParserException('Tag definition must not contain line breaks at "<' . $tags[$count]['p'] . ':' . $tags[$count]['n']
                     . '"! Template code: ' . htmlentities($this->content));
            }

            // assemble the token to allow easier closing tag search
            $token = $tags[$count]['p'] . ':' . $tags[$count]['n'];

            if ($selfClosing === true) {
               // e = tag end position
               $tags[$count]['e'] = $end + 1;
               // set offset to end of last tag before starting with new one
               $offset = $end + 1;
            } else {
               // if we've got an opening/not self-closing tag, let's search for our pendent closing tag
               $tokenLength = strlen($token);
               $startTagLength = $tokenLength + 1; // 1 for "<"
               $endTagLength = $tokenLength + 3; // 3 for "</" plus ">"

               // initialize the token position cursor
               $tokenPos = $space; // for performance reasons: start right after the space to save some chars
               $tagEndPos = $end;

               $openingTagCount = 1; // for performance reasons: start at 1 and skip current tag position to save some chars
               $closingTagCount = 0;

               while (true) {
                  $tokenPos = strpos($this->content, $token, $tokenPos);
                  if ($tokenPos === false) {
                     break;
                  }

                  if (substr($this->content, $tokenPos - 1, 1) == '<') {

                     // Check for explicitly closing tag, because self-closing tags
                     // do not count searching for a symmetric tag hierarchy included
                     // in another tag structure.
                     $bracket = strpos($this->content, '>', $tokenPos + $tokenLength);
                     if (substr($this->content, $bracket - 1, 1) !== '/') {
                        $openingTagCount++;
                     }

                  } else if (substr($this->content, $tokenPos - 1, 1) == '/') {
                     // ID#98: Check for explicit closing tag expressed by "</$token>" instead of relying
                     // on the previous check. Otherwise, an occurrence of "$token" solely will lead to
                     // a match for a closing tag which in fact is just an occurrence of the token.
                     $closingTagCount++;
                  }

                  // In case we have passed the first tag occurrence let's look for a symmetric
                  // tag structure. This check enables nesting tag structures with the same
                  // tag prefix and name.
                  if ($openingTagCount > 0 && $openingTagCount == $closingTagCount) {
                     $tagEndPos = $tokenPos - 2;
                     break;
                  }

                  // Shift cursor to start search after current token position to recursively
                  // search for the last symmetric end tag.
                  $tokenPos = $tokenPos + $startTagLength;
               }

               // e = tag end position
               $tags[$count]['e'] = $tagEndPos + $endTagLength;

               // set offset to end of last tag before starting with new one to skip already analyzed parts
               $offset = $tags[$count]['e'];
            }

         }

         $count++;
      }

      // Offset correction necessary, since previously gathered tag positions are manipulated
      // during building-up the tag markers!
      $offsetCorrection = 0;

      // extract content and loop into recursion ...
      for ($i = 0; $i < $count; $i++) {

         $tagStringLength = $tags[$i]['e'] - $tags[$i]['s'];

         $attributes = XmlParser::getTagAttributes(
               $tags[$i]['p'],
               $tags[$i]['n'],
               substr($this->content, $tags[$i]['s'] - $offsetCorrection, $tagStringLength)
         );

         // initialize object id, that is used to reference the object
         // within the APF DOM tree and to provide a unique key for the
         // children index.
         $objectId = XmlParser::generateUniqID();

         // class is loaded by the class loader lazily
         $class = $this->getTagLibClass($tags[$i]['p'], $tags[$i]['n']);
         if ($class === null) {
            throw new ParserException('No tag definition found for prefix "' . $tags[$i]['p'] . '" and name "' . $tags[$i]['n']
                  . '" in document with type "' . get_class($this) . '"! Template code: ' . htmlentities($this->content));
         }

         $this->children[$objectId] = new $class();
         $this->children[$objectId]->setObjectId($objectId);

         // inject context of the parent object
         $this->children[$objectId]->setContext($this->getContext());

         // inject language of the parent object
         $this->children[$objectId]->setLanguage($this->getLanguage());

         // ID#230: add nodes defining the "dom-id" attribute to the index to allow easy access via getNodeById()
         if (isset($attributes['attributes'][self::ATTRIBUTE_DOM_ID])) {
            self::$documentIndex[$attributes['attributes'][self::ATTRIBUTE_DOM_ID]] = &$this->children[$objectId];
            unset($attributes['attributes'][self::ATTRIBUTE_DOM_ID]);
         }

         // add the tag's attributes
         $this->children[$objectId]->setAttributes($attributes['attributes']);

         // replace the position of the taglib with a place holder
         // token string: <$objectId />.
         // this needs to be done, to be able to place the content of the
         // transformed tags at transformation time correctly
         $this->content = substr_replace($this->content, '<' . $objectId . ' />', $tags[$i]['s'] - $offsetCorrection, $tagStringLength);

         // advertise the parent object
         $this->children[$objectId]->setParentObject($this);

         // add the content to the current APF DOM node
         $this->children[$objectId]->setContent($attributes['content']);

         // call onParseTime() to enable the taglib to initialize itself
         $this->children[$objectId]->onParseTime();

         // correct parser offset to correctly place marker tags at the next parsing loop
         // this is due to string replacement changes the initially gathered values
         $offsetCorrection += $tagStringLength - 36;

      }

      $t->stop($benchId);

      // call onAfterAppend() on each child to enable the taglib to interact with
      // other APF DOM nodes to do extended initialization.
      if (count($this->children) > 0) {

         $benchId = '(' . get_class($this) . ') ' . $this->getObjectId() . '::children[]::onAfterAppend()';
         $t->start($benchId);

         foreach ($this->children as &$child) {
            $child->onAfterAppend();
         }

         $t->stop($benchId);
      }

   }

   public function getObjectId() {
      return $this->objectId;
   }

   public function setObjectId($objectId) {
      $this->objectId = $objectId;

      return $this;
   }

   /**
    * Returns the name of the tag implementation according to the given tag prefix and name.
    *
    * @param string $prefix The tag prefix.
    * @param string $name The tag name.
    *
    * @return null|string The name of the tag implementation or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2014<br />
    * Version 0.2, 20.06.2014 (ID#186: added overriding mechanism on instance basis)<br />
    * Version 0.3, 11.07.2014 (Removed TagLib to gain performance and simplify API)<br />
    */
   protected function getTagLibClass($prefix, $name) {

      // First, look at the list of tags registered for the current
      // instance to allow overriding on an instance basis.
      if (isset($this->knownInstanceTags[$prefix . ':' . $name])) {
         return $this->knownInstanceTags[$prefix . ':' . $name];
      }

      // Second, deliver tag implementation from global store.
      if (isset(self::$knownTags[$prefix . ':' . $name])) {
         return self::$knownTags[$prefix . ':' . $name];
      }

      return null;
   }

   public function onParseTime() {
   }

   public function onAfterAppend() {
      // ID#191: extract "static" expressions (e.g. place holders)
      $this->extractExpressionTags();
   }

   /**
    * Parses the content of the current APF DOM node. Extracts all dynamic expression statements
    * that are shortcuts on certain tags (e.g. place holders or dynamic access on DOM node data).
    * <p/>
    * The current implementation is able to handle place holders such as <em>${foo}</em> as well
    * as dynamic expressions like <em>${foo[2]->getBar()->getBaz()}</em>. These are parsed and
    * as a result instances of <em>PlaceHolderTag</em> or <em>ExpressionEvaluationTag</em> are
    * added to the DOM node to be accessed as you are used to for other DOM nodes.
    * <p/>
    * To protect against infinite loops with broken expression statements the parser uses <em>self::$maxParserLoops</em>
    * to limit the parser cycles to a configurable amount of times. In case your project requires a
    * higher value, please set <em>Document::$maxParserLoops</em> to an appropriate value.
    *
    * @throws ParserException In case of incorrect expression statements or exceeding the configured amount of parser loops.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.01.2014<br />
    */
   protected function extractExpressionTags() {

      $startToken = '${';
      $endToken = '}';

      $loops = 0;
      $offset = 0;

      while (true) {

         if ($loops > self::$maxParserLoops) {
            throw new ParserException('[' . get_class($this) . '::extractExpressionTags()] Maximum numbers of parsing loops reached!', E_USER_ERROR);
         }

         $start = strpos($this->content, $startToken, $offset);
         if ($start === false) {
            break;
         }

         $end = strpos($this->content, $endToken, $start);
         if ($end === false) {
            throw new ParserException('[' . get_class($this) . '::extractExpressionTags()] No closing marker "'
                  . $endToken . '" found for advanced place holder declaration. Tag string: '
                  . htmlentities($this->content), E_USER_ERROR);
            break;
         }

         $token = substr($this->content, $start + 2, $end - $start - 2);

         // create APF node to feel like being created during onParseTime()
         $objectId = XmlParser::generateUniqID();

         /* @var $object DomNode */
         $object = null;

         foreach (self::$knownExpressions as $expression) {
            if ($expression::applies($token)) {
               $object = $expression::getDocument($token);
               break;
            }
         }

         if ($object === null) {
            throw new ParserException('[' . get_class($this) . '::extractExpressionTags()] No expression definition found for token "'
                  . $token . '"! Please check your template code and/or project setup.', E_USER_ERROR);
         }

         $this->children[$objectId] = $object;
         $this->children[$objectId]->setObjectId($objectId);
         $this->children[$objectId]->setContext($this->getContext());
         $this->children[$objectId]->setLanguage($this->getLanguage());

         $this->children[$objectId]->setParentObject($this);

         // add APF parser marker to allow content to be placed appropriately
         $this->content = str_replace($startToken . $token . $endToken, '<' . $objectId . ' />', $this->content);

         // re-adjust offset for performance reasons
         $offset = $start + strlen($objectId) + 3;

         $this->children[$objectId]->onParseTime();
         $this->children[$objectId]->onAfterAppend();

         $loops++;

      }

   }

   public function &getNodeByIdIfExists($id) {
      try {
         return $this->getNodeById($id);
      } catch (InvalidArgumentException $e) {
         $null = null;

         return $null;
      }
   }

   public function &getNodeById($id) {
      if (isset(self::$documentIndex[$id])) {
         return self::$documentIndex[$id];
      }
      throw new InvalidArgumentException('Document with DOM id "' . $id . '" not found. '
            . 'Please review your template setup and/or controller code!');
   }

   public function transform() {

      $t = Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('(' . get_class($this) . ') ' . $this->getObjectId() . '::transform()');

      // create copy, to preserve it!
      $content = $this->content;

      // execute the document controller if applicable
      if ($this->documentController instanceof DocumentController) {

         // start benchmark timer
         $id = '(' . get_class($this->documentController) . ') ' . (XmlParser::generateUniqID()) . '::transformContent()';
         $t->start($id);

         // execute the document controller by using a standard method
         $this->documentController->transformContent();

         $t->stop($id);
      }

      // transform child nodes and replace XML marker to place the output at the right position
      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {
            $content = str_replace('<' . $child->getObjectId() . ' />', $child->transform(), $content);
         }
      }

      $t->stop('(' . get_class($this) . ') ' . $this->getObjectId() . '::transform()');

      return $content;
   }

   /**
    * Creates a string representation of the given attributes list, using a
    * white list to especially include attributes.
    *
    * @param array $attributes The list of attributes to convert to an xml string.
    * @param array $whiteList The list of attributes, the string may contain.
    *
    * @return string The xml attributes string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010 (Replaced old implementation with the white list feature.)<br />
    * Version 0.2, 27.11.2013 (Added default data-* attribute support to ease white list maintenance)<br />
    */
   protected function getAttributesAsString(array $attributes, array $whiteList = []) {

      $attributeParts = [];

      // process white list entries only, when attribute is given
      // code duplication is done here due to performance reasons!!!
      $charset = Registry::retrieve('APF\core', 'Charset');
      if (count($whiteList) > 0) {
         foreach ($attributes as $name => $value) {
            // allow "data-*" attributes by default to not deal with complicated white list configuration
            if (strpos($name, 'data-') !== false || in_array($name, $whiteList)) {
               $attributeParts[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
            }
         }
      } else {
         foreach ($attributes as $name => $value) {
            $attributeParts[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
         }
      }

      return implode(' ', $attributeParts);
   }

   /**
    * Convenience method to transform the current node's children within the current node's content
    * buffer (<em>$this->content</em>).
    * <p/>
    * In case you intend to preserve the current node's content you may want to use the
    * <em>transformChildrenAndPreserveContent()</em> method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.10.2012<br />
    */
   protected function transformChildren() {
      foreach ($this->children as &$child) {
         $this->content = str_replace(
               '<' . $child->getObjectId() . ' />', $child->transform(), $this->content
         );
      }
   }

   /**
    * Convenience method to transform the current node's children and return the result of the
    * transformation. Preserves the current node's internal content buffer (<em>$this->content</em>)
    * to allow further transformations.
    *
    * @return string The current node's transformed content.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.10.2012<br />
    */
   protected function transformChildrenAndPreserveContent() {
      $content = $this->getContent();
      foreach ($this->children as &$child) {
         $content = str_replace(
               '<' . $child->getObjectId() . ' />', $child->transform(), $content
         );
      }

      return $content;
   }

   /**
    * Convenience method to remove the child tag marker within the current node's content
    * buffer (<em>$this->content</em>). Can be used in case the child nodes should be
    * removed from the output.
    * <p/>
    * In case you intend to preserve the current node's content you may want to use the
    * <em>transformChildrenAsEmptyAndPreserveContent()</em> method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.10.2012<br />
    */
   protected function transformChildrenAsEmpty() {
      foreach ($this->children as &$child) {
         $this->content = str_replace('<' . $child->getObjectId() . ' />', '', $this->content);
      }
   }

   /**
    * Convenience method to transform the remove the child tag marker and return the result of the
    * transformation. Preserves the current node's internal content buffer (<em>$this->content</em>)
    * to allow further transformations.
    *
    * @return string
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.10.2012<br />
    */
   protected function transformChildrenAsEmptyAndPreserveContent() {
      $content = $this->getContent();
      foreach ($this->children as &$child) {
         $content = str_replace('<' . $child->getObjectId() . ' />', '', $content);
      }

      return $content;
   }

}
