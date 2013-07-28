<?php
namespace APF\core\pagecontroller;

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
use APF\core\configuration\ConfigurationException;
use APF\core\loader\RootClassLoader;
use APF\core\configuration\Configuration;
use APF\core\logging\Logger;
use APF\core\logging\LogEntry;
use APF\core\logging\SimpleLogEntry;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;

use APF\core\service\APFDIService;
use APF\core\service\ServiceManager;
use APF\core\service\DIServiceManager;
use APF\core\configuration\ConfigurationManager;
use APF\core\benchmark\BenchmarkTimer;

use APF\core\service\APFService;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\html\taglib\HtmlIteratorTag;

/**
 * @package APF\core\pagecontroller
 * @class IncludeException
 *
 * This exception represents an error loading resources (modules,
 * templates, ...) within the page controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.03.2010<br />
 */
class IncludeException extends \Exception {
}

/**
 * @package APF\core\pagecontroller
 * @function printObject
 * @see http://php.net/print_r
 *
 * Creates a print_r() output of the given object, array, string or integer.
 *
 * @param object|array $o The object/array to display.
 * @param boolean $transformHtml In case the HTML characters should be escaped (true) or not (false).
 * @return string The object's string representation.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 04.02.3006<br />
 * Version 0.2, 23.04.2006 (The output is now returned instead of printed directly)<br />
 */
function printObject($o, $transformHtml = false) {

   $buffer = (string)'';
   $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
   $buffer .= "<br />\n";
   $buffer .= "<strong>\n";
   $buffer .= "Output of printObject():\n";
   $buffer .= "</strong>\n";
   $buffer .= "<br />\n";
   $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
   $buffer .= "\n<pre>";

   if ($transformHtml == true) {
      $buffer .= htmlentities(print_r($o, true), null, Registry::retrieve('APF\core', 'Charset'));
   } else {
      $buffer .= print_R($o, true);
   }

   $buffer .= "</pre>\n";
   $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
   $buffer .= "<br />\n";
   $buffer .= "<br />\n";

   return $buffer;
}

/**
 * @package APF\core\pagecontroller
 * @class ParserException
 *
 * Represents a APF parser exception.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.03.2010<br />
 */
class ParserException extends \Exception {
}

/**
 * @package APF\core\pagecontroller
 * @class XmlParser
 *
 * Static parser for XML / XSL Strings.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 22.12.2006<br />
 */
final class XmlParser {

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Extracts attributes and content from an XML tag string.
    *
    * @param string $prefix The prefix of the tag definition.
    * @param string $name The name of the tag definition.
    * @param string $tagString The string, that contains the tag definition.
    * @return string[] The attributes of the tag.
    * @throws ParserException In case of tag mismatch.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 30.12.2006 (Bug-fix: tag-to-attribute delimiter is now a constant value)<br />
    * Version 0.3, 03.01.2007<br />
    * Version 0.4, 13.01.2007 (Improved error messages)<br />
    * Version 0.5, 16.11.2007 (Improved error message. Now affected tag string is displayed, too)<br />
    * Version 0.6, 03.11.2008 (Fixed the issue, that a TAB character is no valid token to attributes delimiter)<br />
    * Version 0.7, 04.11.2008 (Fixed issue, that a combination of TAB and SPACE characters leads to wrong attributes parsing)<br />
    * Version 0.8, 05.11.2008 (Removed the TAB support due to performance and fault tolerance problems)<br />
    * Version 0.9, 26.09.2012 (Introduced additional arguments for prefix and name to gain performance)<br />
    */
   public static function getTagAttributes($prefix, $name, $tagString) {

      // search for taglib to attributes string delimiter
      $tagAttributeDel = strpos($tagString, ' ');

      // search for the closing sign
      $posTagClosingSign = strpos($tagString, '>');

      // In case, the separator between tag and attribute is not found, or in case the tag
      // end position is located between the tag and the attribute, the end sign (">") is used
      // as separator. This allows tags without attributes.
      if ($tagAttributeDel === false || $tagAttributeDel > $posTagClosingSign) {
         $tagAttributeDel = strpos($tagString, '>');
      }

      // search for the first appearance of the closing sign after the attribute string
      $posEndAttrib = strpos($tagString, '>');

      // extract the rest of the tag string.
      $attributesString = substr($tagString, $tagAttributeDel + 1, $posEndAttrib - $tagAttributeDel);

      // parse the tag's attributes
      $tagAttributes = XmlParser::getAttributesFromString($attributesString);

      // Check, whether the tag is self-closing. If not, read the content.
      if (substr($tagString, $posEndAttrib - 1, 1) == '/') {
         $content = '';
      } else {
         // check, if explicitly-closing tag exists
         if (strpos($tagString, '</' . $prefix . ':' . $name . '>') === false) {
            throw new ParserException('[XmlParser::getTagAttributes()] No closing tag found for '
                  . 'tag "<' . $prefix . ':' . $name . ' />"! Tag string: "' . $tagString . '".',
               E_USER_ERROR);
         } else {

            $found = true;
            $offset = 0;
            $posEndContent = 0;
            $count = 0;
            $maxCount = 10;
            $endTag = '</' . $prefix . ':' . $name . '>';

            while ($found == true) {

               // save old value
               $posEndContent = $offset;

               // re-assign the position of the tag end limiter
               $offset = strpos($tagString, $endTag, $offset + 1);

               // in case no further position is found -> end at this point
               if ($offset === false) {
                  $found = false;
               }

               // in case more than max positions are found -> end at this point for security reasons
               if ($count > $maxCount) {
                  $found = false;
               }

               $count++;
            }

            // read the content of the tag
            $content = substr($tagString, $posEndAttrib + 1, ($posEndContent - $posEndAttrib) - 1);
         }
      }

      $attributes = array();
      $attributes['attributes'] = $tagAttributes;
      $attributes['content'] = $content;
      return $attributes;
   }

   /**
    * @public
    * @static
    *
    * Extracts XML attributes from an attributes string. Returns an associative array with the attributes as keys and the values.
    * <pre>
    *   $array['ATTRIBUTE_NAME'] = 'ATTRIBUTE_VALUE';
    * </pre>
    *
    * @param string $attributesString The attributes string of the tag to analyze.
    * @return string[] The attributes of the tag.
    * @throws ParserException In case of tar attribute mismatch that may cause infinite loops.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 30.12.2006 (Enhanced the documentation)<br />
    * Version 0.3, 14.01.2007 (Improved the error message)<br />
    * Version 0.4, 14.11.2007 (Removed $hasFound; see http://forum.adventure-php-framework.org/de/viewtopic.php?t=7)<br />
    */
   public static function getAttributesFromString($attributesString) {

      $attributes = array();
      $offset = 0;

      $parserLoops = 0;
      $parserMaxLoops = 20;

      while (true) {

         $parserLoops++;

         // limit parse loop count to avoid endless while loops
         if ($parserLoops == $parserMaxLoops) {
            throw new ParserException('[XmlParser::getAttributesFromString()] Error while parsing: "'
               . $attributesString . '". Maximum number of loops ("' . $parserMaxLoops
               . '") exceeded!', E_USER_ERROR);
         }

         // find attribute
         $foundAtr = strpos($attributesString, '=', $offset);

         // if no attribute was found -> end at this point
         if ($foundAtr === false) {
            break;
         }

         // extract values
         $key = substr($attributesString, $offset, $foundAtr - $offset);
         $attrValueStart = strpos($attributesString, '"', $foundAtr);
         $attrValueStart++;
         $attrValueEnd = strpos($attributesString, '"', $attrValueStart);
         $attrValue = substr($attributesString, $attrValueStart, $attrValueEnd - $attrValueStart);
         $offset = $attrValueEnd + 1;

         // add to key => value array
         $attributes[trim($key)] = $attrValue;
      }

      return $attributes;
   }

   /**
    * @public
    * @static
    *
    * Generates a unique id, that is used as the object id for the APF DOM tree.
    *
    * @return string The unique id used as GUID for the APF DOM tree.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 25.10.2012 (Removed md5 choosing parameter to gain performance)<br />
    */
   public static function generateUniqID() {
      return md5(uniqid(rand(), true));
   }

}

/**
 * @package APF\core\pagecontroller
 * @class APFObject
 * @abstract
 *
 * Represents the base objects of (nearly) all APF classes. Especially all GUI classes derive
 * from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 11.02.2007 (Added language and context)<br />
 * Version 0.3, 28.10.2008 (Added the serviceType member to indicate the service manager creation type)<br />
 * Version 0.4, 03.11.2008 (Added initializing values to some of the class members)<br />
 */
abstract class APFObject implements APFDIService {

   /**
    * @protected
    * @var string[] The attributes of an object (merely the XML tag attributes).
    */
   protected $attributes = array();

   /**
    * @protected
    * @var string The context of the current object within the application.
    */
   protected $context = null;

   /**
    * @protected
    * @var string The language of the current object within the application.
    */
   protected $language = 'de';

   /**
    * @since 0.3
    * @protected
    * @var string Contains the service type, if the object was created with the ServiceManager.
    */
   protected $serviceType = null;

   /**
    * @since 1.15
    * @protected
    * @var bool Stores the internal initialization status of the present APFDIService.
    */
   protected $isInitialized = false;

   public function markAsInitialized() {
      $this->isInitialized = true;
   }

   public function markAsPending() {
      $this->isInitialized = false;
   }

   public function isInitialized() {
      return $this->isInitialized;
   }

   public function init($initParam) {
   }

   public function setContext($context) {
      $this->context = $context;
   }

   public function getContext() {
      return $this->context;
   }

   public function setLanguage($lang) {
      $this->language = $lang;
   }

   public function getLanguage() {
      return $this->language;
   }

   public function setServiceType($serviceType) {
      $this->serviceType = $serviceType;
   }

   public function getServiceType() {
      return $this->serviceType;
   }

   /**
    * @public
    *
    * This method returns the current version of the present APF distribution. Please
    * note that this revision is no warranty that all files within your current
    * installation are subjected to the returned version number since the APF team
    * cannot guarantee consistency throughout manual patching or manuel SVN updates.
    *
    * @return string The current APF version.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.02.2011<br />
    */
   public function getVersion() {
      return '2.0-SVN';
   }

   /**
    * @public
    *
    * Returns the object's attribute.
    *
    * @param string $name The name of the desired attribute.
    * @param string $default The default value for the attribute.
    * @return string Returns the value or null in case of errors.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 02.02.2007 (Added default value handling)<br />
    */
   public function getAttribute($name, $default = null) {
      return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
   }

   /**
    * @public
    *
    * Sets an object's attribute.
    *
    * @param string $name Name of the attribute.
    * @param string $value Value of the attribute.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function setAttribute($name, $value) {
      $this->attributes[$name] = $value;
   }

   /**
    * @public
    *
    * Returns an object's attributes.
    *
    * @return string[] Returns the list of attributes of the current object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function getAttributes() {
      return $this->attributes;
   }

   /**
    * @public
    *
    * Deletes an attribute.
    *
    * @param string $name The name of the attribute to delete.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function deleteAttribute($name) {
      unset($this->attributes[$name]);
   }

   /**
    * @public
    *
    * Sets an object's attributes.
    *
    * @param array $attributes The attributes list.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function setAttributes(array $attributes = array()) {
      if (count($attributes) > 0) {
         if (!is_array($this->attributes)) {
            $this->attributes = array();
         }
         $this->attributes = array_merge($this->attributes, $attributes);
      }
   }

   /**
    * @public
    *
    * Let's you add the applied value to the given attribute.
    * <p/>
    * Implicitly creates the attribute in case it doesn't exist.
    *
    * @param string $name The name of the attribute to add a value to.
    * @param string $value The value to add to the current attribute value.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.01.2007<br />
    * Version 0.2, 09.02.2013 (Moved to APFObject to avoid multiple implementations)<br />
    */
   public function addAttribute($name, $value) {
      if (isset($this->attributes[$name])) {
         $this->attributes[$name] .= $value;
      } else {
         $this->attributes[$name] = $value;
      }
   }

   /**
    * @protected
    *
    * Returns a service object, that is initialized by dependency injection.
    * For details see {@link DIServiceManager}.
    *
    * @param string $namespace The namespace of the service object definition.
    * @param string $name The name of the service object.
    * @return APFObject The pre-configured service object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.04.2009<br />
    */
   protected function &getDIServiceObject($namespace, $name) {
      return DIServiceManager::getServiceObject(
         $namespace, $name, $this->getContext(), $this->getLanguage());
   }

   /**
    * @protected
    *
    * Returns a service object according to the current application context.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    * @return APFObject The desired service object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.03.2007<br />
    * Version 0.2, 08.03.2007 (Context is now taken from the current object)<br />
    * Version 0.3, 10.03.2007 (Method now is considered protected)<br />
    * Version 0.4, 22.04.2007 (Added language initialization of the service manager)<br />
    * Version 0.5, 24.02.2008 (Added the service type param)<br />
    * Version 0.6  21.11.2012 Jens Prangenberg <jprangenberg@mywebhd.com> (Added the instanceid param)<br />
    */
   protected function &getServiceObject($class, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      return ServiceManager::getServiceObject($class, $this->getContext(), $this->getLanguage(), $type, $instanceId);
   }

   /**
    * @protected
    *
    * Returns a initialized service object according to the current application context.
    *
    * @deprecated Please use getServiceObject() applying the init param to dedicated methods or the DIServiceManager instead.
    *
    * @param string $class Fully qualified class name of the service object.
    * @param string $initParam The initialization parameter.
    * @param string $type The initializing type (see service manager for details).
    * @param string $instanceId The id of the instance to return.
    * @return APFObject The desired service object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 22.04.2007 (Added language initialization of the service manager)<br />
    * Version 0.3, 24.02.2008 (Added the service type param)<br />
    * Version 0.4  21.11.2012 Jens Prangenberg <jprangenberg@mywebhd.com> (Added the instanceid param)<br />
    */
   protected function &getAndInitServiceObject($class, $initParam, $type = APFService::SERVICE_TYPE_SINGLETON, $instanceId = null) {
      return ServiceManager::getAndInitServiceObject($class, $this->getContext(), $this->getLanguage(), $initParam, $type, $instanceId);
   }

   /**
    * @protected
    *
    * Convenience method for loading a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    * @return Configuration The desired configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.09.2010<br />
    */
   protected function getConfiguration($namespace, $name) {
      return ConfigurationManager::loadConfiguration(
         $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name);
   }

   /**
    * @protected
    *
    * Convenience method for saving a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    * @param Configuration $config The configuration to save.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2010<br />
    */
   protected function saveConfiguration($namespace, $name, Configuration $config) {
      ConfigurationManager::saveConfiguration(
         $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name, $config);
   }

   /**
    * @protected
    *
    * Convenience method for deleting a configuration depending on APF DOM attributes and
    * the current environment.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $name The name of the configuration including it's extension.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 27.07.2011<br />
    */
   protected function deleteConfiguration($namespace, $name) {
      ConfigurationManager::deleteConfiguration(
         $namespace, $this->getContext(), $this->getLanguage(), Registry::retrieve('APF\core', 'Environment'), $name);
   }

   /**
    * @protected
    *
    * Creates a string representation of the given attributes list, using a
    * white list to especially include attributes.
    *
    * @param array $attributes The list of attributes to convert to an xml string.
    * @param array $whiteList  The list of attributes, the string may contain.
    * @return string The xml attributes string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010 (Replaced old implementation with the white list feature.)<br />
    */
   protected function getAttributesAsString(array $attributes, array $whiteList = array()) {

      $attributeParts = array();

      // process white list entries only, when attribute is given
      // code duplication is done here due to performance reasons!!!
      $charset = Registry::retrieve('APF\core', 'Charset');
      if (count($whiteList) > 0) {
         foreach ($attributes as $offset => $value) {
            if (in_array($offset, $whiteList)) {
               $attributeParts[] = $offset . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
            }
         }
      } else {
         foreach ($attributes as $offset => $value) {
            $attributeParts[] = $offset . '="' . htmlspecialchars($value, ENT_QUOTES, $charset, false) . '"';
         }
      }

      return implode(' ', $attributeParts);
   }

}

/**
 * @package APF\core\pagecontroller
 * @class TagLib
 *
 * This class represents a taglib and thus is used as a taglib definition. Each time,
 * you add a known taglib to a DOM node, an instance of the TagLib class is added to
 * the node.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 */
final class TagLib {

   /**
    * @var string The class name of the tag implementation .
    */
   private $class;

   /**
    * @var string The prefix of the tag (e.g. <em>core<em> for tag <em>&lt;core:importdesign /&gt;).
    */
   private $prefix;

   /**
    * @var string The name of the tag (e.g. <em>importdesign<em> for tag <em>&lt;core:importdesign /&gt;).
    */
   private $name;

   /**
    * @public
    *
    * Defines a taglib.
    *
    * @param string $class The fully qualified class name of the tag implementation.
    * @param string $prefix The prefix of the tag  (e.g. <em>core<em> for tag <em>&lt;core:importdesign /&gt;).
    * @param string $name The name of the tag  (e.g. <em>importdesign<em> for tag <em>&lt;core:importdesign /&gt;).
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 21.03.2013 (Tag classes are now namespace-aware)<br />
    */
   public function __construct($class, $prefix, $name) {
      $this->class = $class;
      $this->prefix = $prefix;
      $this->name = $name;
   }

   /**
    * @public
    *
    * Returns the prefix of the taglib.
    *
    * @return string The prefix of the taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function getPrefix() {
      return $this->prefix;
   }

   /**
    * @public
    *
    * Returns the class of the taglib.
    *
    * @return string The class of the taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function getClass() {
      return $this->class;
   }

   /**
    * @public
    *
    * @return string The name of the tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.06.2012<br />
    */
   public function getName() {
      return $this->name;
   }

}

/**
 * @package APF\core\pagecontroller
 * @class Page
 *
 * The Page object represents the root node of  a web page. It is used as a container for the
 * initial document (root document) and is responsible for creating and transforming the root
 * document.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 03.01.2007 (Introduced URL rewriting)<br />
 * Version 0.3, 08.06.2007 (URL rewriting was outsourced and "__rewriteRequestURI()" was removed)<br />
 */
class Page extends APFObject {

   /**
    * @var Document Container for the root <em>Document</em> of the page.
    */
   private $document;

   /**
    * @public
    *
    * Returns the root document of the APF DOM tree.
    *
    * @return Document The root document of the page controller's APF DOM tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.10.2010<br />
    */
   public function &getRootDocument() {
      return $this->document;
   }

   /**
    * @public
    *
    * Creates the initial document (root) of the page object and loads the initial template. If
    * no context was set before, the namespace of the initial template is taken instead.
    *
    * @param string $namespace namespace if the initial template
    * @param string $design (file)name if the initial template
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 31.01.2007 (Now the context of the document is set)<br />
    * Version 0.3, 04.03.2007 (The namespace is taken as a context, if no other was set before)<br />
    * Version 0.4, 22.04.2007 (Now the language is applied to the document)<br />
    * Version 0.5, 08.03.2009 (Bug-fix: protected variable parentObject might not be used)<br />
    */
   public function loadDesign($namespace, $design) {

      $this->document = new Document();

      // set the current context
      $context = $this->getContext();
      if (empty($context)) {
         $this->document->setContext($namespace);
      } else {
         $this->document->setContext($context);
      }

      // set the current language
      $this->document->setLanguage($this->getLanguage());

      // load the design
      $this->document->loadDesign($namespace, $design);
      $this->document->setObjectId(XmlParser::generateUniqID());
   }

   /**
    * @public
    *
    * Transforms the APF DOM tree of the current page. Returns the content of the transformed document.
    *
    * @return string The content of the transformed page
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.01.2007 (Introduced URL rewriting)<br />
    * Version 0.3, 08.06.2007 (Moved the URL rewriting into a filter)<br />
    * Version 0.4, 11.12.2008 (Switched to the new input filter concept)<br />
    */
   public function transform() {
      return $this->document->transform();
   }

}

/**
 * @package APF\core\pagecontroller
 * @class Document
 *
 * Represents a node within the APF DOM tree. Each document can compose several other documents
 * by use of the $children property (composite tree).
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 */
class Document extends APFObject {


   /**
    * @const
    * Attribute name for service name of document controller
    */
   const CONTROLLER_ATTR_SERVICE_NAME = 'service';

   /**
    * @const
    * Attribute name for service namespace of document controller
    */
   const CONTROLLER_ATTR_SERVICE_NAMESPACE = 'namespace';

   /**
    * @const
    * Attribute name for fully qualified class name of document controller
    */
   const CONTROLLER_ATTR_CLASS = 'class';



   /**
    * @protected
    * @var string Unique object identifier.
    */
   protected $objectId = null;

   /**
    * @protected
    * @var Document Reference to the parent object.
    */
   protected $parentObject = null;

   /**
    * @protected
    * @var string The content of the tag. Example:
    * <pre>&lt;foo:bar&gt;This is the content of the tag.&lt;/foo:bar&gt;</pre>
    */
   protected $content;

   /**
    * @protected
    * @var DocumentController The instance of the document controller to use at transformation time.
    */
   protected $documentController = null;

   /**
    * @protected
    * @var TagLib[] List of known taglibs.
    */
   protected $tagLibs = array();

   /**
    * @protected
    * @var Document[] List of the children of the current object.
    */
   protected $children = array();

   /**
    * @protected
    * @var int The maximum number of parser loops to protect against infinite loops.
    */
   protected $maxLoops = 300;

   /**
    * @public
    *
    * Initializes the built-in taglibs, used to create the APF DOM tree.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
    * Version 0.3, 11.02.2012 (Added html:getstring tag as known tag (refactoring!))<br />
    */
   public function __construct() {

      // set the object id
      $this->setObjectId(XmlParser::generateUniqID());

      // add the known taglibs (core taglibs!)
      // we are *not* using the addTaglib() method, because the following tags are
      // already included in the pagecontroller.php and adding the tags directly
      // is twice as fast compared to the addTagLib() method.
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'core', 'addtaglib');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\ImportTemplateTag', 'core', 'importdesign');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AppendNodeTag', 'core', 'appendnode');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\TemplateTag', 'html', 'template');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'html', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'html', 'getstring');
   }

   /**
    * @public
    *
    * Injects the parent node of the current APF object.
    *
    * @param Document $parentObject The parent node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setParentObject(Document &$parentObject) {
      $this->parentObject = & $parentObject;
   }

   /**
    * @public
    *
    * Returns the parent node of the current APF object.
    *
    * @return Document The parent node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getParentObject() {
      return $this->parentObject;
   }

   /**
    * @public
    *
    * Sets the object id of the current APF object.
    *
    * @param string $objectId The object id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setObjectId($objectId) {
      $this->objectId = $objectId;
   }

   /**
    * @public
    *
    * Returns the object id of the current APF object.
    *
    * @return string The object id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getObjectId() {
      return $this->objectId;
   }

   /**
    * @public
    *
    * Returns the textual content of the current node.
    *
    * @return string The content of the current node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getContent() {
      return $this->content;
   }

   /**
    * @public
    *
    * Sets the textual content of the current node.
    *
    * @param string $content The content of the current node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setContent($content) {
      $this->content = $content;
   }

   /**
    * @public
    *
    * Returns the list of the current node's children.
    *
    * @return Document[] The current node's children.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getChildren() {
      return $this->children;
   }

   /**
    * @protected
    *
    * Let's you retrieve a child node of the current document by specifying a selector
    * (attribute name and attribute value) and the expected node type (name of the taglib
    * class).
    *
    * @param string $attributeName The name of the attribute to match against the given value.
    * @param string $value The value of the attribute to select the desired node.
    * @param string $tagLibClass The expected class name of the node.
    * @return Document The desired child node.
    * @throws \InvalidArgumentException In case the node has no children or no child node can be found with the given selectors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.12.2011<br />
    * Version 0.2, 09.02.2013 (Now public access since DocumentController is now derived from APFObject instead of Document)<br />
    */
   public function &getChildNode($attributeName, $value, $tagLibClass) {
      $children = & $this->getChildren();
      if (count($children) > 0) {
         foreach ($children as $objectId => $DUMMY) {
            if ($children[$objectId] instanceof $tagLibClass) {
               if ($children[$objectId]->getAttribute($attributeName) == $value) {
                  return $children[$objectId];
               }
            }
         }
      } else {
         throw new \InvalidArgumentException('[' . get_class($this) . '::getChildNode()] Current node has no children!',
            E_USER_ERROR);
      }
      throw new \InvalidArgumentException('[' . get_class($this) . '::getChildNode()] No child node with type "'
         . $tagLibClass . '" and attribute selector ' . $attributeName . '="' . $value . '" composed in current '
         . 'document!', E_USER_ERROR);
   }

   /**
    * @protected
    *
    * Let's you retrieve a list of child nodes of the current document by specifying a selector
    * (attribute name and attribute value) and the expected node type (name of the taglib
    * class).
    *
    * @param string $attributeName The name of the attribute to match against the given value.
    * @param string $value The value of the attribute to select the desired node.
    * @param string $tagLibClass The expected class name of the nodes.
    * @return Document[] The desired list of child nodes.
    * @throws \InvalidArgumentException In case the node has no children or no child node can be found with the given selectors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.07.2012<br />
    * Version 0.2, 09.02.2013 (Now public access since DocumentController is now derived from APFObject instead of Document)<br />
    */
   public function &getChildNodes($attributeName, $value, $tagLibClass) {
      $children = & $this->getChildren();

      if (count($children) > 0) {
         $result = array();
         foreach ($children as $objectId => $DUMMY) {
            if ($children[$objectId] instanceof $tagLibClass) {
               if ($children[$objectId]->getAttribute($attributeName) == $value) {
                  $result[] = & $children[$objectId];
               }
            }
         }
         if (count($result) == 0) {
            throw new \InvalidArgumentException('[' . get_class($this) . '::getChildNodes()] No child nodes with type "'
               . $tagLibClass . '" and attribute selector ' . $attributeName . '="' . $value . '" composed in current '
               . 'document!', E_USER_ERROR);
         } else {
            return $result;
         }
      }

      throw new \InvalidArgumentException('[' . get_class($this) . '::getChildNodes()] Current node has no children!', E_USER_ERROR);
   }

   /**
    * @public
    *
    * API method to set a place holder's content within a document.
    *
    * @param string $name name of the place holder.
    * @param string $value value of the place holder.
    * @return Document This instance for further usage.
    * @throws \InvalidArgumentException In case the place holder cannot be found.
    *
    * @author Christian Achatz, Jan Wiese
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 10.11.2008 (Removed check, if taglib class exists)<br />
    * Version 0.3, 07.02.2013 (Moved to Document to avoid multiple implementations)<br />
    */
   public function &setPlaceHolder($name, $value) {
      $count = 0;
      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof PlaceHolderTag
            && $this->children[$objectId]->getAttribute('name') === $name
         ) {
            $this->children[$objectId]->setContent($value);
            $count++;
         }
      }

      if ($count == 0) {

         // Since this method is used within all derived classes the exception message is
         // rather generic unfortunately. In order to be more precise, the convenience methods
         // within BaseDocumentController catch and rethrow the exception enriched with further
         // information.
         $message = '[' . get_class($this) . '::setPlaceHolder()] No place holder with name "' . $name
            . '" found within document with ';

         $nodeName = $this->getAttribute('name');
         if (!empty($nodeName)) {
            $message .= 'name "' . $nodeName . '" and ';
         }
         $message .= 'node type "' . get_class($this) . '"! Please check your template code: ' . $this->getContent() . '.';

         throw new \InvalidArgumentException($message, E_USER_ERROR);
      }

      return $this;
   }

   /**
    * @public
    *
    * This method is for conveniently setting of multiple place holders. The applied
    * array must contain a structure like this:
    * <code>
    * array(
    *    'key-a' => 'value-a',
    *    'key-b' => 'value-b',
    *    'key-c' => 'value-c',
    *    'key-d' => 'value-d',
    *    'key-e' => 'value-e',
    * )
    * </code>
    * Thereby, the <em>key-*</em> offsets define the name of the place holders, their
    * values are used as the place holder's values.
    *
    * @param string[] $placeHolderValues Key-value-couples to fill place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    * Version 0.2, 09.02.2013 (Moved to Document to avoid multiple implementations)<br />
    */
   public function setPlaceHolders(array $placeHolderValues) {
      foreach ($placeHolderValues as $key => $value) {
         $this->setPlaceHolder($key, $value);
      }
   }

   /**
    * @protected
    *
    * Replaces string place holders in content of &lt;*:placeholder /&gt; tag.
    * An example of a string place holder with key "url" is "{URL}"
    * String place holders are always written in capital letters!
    *
    * @param string $name Place holder name.
    * @param string $key Key name of string place holder.
    * @param string $value Value, the string place holder is replaced with.
    * @return Document This instance for further usage.
    * @throws \InvalidArgumentException In case no place holder has been found.
    *
    * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
    * @version
    * Version 0.1, 03.10.2012<br />
    */
   public function &setStringPlaceHolder($name, $key, $value) {
      $nodes = & $this->getChildNodes('name', $name, 'APF\core\pagecontroller\PlaceHolderTag');
      /* @var $nodes PlaceHolderTag[] */
      foreach ($nodes as $node) {
         $node->setStringReplacement($key, $value);
      }
      return $this;
   }

   /**
    * @public
    *
    * Returns the name of the document controller in case the document should
    * be transformed using an MVC controller. In case no controller is defined
    * <em>null</em> is returned instead.
    *
    * @return string The name of the document controller.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getDocumentController() {
      return get_class($this->documentController);
   }

   /**
    * @public
    *
    * This method is used to add more known taglibs to a document.
    *
    * @param TagLib $tag The tag lib to add.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
    * Version 0.3, 14.02.2011 (Refactored method signature to be more type safe)<br />
    */
   public function addTagLib(TagLib $tag) {
      // add the taglib to the current node
      $this->tagLibs[] = $tag;
   }

   /**
    * @public
    *
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

      // parse known taglibs
      $this->extractTagLibTags();
   }

   /**
    * @protected
    *
    * Loads a template from a given namespace. The convention says, that the name of the template
    * is equal to the file body plus the ".html" extensions. The namespace is a APF namespace.
    *
    * @param string $namespace The namespace of the template.
    * @param string $name The name of the template (a.k.a. design).
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
      } catch (\Exception $e) {
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
    * @protected
    *
    * Generates the file path of the desired template.
    * <p/>
    * Overwriting this method allows you to use a different algorithm of creating the
    * path within your custom tag implementations.
    *
    * @param string $namespace The namespace of the template.
    * @param string $name The (file) name of the template.
    * @return string The template file path of the referred APF template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2012<br />
    */
   protected function getTemplateFilePath($namespace, $name) {
      // gather namespace and full(!) template name and use class loader to determine root path
      $classLoader = RootClassLoader::getLoaderByNamespace($namespace);
      $rootPath = $classLoader->getRootPath();
      $vendor = $classLoader->getVendorName();
      $fqNamespace = str_replace($vendor . '\\', '', $namespace);
      return $rootPath . '/' . str_replace('\\', '/', $fqNamespace) . '/' . $name . '.html';
   }

   /**
    * @protected
    *
    * Parses the content of the current APF DOM node. Extracts all known taglibs listed in
    * the <em>$this->tagLibs</em> property. Each taglib is converted into a child document
    * of the current tree element. The tag definition place is reminded by a marker tag using
    * the internal id of the DOM node.
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
    * Please note that using nested structures must be supported by the tag implementations
    * by registering itself within the tag implementation class to create the nested sub-tree
    * on parse time.
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
    */
   protected function extractTagLibTags() {

      $tagLibLoops = 0;
      $i = 0;

      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      /* @var $t BenchmarkTimer */

      // Parse the known taglibs. Here, we have to use a while loop, because one parser loop
      // can result in an increasing amount of known taglibs (core:addtaglib!).
      while ($i < count($this->tagLibs)) {

         if ($tagLibLoops > $this->maxLoops) {
            throw new ParserException('[' . get_class($this) . '::extractTagLibTags()] Maximum numbers of '
               . 'parsing loops reached!', E_USER_ERROR);
         }

         $class = $this->tagLibs[$i]->getClass();
         $prefix = $this->tagLibs[$i]->getPrefix();
         $name = $this->tagLibs[$i]->getName();
         $token = $prefix . ':' . $name;

         $tagLoops = 0;

         while (strpos($this->content, '<' . $token) !== false) {

            if ($tagLoops > $this->maxLoops) {
               throw new ParserException('[' . get_class($this) . '::extractTagLibTags()] Maximum numbers of parsing loops reached!', E_USER_ERROR);
            }

            // Find the first occurrence of the current tag to distinguish between
            // self- and explicitly-closing tags.
            $tagStartPos = strpos($this->content, '<' . $token);

            $bracket = strpos($this->content, '>', $tagStartPos);
            if (substr($this->content, $bracket - 1, 1) == '/') {
               $tagEndPos = $bracket + 1;
               $endTagLength = 0;
            } else {
               $startTagLength = strlen('<' . $token);
               $endTagLength = strlen('</' . $token . '>');

               // initialize the token position cursor
               $tokenPos = $tagStartPos;
               $tagEndPos = $tagStartPos;

               $openingTagCount = 0;
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
                     $bracket = strpos($this->content, '>', $tokenPos + strlen($token));
                     if (substr($this->content, $bracket - 1, 1) !== '/') {
                        $openingTagCount++;
                     }

                  } else {
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

            }

            // extract the resulting tag string
            $tagStringLength = ($tagEndPos - $tagStartPos) + $endTagLength;
            $tagString = substr($this->content, $tagStartPos, $tagStringLength);

            // get the tag attributes of the current tag
            $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);

            // class is loaded by the class loader lazily
            $object = new $class();
            /* @var $object Document */

            // inject context of the parent object
            $object->setContext($this->getContext());

            // inject language of the parent object
            $object->setLanguage($this->getLanguage());

            // add the tag's attributes
            $object->setAttributes($attributes['attributes']);

            // initialize object id, that is used to reference the object
            // within the APF DOM tree and to provide a unique key for the
            // children index.
            $objectId = XmlParser::generateUniqID();
            $object->setObjectId($objectId);

            // replace the position of the taglib with a place holder
            // token string: <$objectId />.
            // this needs to be done, to be able to place the content of the
            // transformed taglib at transformation time correctly
            $this->content = substr_replace($this->content, '<' . $objectId . ' />', $tagStartPos, $tagStringLength);

            // advertise the parent object
            $object->setParentObject($this);

            // add the content to the current APF DOM node
            $object->setContent($attributes['content']);

            // call onParseTime() to enable the taglib to initialize itself
            $benchId = '(' . get_class($this) . ') ' . $this->getObjectId() . '::children[('
               . get_class($object) . ') ' . $objectId . ']::onParseTime()';
            $t->start($benchId);
            $object->onParseTime();
            $t->stop($benchId);

            // add current object to the APF DOM tree (no reference, because this leads to NPEs!)
            $this->children[$objectId] = $object;

            $tagLoops++;

            // delete current object to avoid interference.
            unset($object);
         }

         $i++;
      }

      // call onAfterAppend() on each child to enable the taglib to interact with
      // other APF DOM nodes to do extended initialization.
      if (count($this->children) > 0) {

         $benchId = '(' . get_class($this) . ') ' . $this->getObjectId() . '::children[]::onAfterAppend()';
         $t->start($benchId);

         foreach ($this->children as $objectId => $DUMMY) {
            $this->children[$objectId]->onAfterAppend();
         }

         $t->stop($benchId);
      }
   }

   /**
    * @protected
    *
    * Initializes the document controller class, that is executed at APF DOM node
    * transformation time.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 15.12.2009 (Added check for non existing class attribute)<br />
    * Version 0.2, 28.07.2013 Jan Wiese (Introduced di-service support for documentcontrollers. Moved controller creation here)<br />
    */
   protected function extractDocumentController() {

      // define start and end tag
      $controllerStartTag = '<@controller';
      $controllerEndTag = '@>';

      if(strpos($this->content, $controllerStartTag) === false) {
         // no controller tag found
         return;
      }

      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('(' . get_class($this) . ') ' . $this->getObjectId() . '::extractDocumentController()');

      $tagStartPos = strpos($this->content, $controllerStartTag);
      $tagEndPos = strpos($this->content, $controllerEndTag, $tagStartPos);
      $controllerTag = substr($this->content, $tagStartPos + strlen($controllerStartTag), ($tagEndPos - $tagStartPos) - 1 - strlen($controllerStartTag));
      $controllerAttributes = XmlParser::getAttributesFromString($controllerTag);


      // check if di service type is given
      if(
         isset($controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAMESPACE]) &&
         isset($controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAME])
      ) {

         // fetch di service parameters
         $DINamespace = $controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAMESPACE];
         $DIServicename = $controllerAttributes[self::CONTROLLER_ATTR_SERVICE_NAME];

         // start benchmark timer
         $id = '(' . $DINamespace . '\\' . $DIServicename . ') ' . (XmlParser::generateUniqID()) . '::__construct()';
         $t->start($id);

         try {
            // create document controller via di service manager
            $docCon = $this->getDIServiceObject($DINamespace, $DIServicename);
         }
         catch (\Exception $e) {
            throw new \InvalidArgumentException('[' . get_class($this) . '::extractDocumentController()] Given documentcontroller '
               . 'could not successfully be created using the di-service manager: ' . $e->getMessage(), $e->getCode());
         }

      }
      // check if normal type is given
      elseif(
         isset($controllerAttributes[self::CONTROLLER_ATTR_CLASS])
      ) {

         $docConClass = $controllerAttributes[self::CONTROLLER_ATTR_CLASS];

         // start benachmark timer
         $id = '(' . $docConClass . ') ' . (XmlParser::generateUniqID()) . '::__construct()';
         $t->start($id);

         // class is loaded via the class loader lazily
         $docCon = new $docConClass;
         /* @var $docCon DocumentController */

         // inject context
         $docCon->setContext($this->getContext());

         // inject current language
         $docCon->setLanguage($this->getLanguage());

      }
      // no valid document controller definition given
      else {

         throw new ParserException('[' . get_class($this) . '::extractDocumentController()] Document '
            . 'controller specification does not contain a valid controller class or service definition. '
            . 'Please double check the template code and consult the documentation. '
            . 'Template code: ' . $this->getContent());

      }


      // remark document controller
      $this->documentController = $docCon;

      // remove definition from content to be not displayed
      $this->content = substr_replace($this->content, '', $tagStartPos, ($tagEndPos - $tagStartPos) + strlen($controllerEndTag));

      // stop benchmark timers
      $t->stop($id);
      $t->stop('(' . get_class($this) . ') ' . $this->getObjectId() . '::extractDocumentController()');

   }

   /**
    * Interface definition of the onParseTime() method. This function is called after the creation
    * of a new DOM node. It must be implemented by derived classes.
    *
    * @public
    * @abstract
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function onParseTime() {
   }

   /**
    * Interface definition of the onAfterAppend() method. This function is called after the DOM
    * node is appended to the DOM tree. It must be implemented by derived classes.
    *
    * @public
    * @abstract
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function onAfterAppend() {
   }

   /**
    * @public
    *
    * Implements the method, that is called at transformation time (see DOM node life cycle). If
    * you want to add custom logic in your taglib, overwrite this method. The page controller
    * expects the method to return the content of the transformed node.
    *
    * @return string The transformed content of the current DOM node.
    * @throws \InvalidArgumentException In case the document controller class is missing.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 21.01.2007 (Now, the attributes of the current node are injected the document controller)<br />
    * Version 0.3, 31.01.2007 (Added context injection)<br />
    * Version 0.4, 24.02.2007 (Switched timer inclusion to common benchmarker usage)<br />
    * Version 0.5, 09.04.2007 (Added language injection)<br />
    * Version 0.6, 09.02.2013 (Introduced the DocumentController interface)<br />
    * Version 0.7, 28.07.2013 Jan Wiese (Introduced di-service support for documentcontrollers. Moved controller creation to ::extractDocumentController())<br />
    */
   public function transform() {

      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('(' . get_class($this) . ') ' . $this->getObjectId() . '::transform()');

      // create copy, to preserve it!
      $content = $this->content;

      $docCon = $this->documentController;

      // execute the document controller if applicable
      if(!empty($docCon)) {

         // start benachmark timer
         $id = '(' . get_class($this->documentController) . ') ' . (XmlParser::generateUniqID()) . '::transformContent()';
         $t->start($id);

         // inject this document to be able to work on the DOM
         $docCon->setDocument($this);

         // inject the content to be able to access it
         $docCon->setContent($content);

         // execute the document controller by using a standard method
         $docCon->transformContent();

         // retrieve the content
         $content = $docCon->getContent();

         $t->stop($id);
      }

      // transform child nodes and replace XML marker to place the output at the right position
      if (count($this->children) > 0) {
         foreach ($this->children as $objectId => $DUMMY) {
            $content = str_replace('<' . $objectId . ' />', $this->children[$objectId]->transform(), $content);
         }
      }

      $t->stop('(' . get_class($this) . ') ' . $this->getObjectId() . '::transform()');

      return $content;
   }

   /**
    * @protected
    *
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
      foreach ($this->children as $objectId => $DUMMY) {
         $this->content = str_replace(
            '<' . $objectId . ' />', $this->children[$objectId]->transform(), $this->content
         );
      }
   }

   /**
    * @protected
    *
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
      foreach ($this->children as $objectId => $DUMMY) {
         $content = str_replace(
            '<' . $objectId . ' />', $this->children[$objectId]->transform(), $content
         );
      }
      return $content;
   }

   /**
    * @protected
    *
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
      foreach ($this->children as $objectId => $DUMMY) {
         $this->content = str_replace('<' . $objectId . ' />', '', $this->content);
      }
   }

   /**
    * @protected
    *
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
      foreach ($this->children as $objectId => $DUMMY) {
         $content = str_replace('<' . $objectId . ' />', '', $content);
      }
      return $content;
   }

}

/**
 * @package APF\core\pagecontroller
 * @class AppendNodeTag
 *
 * Parses a template and appends the child nodes to the parent's child list. Hence, this taglib
 * can be used to swap shared templates / forms / ... Please make sure, that the imported template
 * includes the necessary "*:addtaglib" statements for the relevant tag definitions.
 * <p/>
 * Usage:
 * <pre>&lt;core:addtaglib class="APF\core\pagecontroller\AppendNodeTag" prefix="core" class="appendnode" /&gt;
 * &lt;core:appendnode namespace="..." template="..."[ includestatic="true"]/&gt;</pre>
 * <p/>
 * In case the <em>includestatic</em> is present and set to <em>true</em>, the static content
 * (e.g. html markup) is included in the parent's content, too.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.11.2008<br />
 */
class AppendNodeTag extends Document {

   /**
    * @var string Indicates, whether the static content of an included templates
    * should be appended to the target template (<em>true</em>) or not (<em>false</em>).
    */
   protected static $INCLUDE_STATIC_CONTENT_ATTRIBUTE_NAME = 'includestatic';

   /**
    * @public
    *
    *  Initializes the known taglib list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    */
   public function __construct() {
      parent::__construct();
   }

   /**
    * @public
    *
    * Load the content and parse the template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    */
   public function onParseTime() {

      // check attributes
      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new \InvalidArgumentException('[AppendNodeTag::onParseTime()] Attribute '
            . '"namespace" is not present or empty! Please provide the namespace of the '
            . 'desired template.', E_USER_ERROR);
      }

      $template = $this->getAttribute('template');
      if ($template === null) {
         throw new \InvalidArgumentException('[AppendNodeTag::onParseTime()] Attribute '
            . '"template" is not present or empty! Please provide the name of the desired '
            . 'template.', E_USER_ERROR);
      }

      // load the content
      $this->loadContentFromFile($namespace, $template);

      // parse known tags
      $this->extractTagLibTags();

   }

   /**
    * @public
    *
    * Appends the node's children to the parent node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    * Version 0.2, 16.11.2008 (Bug-fix: added a parent object reference correction for the new nodes)<br />
    * Version 0.3, 16.11.2008 (Enhancement: added a tag marker to the parent object to enable the transformOnPlace() feature)<br />
    * Version 0.4, 22.02.2010 (Added possibility to include static content)<br />
    */
   public function onAfterAppend() {

      // get parent children list
      /* @var $parentChildren Document[] */
      $parentChildren = & $this->parentObject->getChildren();
      $parentContent = $this->parentObject->getContent();
      $currentObjectId = $this->getObjectId();

      // include static content, if desired.
      // code duplication is done to speed up the DOM node relocation!
      $includeStatic = $this->getAttribute(self::$INCLUDE_STATIC_CONTENT_ATTRIBUTE_NAME);
      if ($includeStatic === 'true') {

         foreach ($this->children as $objectId => $DUMMY) {

            // append node to parent object's children list
            $parentChildren[$objectId] = & $this->children[$objectId];

            // correct the parent object reference
            $parentChildren[$objectId]->setParentObject($this->parentObject);
         }

         // include complete content of the current document and append it to
         // the place holder of the present tag's marker
         $this->parentObject->setContent(
            str_replace('<' . $currentObjectId . ' />',
               '<' . $currentObjectId . ' />' . $this->content,
               $parentContent)
         );

      } else {

         foreach ($this->children as $objectId => $DUMMY) {

            // append node to parent object's children list
            $parentChildren[$objectId] = & $this->children[$objectId];

            // correct the parent object reference
            $parentChildren[$objectId]->setParentObject($this->parentObject);

            // add a marker tag to the parent object after the tag's marker
            $parentContent = str_replace('<' . $currentObjectId . ' />', '<' . $currentObjectId . ' /><' . $objectId . ' />', $parentContent);
            $currentObjectId = $objectId;

         }

         // include content of the current document
         $this->parentObject->setContent($parentContent);

      }
   }

   /**
    * @public
    *
    * Returns an empty string, due to the fact, that the core:appendnode tag does not have to
    * create output.
    *
    * @return string Nothing since the tag generates no output.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    */
   public function transform() {
      return '';
   }

}

/**
 * @package APF\core\pagecontroller
 * @class ImportTemplateTag
 *
 * This class implements the functionality of the core::importdesign tag. It generates a sub node
 * from the template specified by the tag's attributes within the current APF DOM tree. Each
 * importdesign tag can compose further tags.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 */
class ImportTemplateTag extends Document {

   /**
    * @public
    *
    * Constructor of the class. Sets the known taglibs.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function __construct() {
      parent::__construct();
   }

   /**
    * @public
    *
    * Implements the onParseTime() method from the Document class. Includes the desired template
    * as a new DOM node into the current APF DOM tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 31.12.2006 (Added pagepart option)<br />
    * Version 0.3, 15.01.2007 (Now DocumentController tags are extracted first)<br />
    * Version 0.4, 10.03.2007 (The Context can now be manipulated in the core:importdesign tag)<br />
    * Version 0.5, 11.03.2007 (Introduced the "incparam" attribute to be able to control the template param via url)<br />
    * Version 0.6, 26.10.2008 (Made the benchmark id generation more generic)<br />
    */
   public function onParseTime() {

      // get attributes
      $namespace = $this->getAttribute('namespace');
      $template = $this->getAttribute('template');

      // apply context if available
      $context = $this->getAttribute('context');
      if ($context !== null) {
         $this->setContext($context);
      }

      // apply language if available
      $language = $this->getAttribute('language');
      if ($language !== null) {
         $this->setLanguage($language);
      }

      // manager inc param
      $incParam = null;
      if (isset($this->attributes['incparam'])) {
         $incParam = $this->attributes['incparam'];
      } else {
         $incParam = 'pagepart';
      }

      // check, if the inc param is present in the current request
      if (strpos($template, '[') !== false) {

         if (isset($_REQUEST[$incParam]) && !empty($_REQUEST[$incParam])) {
            $template = $_REQUEST[$incParam];
         } else {

            // read template attribute from inc param
            $pagepartStartPos = strpos($template, '=');
            $pagepartEndPos = strlen($template) - 1;
            $template = trim(substr($template, $pagepartStartPos + 1, ($pagepartEndPos - $pagepartStartPos) - 1));
         }
      }

      // get content
      $this->loadContentFromFile($namespace, $template);

      // parse document controller statements
      $this->extractDocumentController();

      // extract further xml tags
      $this->extractTagLibTags();
   }

}

/**
 * @package APF\core\pagecontroller
 * @class AddTaglibTag
 *
 * Represents the functionality of the core:addtaglib tag. Adds a further taglib to the known
 * taglibs of the tag's parent object. This can be used to enhance the known tag list if a
 * desired APF DOM node.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 */
class AddTaglibTag extends Document {

   public function __construct() {
      // do nothing, especially not initialize tag libs
   }

   /**
    * @public
    *
    * Implements the onParseTime() method of the Document class. Adds the desired
    * taglib to the parent object.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 10.11.2008 (Changed implementation. We now use getAttribute() instead of direct internal attribute addressing)<br />
    * Version 0.3, 14.02.2011 (Adapted to new Document::addTaglib() signature)<br />
    * Version 0.4, 11.06.2012 (Introduced fallback mechanism for old tag definitions)<br />
    * Version 0.5, 20.12.2012 (Removed fallback mechanism for 1.17)<br />
    */
   public function onParseTime() {
      $class = $this->getAttribute('class');
      $name = $this->getAttribute('name');
      $prefix = $this->getAttribute('prefix');

      $tagLib = new TagLib($class, $prefix, $name);
      $this->getParentObject()->addTagLib($tagLib);

      // Resets the attributes list to avoid selection issues with the
      // getChildNode() and getChildNodes() methods that may select this
      // node when requesting a node by the "name" attribute. See bug
      // http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=1192
      // for more details.
      $this->attributes = array();
   }

   /**
    * @public
    *
    * Implements the Document's transform() method. Returns an empty string, because the addtaglib
    * tag generates no output.
    *
    * @return string Nothing, since this tag produces no output.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.04.2007<br />
    */
   public function transform() {
      return '';
   }

}

/**
 * @package APF\core\pagecontroller
 * @class PlaceHolderTag
 *
 * Represents a place holder within a template file. Can be filled within a document controller
 * using the setPlaceHolder() method.
 *
 * @author Christian Achatz, Jan Wiese
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 02.01.2013 (Introduced string place holder mechanism)<br />
 */
class PlaceHolderTag extends Document {

   /**
    * @since 1.17
    * @var string[] Replacement strings for string place holders.
    */
   protected $stringReplacement = array();

   public function __construct() {
      // do nothing, especially not initialize tag libs
   }

   /**
    * @public
    *
    * Let's you set a string replacement to the current place holder instance.
    * <p/>
    * Please note, that the keys must be specified in uppercase letters.
    *
    * @param string $key Name of the string place holder.
    * @param string $value Replacement value.
    *
    * @since 1.17
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   public function setStringReplacement($key, $value) {
      $this->stringReplacement[strtoupper($key)] = $value;
   }

   /**
    * @public
    *
    * Implements the transform() method. Returns the content of the tag, that is set by a
    * document controller using the BaseDocumentController's setPlaceHolder() method.
    *
    * @return string The content of the place holder.
    *
    * @author Christian Schäfer, Jan Wiese
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 06.02.2013 (Added string place holder support)<br />
    */
   public function transform() {
      // preserve content to allow multiple transformation
      $content = $this->content;
      foreach ($this->stringReplacement as $key => $value) {
         $content = str_replace('{' . $key . '}', $value, $content);
      }
      return $content;
   }

}

/**
 * @package APF\core\pagecontroller
 * @class TemplateTagClearApproach
 *
 * Defines the way a TemplateTag instance can be cleared using it within a loop
 * before displaying the next item.
 * <p/>
 * This is especially necessary, in case place holders for the next line of a
 * repeating output (e.g. lines of a table) should be cleared to conditionally
 * contain no content.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2013<br />
 */
interface TemplateTagClearApproach {

   /**
    * @public
    *
    * Clears the applied TemplateTag according to the implementation.
    *
    * @param TemplateTag $node The current TemplateTag node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.05.2013<br />
    */
   public function clear(TemplateTag &$node);
}

/**
 * @package APF\core\pagecontroller
 * @class DefaultTemplateTagClearApproach
 *
 * Implements the default clear mechanism that resets the content of the place holders
 * defines within the TemplateTag instance.
 * <p/>
 * Please note, that this implementation only covers instances of the PlaceHolderTag
 * class that is the standard place holder tag within the APF. In case you intend to
 * add your own implementation of place holders or elements that need to be cleared,
 * please implement your custom clear strategy and apply to the <em>TemplateTag::clear()</em>
 * method within the respective document controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2013<br />
 */
class DefaultTemplateTagClearApproach implements TemplateTagClearApproach {

   public function clear(TemplateTag &$node) {
      $children = & $node->getChildren();
      foreach ($children as $objectId => $DUMMY) {
         if ($children[$objectId] instanceof PlaceHolderTag) {
            $children[$objectId]->setContent('');
         }
         if ($children[$objectId] instanceof LanguageLabelTag) {
            /* @var $children LanguageLabelTag[] */
            $children[$objectId]->clearPlaceHolders();
         }
      }
   }

}

/**
 * @package APF\core\pagecontroller
 * @class TemplateTag
 *
 * Represents a reusable html fragment (template) within a template file. The tag's functionality
 * can be extended by the &lt;template:addtaglib /&gt; tag. Use setPlaceHolder() to set a place
 * holder's value and transformOnPlace() or transformTemplate() to generate the output.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 10.11.2008 (Removed the IncludedTagLib behavior, because this lead to errors when including new taglibs with template:addtaglib.)<br />
 */
class TemplateTag extends Document {

   /**
    * @protected
    * Indicates, if the template should be transformed on the place of definition. Default is false.
    */
   protected $transformOnPlace = false;

   /**
    * @public
    *
    * Constructor of the class. Initializes the known taglibs.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 30.12.2006 (Added the template:config tag)<br />
    * Version 0.3, 05.01.2007 (Added the template:addtaglib tag)<br />
    * Version 0.4, 12.01.2007 (Removed the template:addtaglib tag)<br />
    * Version 0.5, 03.03.2007 (Removed the "&" before the "new" operator)<br />
    * Version 0.6, 21.04.2007 (Added the template:addtaglib tag again)<br />
    * Version 0.7, 02.05.2007 (Removed the template:config tag)<br />
    * Version 0.8, 11.02.2012 (Added template:getstring tag as known tag (refactoring!))<br />
    */
   public function __construct() {
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'template', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'template', 'addtaglib');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'template', 'getstring');
   }

   /**
    * @public
    *
    * Implements the onParseTime() method from the APFObject class. Uses the extractTagLibTags()
    * function to parse the known taglibs.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 31.12.2006<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * @public
    *
    * Let's you retrieve an &lt;template:getstring /&gt; tag instance with the specified name.
    *
    * @param string $name The name of the template label to return.
    * @return LanguageLabelTag The instance of the desired label.
    * @throws \InvalidArgumentException In case no label can be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.01.2012<br />
    */
   public function &getLabel($name) {
      try {
         return $this->getChildNode('name', $name, 'APF\core\pagecontroller\LanguageLabelTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[TemplateTag::getLabel()] No label found with name "' . $name
            . '" composed in template with name "' . $this->getAttribute('name') . '" for document controller "'
            . $this->getParentObject()->getDocumentController() . '"! Perhaps, the tag library for template:getstring '
            . 'is not loaded.', E_USER_ERROR, $e);
      }
   }

   /**
    * @public
    *
    * Returns the content of the template. Can be used to generate the template output
    * within a document controller. Usage:
    * <pre>
    * $template = &$this->getTemplate('MyTemplate');
    * $template->setPlaceHolder('URL','http://adventure-php-framework.org');
    * echo $template->transformTemplate();
    * </pre>
    *
    * @return string The content of the transformed template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 31.12.2006 (Removed parameter $this->isVisible, because the parent object automatically removes the XML positioning tag on transformation now)<br />
    * Version 0.3, 02.02.2007 (Renamed method to transformTemplate(). Removed visible marking finally from the class.)<br />
    * Version 0.4, 05.01.2007 (Added the template:addtaglib tag)<br />
    */
   public function transformTemplate() {
      return $this->transformChildrenAndPreserveContent();
   }

   /**
    * @public
    *
    * Allows you to clear a TemplateTag instance according to the applied clear approach.
    * <p/>
    * By default, the <em>DefaultTemplateTagClearApproach</em> is used reset the template's
    * place holder tags by removing their content.
    * <p/>
    * In case you intend to add your own implementation of place holders or elements that need
    * to be cleared, please implement your custom clear strategy and apply to this method.
    *
    * @param TemplateTagClearApproach $approach The clear approach to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.05.2013<br />
    */
   public function clear(TemplateTagClearApproach $approach = null) {
      if ($approach === null) {
         $approach = new DefaultTemplateTagClearApproach();
      }
      $approach->clear($this);
   }

   /**
    * @public
    *
    * Indicates, that the template should be displayed on the place of definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.05.2008<br />
    */
   public function transformOnPlace() {
      $this->transformOnPlace = true;
   }

   /**
    * @public
    *
    * By default, the content of the template is returned as an empty string. This is because the
    * html:template tag normally is used as a reusable fragment. If the transformOnPlace() function
    * is called before, the content of the template is returned instead.
    *
    * @return string Empty string or content of the tag
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.01.2007<br />
    * Version 0.2, 12.01.2007 (An empty string is now returned)<br />
    * Version 0.3, 19.05.2008 (Implemented the transformOnPlace() feature)<br />
    */
   public function transform() {

      // checks, if transformOnPlace is activated
      if ($this->transformOnPlace === true) {
         return $this->transformTemplate();
      }

      // return empty string
      return '';
   }

}

/**
 * @package APF\core\pagecontroller
 * @class LanguageLabelTag
 *
 * Implements a class for the taglibs &lt;html:getstring /&gt; and &lt;template:getstring /&gt;. This
 * lib fetches the desired configuration value and returns it on transformation time. The configuration
 * files must be structured as follows:
 * <p/>
 * <pre>
 * [de]
 * key = "german value"
 *
 * [en]
 * key = "english value"
 *
 * ...
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.04.2006<br />
 * Version 0.2, 17.09.2009 (Refactored due to form taglib changes)<br />
 * Version 0.3, 11.02.2012 (Added LanguageLabelTag to core (refactoring!))
 */
class LanguageLabelTag extends Document {

   /**
    * @var array A list of place holder names and values.
    */
   private $placeHolders = array();

   public function __construct() {
      // do nothing, especially not initialize tag libs
   }

   /**
    * @public
    *
    * Implements the functionality to retrieve a language dependent value form a
    * configuration file. Checks the attributes needed for displaying data.
    *
    * @return string The desired translation text.
    * @throws \InvalidArgumentException In case of parameter issues.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2006<br />
    * Version 0.2, 17.10.2008 (Enhanced error messages)<br />
    */
   public function transform() {

      // check for attribute "namespace"
      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new \InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
            . '"namespace" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "config"
      $configName = $this->getAttribute('config');
      if ($configName === null) {
         throw new \InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
            . '"config" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "entry"
      $entry = $this->getAttribute('entry');
      if ($entry === null) {
         throw new \InvalidArgumentException('[' . get_class($this) . '->transform()] No attribute '
            . '"entry" given in tag definition!', E_USER_ERROR);
      }

      // get configuration values
      $config = $this->getConfiguration($namespace, $configName);
      $value = $config->getSection($this->getLanguage()) === null
         ? null
         : $config->getSection($this->getLanguage())->getValue($entry);

      if ($value == null) {

         // get environment variable from registry to have nice exception message
         $env = Registry::retrieve('APF\core', 'Environment');

         throw new \InvalidArgumentException('[' . get_class($this) . '::transform()] Given entry "'
            . $entry . '" is not defined in section "' . $this->getLanguage() . '" in configuration "'
            . $env . '_' . $configName . '" in namespace "' . $namespace . '" and context "'
            . $this->getContext() . '"!', E_USER_ERROR);
      }
      return $this->replace($value);
   }

   /**
    * @public
    *
    * Let's you add a place holder that is replaced into the current label. Each place holder
    * must be defined with square brackets ("{" and "}") with the key between the opening and
    * the closing bracket (e.g. "{foo}" in case the name of the place holder is "foo").
    *
    * @param string $name The name of the place holder.
    * @param string $value The value of the place holder.
    * @return LanguageLabelTag This instance for further usage (e.g. adding further place holders).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.01.2012<br />
    */
   public function &setPlaceHolder($name, $value) {
      $this->placeHolders[$name] = $value;
      return $this;
   }

   /**
    * @public
    *
    * Resets the list of place holders that have been defined so far.
    *
    * @return LanguageLabelTag This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.05.2013<br />
    */
   public function &clearPlaceHolders() {
      $this->placeHolders = array();
      return $this;
   }

   /**
    * @private
    *
    * Replaces all place holders within the current label that are registered within this instance.
    *
    * @param string $label The raw label.
    * @return string The label with replaced place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.01.2012<br />
    */
   protected function replace($label) {
      foreach ($this->placeHolders as $key => $value) {
         $label = str_replace('{' . $key . '}', $value, $label);
      }
      return $label;
   }

}


/**
 * @package APF\core\pagecontroller
 * @class DocumentController
 *
 * Defines the interface for APF document controller implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.02.2013<br />
 */
interface DocumentController extends APFService {

   /**
    * @public
    *
    * Injects the content of the current Document the controller is responsible for. Since the
    * content is retrieved by the current Document after executing {@link transformContent}
    * the developer is able to modify the content if the current node within a document controller.
    *
    * @param string $content The content of the current Document node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2013<br />
    */
   public function setContent($content);

   /**
    * @public
    *
    * Let's the current Document retrieve the (potentially) modified content after transformation.
    *
    * @return  string The content of the current Document node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2013<br />
    */
   public function getContent();

   /**
    * @public
    *
    * Injects the document into the document controller. This enables the developer
    * to retrieve information and DOM elements stored in the node, the controller
    * is responsible to transform.
    *
    * @param Document $document The dom node, the controller is intended to transform.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setDocument(Document &$document);

   /**
    * Returns the document that represents the present DOM node the
    * controller is responsible for.
    *
    * @return Document The present DOM node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.10.2010<br />
    */
   public function &getDocument();

   /**
    * @public
    *
    * Interface definition of the transformContent() method. This function is applied to a
    * document controller during the transformation of a DOM node. It must be implemented by
    * each document controller to influence content generation.
    *
    * @return void
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function transformContent();
}

/**
 * @package APF\core\pagecontroller
 * @class BaseDocumentController
 * @abstract
 *
 * Defines the base class for all document controller classes. To add custom logic, implement
 * the {@link transformContent} method.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 04.11.2007 (Removed the isButtonPushed() method)<br />
 * Version 0.3, 09.02.2013 (Introduced the DocumentController interface)<br />
 */
abstract class BaseDocumentController extends APFObject implements DocumentController {

   /**
    * @var Document References the document, the document controller is responsible for transformation.
    */
   protected $document;

   /**
    * @var string The content of the Document the controller is responsible for.
    */
   protected $content;

   public function setDocument(Document &$document) {
      $this->document = & $document;
   }

   public function &getDocument() {
      return $this->document;
   }

   public function setContent($content) {
      $this->content = $content;
   }

   public function getContent() {
      return $this->content;
   }

   /**
    * @public
    *
    * Sets the given value as the content of the specified place holder.
    *
    * @param string $name The name of the place holder to fill.
    * @param string $value The value to insert into the place holder.
    * @throws \InvalidArgumentException In case the place holder cannot be found.
    *
    * @author Christian Schäfer, Jan Wiese
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 09.02.2013 (Switched to Document::setPlaceHolder() implementation)<br />
    */
   protected function setPlaceHolder($name, $value) {
      try {
         $this->document->setPlaceHolder($name, $value);
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::setPlaceHolder()] No place holders '
            . 'found for name "' . $name . '" in document controller "' . get_class($this) . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * @protected
    *
    * Convenience method to replace string place holders.
    * <p/>
    * Template:
    * <code>
    * <html:placeholder name="foo"/>
    * <html:placeholder name="foo">I am an {FAN-TYPE}</html:placeholder>
    * </code>
    * Controller:
    * <code>
    * $this->setPlaceholder('foo', 'Ich bin {FAN-TYPE}');
    * $this->setStringPlaceholder('foo', 'FAN-TYPE', 'APF-Fan!');
    * </code>
    *
    * @param string $name Place holder name.
    * @param string $key Key name of string place holder.
    * @param string $value Value, the string place holder is replaced with.
    * @throws \InvalidArgumentException In case no place holder has been found.
    *
    * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
    * @version
    * Version 0.1, 03.10.2012<br />
    */
   protected function setStringPlaceHolder($name, $key, $value) {
      try {
         $this->document->setStringPlaceHolder($name, $key, $value);
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::setStringPlaceHolder()] No place holders '
            . 'found for name "' . $name . '" in document controller "' . get_class($this) . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * @protected
    *
    * This method is for convenient setting of multiple place holders. The applied
    * array must contain a structure like this:
    * <code>
    * array(
    *    'key-a' => 'value-a',
    *    'key-b' => 'value-b',
    *    'key-c' => 'value-c',
    *    'key-d' => 'value-d',
    *    'key-e' => 'value-e',
    * )
    * </code>
    * Thereby, the <em>key-*</em> offsets define the name of the place holders, their
    * values are used as the place holder's values.
    *
    * @param string[] $placeHolderValues Key-value-couples to fill place holders.
    * @throws \InvalidArgumentException In case one of the place holders cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    */
   protected function setPlaceHolders(array $placeHolderValues) {
      $this->getDocument()->setPlaceHolders($placeHolderValues);
   }

   /**
    * @protected
    *
    * Set's a place holder in case it exists. Otherwise it is ignored.
    *
    * @param string $name The name of the place holder.
    * @param string $value The place holder's value.
    *
    * @author Christian Achatz, Werner Liemberger
    * @version
    * Version 0.1, 02.07.2011<br />
    */
   protected function setPlaceHolderIfExist($name, $value) {
      try {
         $this->setPlaceHolder($name, $value);
      } catch (\Exception $e) {
         $log = & Singleton::getInstance('APF\core\logging\Logger');
         /* @var $log Logger */
         $log->addEntry(
            new SimpleLogEntry(
            // use the configured log target to allow custom configuration of APF-internal log statements
            // to be written to a custom file/location
               Registry::retrieve('APF\core', 'InternalLogTarget'),
               'Place holder with name "' . $name . '" does not exist within the current document '
                  . 'handled by document controller "' . get_class($this) . '". '
                  . 'Please check your setup. Details: ' . $e,
               LogEntry::SEVERITY_WARNING
            )
         );
      }
   }

   /**
    * @protected
    *
    * This method is for convenient setting of multiple place holders in case they exist within
    * the current document. See <em>BaseDocumentController::setPlaceHolderIfExist()</em> for details.
    *
    * @param array $placeHolderValues Key-value-couples to fill place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.07.2011<br />
    */
   protected function setPlaceHoldersIfExist(array $placeHolderValues) {
      foreach ($placeHolderValues as $key => $value) {
         $this->setPlaceHolderIfExist($key, $value);
      }
   }

   /**
    * @protected
    *
    * Returns the instance of the form specified by the given name. This method can be used to
    * access a form object within a document controller.
    *
    * @param string $formName The name of the form to return.
    * @return HtmlFormTag The instance of the desired form.
    * @throws \InvalidArgumentException In case the form cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 14.06.2008 (Improved error handling.)<br />
    */
   protected function &getForm($formName) {
      try {
         return $this->getDocument()->getChildNode('name', $formName, 'APF\tools\form\taglib\HtmlFormTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::getForm()] No form object with name "'
            . $formName . '" composed in current document for document controller "' . get_class($this)
            . '"! Perhaps tag library html:form is not loaded in current document!', E_USER_ERROR, $e);
      }
   }

   /**
    * @protected
    *
    * Returns the instance of the template specified by the given name. This method can be used
    * to access a html template object within a document controller.
    *
    * @param string $name The name of the template to return.
    * @return TemplateTag The desired template instance.
    * @throws \InvalidArgumentException In case the template cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.01.2007 (Bug fix: now not only the first template is returned)<br />
    * Version 0.3, 12.01.2006 (Renamed from "__getContentTemplate" to "__getTemplate" due to the introduction of "__getForm")<br />
    * Version 0.4, 23.04.2009 (Corrected PHP4 style object access)<br />
    */
   protected function &getTemplate($name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, 'APF\core\pagecontroller\TemplateTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::getTemplate()] No template with name "'
            . $name . '" composed in current document for document controller "' . get_class($this)
            . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * @protected
    *
    * Let's you retrieve an instance of the LanguageLabelTag label instance to
    * fill a place holder.
    *
    * @param string $name The content of the tag's "name" attribute to select the node.
    * @return LanguageLabelTag The instance of the desired label node.
    * @throws \InvalidArgumentException In case no label node can be found.
    */
   protected function &getLabel($name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, 'APF\core\pagecontroller\LanguageLabelTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::getLabel()] No label with name "'
            . $name . '" composed in current document for document controller "' . get_class($this)
            . '"! Perhaps tag library html:getstring is not loaded in current template!', E_USER_ERROR, $e);
      }
   }

   /**
    * @protected
    *
    * Checks, if a place holder exists within the current document.
    *
    * @param string $name The name of the place holder.
    * @return bool True if yes, false otherwise.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 11.03.2007<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 02.07.2011 (Renaming to fit the APF naming convention)<br />
    */
   protected function placeHolderExists($name) {
      try {
         $this->getDocument()->getChildNode('name', $name, 'APF\core\pagecontroller\PlaceHolderTag');
         return true;
      } catch (\InvalidArgumentException $e) {
         return false;
      }
   }

   /**
    * @protected
    *
    * Checks, if a place holder exists within the given template.
    *
    * @param TemplateTag $template The instance of the template to check.
    * @param string $name The name of the place holder.
    * @return bool True if yes, false otherwise.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 11.03.2007<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 02.07.2011 (Renaming to fit the APF naming convention)<br />
    */
   protected function templatePlaceHolderExists(TemplateTag &$template, $name) {
      try {
         $template->getChildNode('name', $name, 'APF\core\pagecontroller\PlaceHolderTag');
         return true;
      } catch (\InvalidArgumentException $e) {
         return false;
      }
   }

   /**
    * @protected
    *
    * Returns a reference on the desired iterator.
    *
    * @param string $name Name of the iterator.
    * @return HtmlIteratorTag The desired iterator.
    * @throws IncludeException In case the iterator taglib is not loaded.
    * @throws \InvalidArgumentException In case the desired iterator cannot be returned.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.06.2008<br />
    */
   protected function &getIterator($name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, 'APF\tools\html\taglib\HtmlIteratorTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::getIterator()] No iterator with name "'
            . $name . '" composed in current document for document controller "' . get_class($this) . '"! '
            . 'Perhaps tag library html:iterator is not loaded in current template!', E_USER_ERROR, $e);
      }
   }

}
