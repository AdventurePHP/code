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

   /**
    * @file pagecontroller.php
    *
    * Setups the framework's core environment. Initializes the Registry, that stores parameters,
    * that are used within the complete framework. These are
    * <ul>
    * <li>Environment      : environment, the application is executed in. The value is 'DEFAULT' in common</li>
    * <li>URLRewriting     : indicates, is url rewriting should be used</li>
    * <li>LogDir           : path, where logfiles are stored. The value is './logs' by default.</li>
    * <li>URLBasePath      : absolute url base path of the application (not really necessary)</li>
    * <li>LibPath          : path, where the framework and your own libraries reside. This path can be used
    *                        to adress files with in the lib path directly (e.g. images or other ressources)</li>
    * <li>CurrentRequestURL: the fully qualified request url</li>
    * </ul>
    * Further, the built-in input and output filters are initialized. For this reason, the following
    * registry entries are created within the "apf::core::filter" namespace:
    * <ul>
    * <li>PageControllerInputFilter : the definition of the input filter</li>
    * <li>OutputFilter              : the definition of the output filter</li>
    * </ul>
    * The file also contains the pagecontroller core implementation with the classes Page,
    * Document, TagLib, APFObject, XmlParser and base_controller (the basic MVC document controller).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2008<br />
    * Version 0.2, 16.07.2008 (added the LibPath to the registry namespace apf::core)
    * Version 0.3, 07.08.2008 (Made LibPath readonly)<br />
    * Version 0.4, 13.08.2008 (Fixed some timing problems with the registry initialisation)<br />
    * Version 0.5, 14.08.2008 (Changed LogDir initialisation to absolute paths)<br />
    * Version 0.6, 05.11.2008 (Added the 'CurrentRequestURL' attribute to the 'apf::core' namespace of the registry)<br />
    * Version 0.7, 11.12.2008 (Added the input and output filter initialization)<br />
    * Version 0.8, 01.02.2009 (Added the protocol prefix to the URLBasePath)<br />
    * Version 0.9, 21.02.2009 (Added the exception handler, turned off the php5 support in the import() function of the PHP4 branch)<br />
    */

   /////////////////////////////////////////////////////////////////////////////////////////////////
   // Define the internally used base path for the adventure php framework libraries.             //
   /////////////////////////////////////////////////////////////////////////////////////////////////

   // get current path
   $path = explode('/',str_replace('\\','/',dirname(__FILE__)));

   // get relevant segments
   $count = count($path);
   $appsPath = array();
   for($i = 0; $i < $count; $i++){

      if($path[$i] != 'core'){
         $appsPath[] = $path[$i];
       // end if
      }
      else{
         break;
       // end else
      }

    // end for
   }

   // define the APPS__PATH constant to be used in the import() function (performance hack!)
   define('APPS__PATH',implode($appsPath,'/'));

   /////////////////////////////////////////////////////////////////////////////////////////////////

   // include core libraries for the basic configuration
   import('core::singleton','Singleton');
   import('core::registry','Registry');

   // define base parameters of the framework's core and tools layer
   $reg = &Singleton::getInstance('Registry');
   $reg->register('apf::core','Environment','DEFAULT');
   $reg->register('apf::core','URLRewriting',false);
   $reg->register('apf::core','LogDir',str_replace('\\','/',getcwd()).'/logs');
   $reg->register('apf::core','LibPath',APPS__PATH,true);

   // define current request url entry
   $protocol = null;
   if($_SERVER['SERVER_PORT'] == '443'){
      $protocol = 'https://';
    // end if
   }
   else{
      $protocol = 'http://';
    // end else
   }
   $reg->register('apf::core','URLBasePath',$protocol.$_SERVER['HTTP_HOST']);
   $reg->register('apf::core','CurrentRequestURL',$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],true);

   // include necessary core libraries for the pagecontroller
   import('core::errorhandler','errorhandler');
   import('core::exceptionhandler','exceptionhandler');
   import('core::service','ServiceManager');
   import('core::service','DIServiceManager');
   import('core::configuration','ConfigurationManager');
   import('core::benchmark','BenchmarkTimer');
   import('core::filter','FilterFactory');

   // set up the input and output filter
   $reg->register('apf::core::filter','PageControllerInputFilter',new FilterDefinition('core::filter','PageControllerInputFilter'));
   $reg->register('apf::core::filter','OutputFilter',new FilterDefinition('core::filter','GenericOutputFilter'));

   /**
    * @package core::pagecontroller
    * @class IncludeException
    *
    * This exception represents an error loading resources (modules,
    * templates, ...) within the page controller.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.03.2010<br />
    */
   class IncludeException extends Exception {
   }

   /**
    * @package core::pagecontroller
    * @function import
    *
    * Imports classes or modules from a given namespace.
    * <p/>
    * Usage:
    * <pre>import('core::frontcontroller','Frontcontroller');</pre>
    *
    * @param string $namespace the namespace of the file (=relative path, starting at the root of your code base).
    * @param string $file the body of the desired file / class to include (without extension).
    * @throws Exception In case the requested component does not exist.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.12.2005<br />
    * Version 0.2, 14.04.2006<br />
    * Version 0.3, 14.01.2007 (Implemented proprietary support for PHP 5)<br />
    * Version 0.4, 03.03.2007 (Did some cosmetics)<br />
    * Version 0.5, 03.03.2007 (Added support for mixed operation under PHP 4 and PHP 5)<br />
    * Version 0.6, 24.03.2008 (Improved Performance due to include cache introduction)<br />
    * Version 0.7, 20.06.2008 (Moved to pagecontroller.php due to the Registry introduction)<br />
    * Version 0.8, 13.11.2008 (Replaced the include_once() calls with include()s to gain performance)<br />
    * Version 0.9, 25.03.2009 (Cleared implementation for the PHP 5 branch)<br />
    * Version 1.0, 08.03.2010 (Introduced exception instead of trigger_error())<br />
    */
   function import($namespace,$file){

      // create the complete and absolute file name
      $file = APPS__PATH.'/'.str_replace('::','/',$namespace).'/'.$file.'.php';

      // check if the file is already included, if yes, return
      if(isset($GLOBALS['IMPORT_CACHE'][$file])){
         return true;
      }
      else{
         $GLOBALS['IMPORT_CACHE'][$file] = true;
      }

      // handle non-existing files
      if(!file_exists($file)){
         throw new IncludeException('[import()] The given module ('.$file.') cannot be loaded!',E_USER_ERROR);
      }
      else{
         include($file);
      }

    // end function
   }

   /**
    * @package core::pagecontroller
    * @function printObject
    * @see http://php.net/print_r
    *
    * Creates a print_r() output of the given object, array, string or integer.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 04.02.3006<br />
    * Version 0.2, 23.04.2006 (The output is now returned instead of printed directly)<br />
    */
   function printObject($o,$transformHtml = false){

      $buffer = (string)'';
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "<br />\n";
      $buffer .= "<strong>\n";
      $buffer .= "Output of printObject():\n";
      $buffer .= "</strong>\n";
      $buffer .= "<br />\n";
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "\n<pre>";

      if($transformHtml == true){
         $buffer .= htmlentities(print_r($o,true));
       // end if
      }
      else{
         $buffer .= print_R($o,true);
       // end else
      }

      $buffer .= "</pre>\n";
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "<br />\n";
      $buffer .= "<br />\n";

      return $buffer;

    // end function
   }

   /**
    * @package core::pagecontroller
    * @class ParserException
    *
    * Represents a APF parser exception.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.03.2010<br />
    */
   class ParserException extends Exception {
   }

   /**
    * @package core::pagecontroller
    * @class XmlParser
    *
    * Static parser for XML / XSL Strings.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 22.12.2006<br />
    */
   final class XmlParser {

      private function XmlParser(){
      }

      /**
       * @public
       * @static
       *
       * Extracts the attributes from an XML attributes string.
       *
       * @param string $tagString The string, that contains the tag definition.
       * @return string[] The attributes of the tag.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.12.2006<br />
       * Version 0.2, 30.12.2006 (Bugfix: tag-to-attribute delimiter is now a constant value)<br />
       * Version 0.3, 03.01.2007<br />
       * Version 0.4, 13.01.2007 (Improved error messages)<br />
       * Version 0.5, 16.11.2007 (Improved error message. Now affected tag string is displayed, too)<br />
       * Version 0.6, 03.11.2008 (Fixed the issue, that a TAB character is no valid token to attributes delimiter)<br />
       * Version 0.7, 04.11.2008 (Fixed issue, that a combination of TAB and SPACE characters leads to wrong attributes parsing)<br />
       * Version 0.8, 05.11.2008 (Removed the TAB support due to performance and fault tolerance problems)<br />
       */
      public static function getTagAttributes($tagString){

         // search for taglib to attributes string delimiter
         $tagAttributeDel = strpos($tagString,' ');

         // search for the closing sign
         $posTagClosingSign = strpos($tagString,'>');

         // In case, the separator between tag and attribute is not found, or in case the tag
         // end position is located between the tag and the attribute, the end sign (">") is used
         // as separator. This allows tags without attributes.
         if($tagAttributeDel === false || $tagAttributeDel > $posTagClosingSign){
            $tagAttributeDel = strpos($tagString,'>');
          // end if
         }

         // search for separator between prefix and class
         $prefixDel = strpos($tagString,':');

         // gather class
         $class = substr($tagString,$prefixDel + 1,$tagAttributeDel - ($prefixDel +1));

         // gather prefix
         $prefix = substr($tagString,1,$prefixDel - 1);

         // search for the first appearance of the closing sign after the attribute string
         $posEndAttrib = strpos($tagString,'>');

         // extract the rest of the tag string.
         $attributesString = substr($tagString,$tagAttributeDel + 1,$posEndAttrib - $tagAttributeDel);

         // parse the tag's attributes
         $tagAttributes = XmlParser::getAttributesFromString($attributesString);

         // Check, whether the tag is self-closing. If not, read the content.
         $content = null;
         if(substr($tagString,$posEndAttrib - 1,1) == '/'){
            $content = (string)'';
          // end if
         }
         else{

            // initialize the content as empty string
            $content = (string)'';

            // check, if explicitly-closing tag exists
            if(strpos($tagString,'</'.$prefix.':'.$class.'>') === false){
               throw new ParserException('[XmlParser::getTagAttributes()] No closing tag found for tag "&lt;'
                       .$prefix.':'.$class.' /&gt;"! Tag string: "'.htmlentities($tagString).'".',
                       E_USER_ERROR);
             // end if
            }
            else{

               $found = true;
               $offset = 0;
               $posEndContent = 0;
               $count = 0;
               $maxCount = 10;
               $endTag = '</'.$prefix.':'.$class.'>';

               while($found == true){

                  // save old value
                  $posEndContent = $offset;

                  // reasign the position of the tag end limiter
                  $offset = strpos($tagString,$endTag,$offset + 1);

                  // in case no futher position is found -> end at this point
                  if($offset === false){
                     $found = false;
                   // end if
                  }

                  // in case more than max positions are found -> end at this point for security reasons
                  if($count > $maxCount){
                     $found = false;
                   // end if
                  }

                  $count++;

                // end while
               }

               // read the content of the tag
               $content = substr($tagString,$posEndAttrib + 1,($posEndContent - $posEndAttrib) - 1);

             // end else
            }

          // end else
         }

         $attributes = array();
         $attributes['attributes'] = $tagAttributes;
         $attributes['class'] = $class;
         $attributes['prefix'] = $prefix;
         $attributes['content'] = $content;
         return $attributes;

       // end function
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
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 22.12.2006<br />
       * Version 0.2, 30.12.2006 (Enhanced the documentation)<br />
       * Version 0.3, 14.01.2007 (Improved the error message)<br />
       * Version 0.4, 14.11.2007 (Removed $hasFound; see http://forum.adventure-php-framework.org/de/viewtopic.php?t=7)<br />
       */
      public static function getAttributesFromString($attributesString){

         $attributes = array ();
         $foundAtr = true;
         $offset = 0;

         $parserLoops = 0;
         $parserMaxLoops = 20;

         while(true){

            $parserLoops++;

            // limit parse loop count to avoid enless while loops
            if($parserLoops == $parserMaxLoops){
               throw new ParserException('[XmlParser::getAttributesFromString()] Error while parsing: "'.htmlentities($attributesString).'". Maximum number of loops exceeded!',E_USER_ERROR);
             // end if
            }

            // find attribute
            $foundAtr = strpos($attributesString, '=', $offset);

            // if no attribute was found -> end at this point
            if($foundAtr === false){
                break;
             // end if
            }

            // extract values
            $key = substr($attributesString, $offset, $foundAtr - $offset);
            $attrValueStart = strpos($attributesString, '"', $foundAtr);
            $attrValueStart++;
            $attrValueEnd = strpos($attributesString, '"', $attrValueStart);
            $attrValue = substr($attributesString, $attrValueStart, $attrValueEnd - $attrValueStart);
            $offset = $attrValueEnd + 1;

            // add to key => value array
            $attributes[trim($key)] = trim($attrValue);

          // end while
         }

         return $attributes;

       // end function
      }

      /**
       * @public
       * @static
       *
       * Generates a uniqe id, that is used as the object id for the APF DOM tree.
       *
       * @return string The unique id used as GUID for the APF DOM tree.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 22.12.2006<br />
       */
      public static function generateUniqID($md5 = true){

         if($md5 == true){
            return md5(uniqid(rand(),true));
          // end if
         }
         else{
            return uniqid(rand(),true);
          // end else
         }

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
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
    * Version 0.3, 28.10.2008 (Added the __ServiceType member to indicate the service manager creation type)<br />
    * Version 0.4, 03.11.2008 (Added initializing values to some of the class members)<br />
    */
   abstract class APFObject {

      /**
       * @protected
       * @var string Unique object identifier.
       */
      protected $__ObjectID = null;

      /**
       * @protected
       * @var APFObject Reference to the parent object.
       */
      protected $__ParentObject = null;

      /**
       * @protected
       * @var APFObject[] List of the children of the current object.
       */
      protected $__Children = array();

      /**
       * @protected
       * @var string[] The attributes of an object (merely the XML tag attributes).
       */
      protected $__Attributes = array();

      /**
       * @protected
       * @var string The context of the current object within the application.
       */
      protected $__Context = null;

      /**
       * @protected
       * @var string The language of the current object within the application.
       */
      protected $__Language = 'de';

      /**
       * @since 0.3
       * @protected
       * @var string Contains the service type, if the object was created with the ServiceManager.
       */
      protected $__ServiceType = null;

      public function APFObject(){
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
      public function setObjectId($objectId){
         $this->__ObjectID = $objectId;
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
      public function getObjectId(){
         return $this->__ObjectID;
      }

      /**
       * @public
       *
       * Sets the context of the current APF object.
       *
       * @param string $context The context.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function setContext($context){
         $this->__Context = $context;
      }

      /**
       * @public
       *
       * Returns the context of the current APF object.
       *
       * @return string The context.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getContext(){
         return $this->__Context;
      }

      /**
       * @public
       *
       * Sets the language of the current APF object.
       *
       * @param string $lang The language.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function setLanguage($lang){
         $this->__Language = $lang;
      }

      /**
       * @public
       *
       * Returns the language of the current APF object.
       *
       * @return string The language.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getLanguage(){
         return $this->__Language;
      }

      /**
       * @public
       *
       * Sets the service type of the current APF object.
       *
       * @param string $serviceType The service type.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function setServiceType($serviceType){
         $this->__ServiceType = $serviceType;
      }

      /**
       * @public
       *
       * Returns the service type of the current APF object.
       *
       * @return string The service type.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getServiceType(){
         return $this->__ServiceType;
      }

      /**
       * @public
       *
       * Injects the parent node of the current APF object.
       *
       * @param APFObject $parentObject The parent node.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function setParentObject(&$parentObject){
         $this->__ParentObject = &$parentObject;
      }

      /**
       * @public
       *
       * Returns the parent node of the current APF object.
       *
       * @return string The parent node.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function &getParentObject(){
         return $this->__ParentObject;
      }

      /**
       * @public
       *
       * Returns the list of the current node's children.
       *
       * @return APFObject[] The current node's children.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function &getChildren(){
         return $this->__Children;
      }

      /**
       * @public
       *
       * Retrieves an object's property.
       *
       * @param string $attributeName Name of an object's property.
       * @return string The property's value.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 02.02.2007 (In case the attribute does not exist, null is returned now)<br />
       */
      public function get($Attribute){

         if(isset($this->{'__'.$Attribute})){
            return $this->{'__'.$Attribute};
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Sets an object's property.
       *
       * @param string $attributeName Name of an object's property.
       * @param string $value The value to set.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function set($attributeName,$value){
         $this->{'__'.$attributeName} = $value;
       // end function
      }

      /**
       * @public
       *
       * Appends a value to a given attribute list.
       *
       * @param string $attributeName Name of the attribute to set.
       * @param string $value Value of the attribute.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 11.11.2007<br />
       */
      public function add($attributeName,$value){
         $this->{'__'.$attributeName}[] = $value;
       // end function
      }

      /**
       * @public
       *
       * Returns the object's attribute.
       *
       * @param string $name The name of the desired attribute.
       * @return string Returns the attribute's value or null in case of errors.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 02.02.2007 (Behandlung, falls Attribut nicht existiert hinzugef�gt)<br />
       */
      public function getAttribute($name){

         if(isset($this->__Attributes[$name])){
            return $this->__Attributes[$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Sets an object's attribute.
       *
       * @param string $name Name des Attributes, dessen Wert zur�ckgeliefert werden soll.
       * @param string $value Wert des Attributes, dessen Wert gesetzt werden soll.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function setAttribute($name,$value){
         $this->__Attributes[$name] = $value;
       // end function
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
      public function getAttributes(){
         return $this->__Attributes;
       // end function
      }

      /**
       * @public
       *
       * Deletes an attribute.
       *
       * @param string $name The name of the attribute to delete.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function deleteAttribute($name){
         unset($this->__Attributes[$name]);
       // end function
      }

      /**
       * @public
       *
       * Sets an object's attributes.
       *
       * @param string[] $attributes The attributes list.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function setAttributes($attributes = array()){

         if(is_array($attributes) && count($attributes) > 0){

            if(!is_array($this->__Attributes)){
               $this->__Attributes = array();
             // end if
            }

            $this->__Attributes = array_merge($this->__Attributes,$attributes);

          // end if
         }

       // end function
      }

      /**
       * Interface definition of the <em>transform()</em> method. This function is used to
       * transform a DOM node within the page controller. It must be implemented by derived classes.
       *
       * @public
       * @abstract
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function transform(){
      }

      /**
       * Interface definition of the init() method. This function is used to initialize a service
       * object with the service manager. It must be implemented by derived classes.
       *
       * @public
       * @abstract
       *
       * @param string $initParam The initializing value of the service object. Data type may also be array or object.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 30.03.2007<br />
       */
      public function init($initParam){
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
      public function onParseTime(){
      }

      /**
       * Interface definition of the onAfterAppend() method. This function is called after the DOM
       * node is appended to the DOM tree. It must be implemented by derived classes.
       *
       * @public
       * @abstract
       *
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function onAfterAppend(){
      }

      /**
       * Interface definition of the transformContent() method. This function is applied to a
       * document controller during the transformation of a DOM node. It must be implemented by
       * each document controller to influence content generation.
       *
       * @public
       * @abstract
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function transformContent(){
      }

      /**
       * @protected
       *
       * Returns a service object, that is initialized by dependency injection.
       * For details see {@link DIServiceManager}.
       *
       * @param string $namespace The namespace of the service object definition.
       * @param string $name The namne of the service object.
       * @return APFObject The preconfigured service object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 18.04.2009<br />
       */
      protected function &__getDIServiceObject($namespace,$name){

         $diServiceMgr = &Singleton::getInstance('DIServiceManager');
         $diServiceMgr->setContext($this->__Context);
         $diServiceMgr->setLanguage($this->__Language);
         return $diServiceMgr->getServiceObject($namespace,$name);

       // end function
      }

      /**
       * @protected
       *
       * Returns a service object according to the current application context.
       *
       * @param string $namespace Namespace of the service object (currently ignored).
       * @param string $serviceName Name of the service object (=class name).
       * @param string $type The initializing type (see service manager for details).
       * @return APFObject The desired service object.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.03.2007<br />
       * Version 0.2, 08.03.2007 (Context is now taken from the current object)<br />
       * Version 0.3, 10.03.2007 (Method now is considered protected)<br />
       * Version 0.4, 22.04.2007 (Added language initializaton of the service manager)<br />
       * Version 0.5, 24.02.2008 (Added the service type param)<br />
       */
      protected function &__getServiceObject($namespace,$serviceName,$type = 'SINGLETON'){

         $serviceManager = &Singleton::getInstance('ServiceManager');
         $serviceManager->setContext($this->__Context);
         $serviceManager->setLanguage($this->__Language);
         return $serviceManager->getServiceObject($namespace,$serviceName,$type);

       // end function
      }

      /**
       * @protected
       *
       * Returns a initialized service object according to the current application context.
       *
       * @param string $namespace Namespace of the service object (currently ignored).
       * @param string $serviceName Name of the service object (=class name).
       * @param string $InitParam The initialization parameter.
       * @param string $type The initializing type (see service manager for details).
       * @return APFObject The desired service object.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 29.03.2007<br />
       * Version 0.2, 22.04.2007 (Added language initializaton of the service manager)<br />
       * Version 0.3, 24.02.2008 (Added the service type param)<br />
       */
      protected function &__getAndInitServiceObject($namespace,$serviceName,$initParam,$type = 'SINGLETON'){
         $serviceManager = &Singleton::getInstance('ServiceManager');
         $serviceManager->setContext($this->__Context);
         $serviceManager->setLanguage($this->__Language);
         return $serviceManager->getAndInitServiceObject($namespace,$serviceName,$initParam,$type);
       // end function
      }

      /**
       * @protected
       *
       * Returns a configuration object according to the current application context and the given
       * parameters.
       *
       * @param string $namespace The namespace of the configuration file.
       * @param string $configName The name of the configuration file.
       * @param boolean $parseSubsections Indicates, whether the configuration manager should parse subsections.
       * @return Configuration The desired configuration object.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.03.2007<br />
       * Version 0.2, 08.03.2007 (Context is now taken from the current object)<br />
       * Version 0.3, 10.03.2007 (Method now is considered protected)<br />
       */
      protected function &__getConfiguration($namespace,$configName,$parseSubsections = false){
         $configurationManager = &Singleton::getInstance('ConfigurationManager');
         return $configurationManager->getConfiguration($namespace,$this->__Context,$configName,$parseSubsections);
       // end function
      }

      /**
       * @protected
       *
       * Creates a string representation of the given attributes list, using a
       * white list to especially include attributes.
       *
       * @param string[] $attributes The list of attributes to convert to an xml string.
       * @param string[] $whiteList  The list of attributes, the string may contain.
       * @return string The xml attributes string.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010 (Replaced old implementation with the white list feature.)<br />
       */
      protected function __getAttributesAsString($attributes,$whiteList = array()){

         if(count($attributes) > 0){

            $attributeParts = array();

            // process white list entries only, when attribute is given
            // code duplication is done here due to performance reasons!!!
            if(count($whiteList) > 0){
               foreach($attributes as $offset => $value){
                  if(in_array($offset,$whiteList)){
                     $attributeParts[] = $offset.'="'.$value.'"';
                  }
               }
            }
            else{
               foreach($attributes as $offset => $value){
                  $attributeParts[] = $offset.'="'.$value.'"';
               }
            }

            return implode(' ',$attributeParts);

          // end if
         }
         else{
            return (string)'';
          // end else
         }

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class TagLib
    *
    * This class represents a taglib and thus is used as a taglib definition. Each time,
    * you add a known taglib to a DOM node, an instance of the TagLib class is added to
    * the node.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   final class TagLib extends APFObject {

      /**
       * @protected
       * @var string The namespace of the taglib.
       */
      protected $__Namespace;

      /**
       * @protected
       * @var string The prefix of the taglib.
       */
      protected $__Prefix;

      /**
       * @protected
       * @var string The class name of the taglib.
       */
      protected $__Class;

      /**
       * @public
       *
       * Defines a taglib.
       *
       * @param string $namespace The namespace of the taglib.
       * @param string $prefix The prefix of the taglib.
       * @param string $class The class name of the taglib.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function TagLib($namespace,$prefix,$class){
         $this->__Namespace = $namespace;
         $this->__Class = $class;
         $this->__Prefix = $prefix;
      }

      /**
       * @public
       *
       * Returns the namespace of the taglib.
       *
       * @return string The namespace of the taglib.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function getNamespace(){
         return $this->__Namespace;
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
      public function getPrefix(){
         return $this->__Prefix;
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
      public function getClass(){
         return $this->__Class;
      }

    // end class
   }

   /**
    * @package core::pagecontroller
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
    * Version 0.3, 08.06.2007 (URL rewriting was outsorced and "__rewriteRequestURI()" was removed)<br />
    */
   class Page extends APFObject {

      /**
       * @protected
       * @var Document Container for the initial <em>Document</em> of the page.
       */
      protected $__Document;

      /**
       * @public
       *
       * Constructor of the page class. The class is the root node of the APF DOM tree.
       *
       * @param string $name the optional name of the page
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 03.01.2007 (Introduced URL rewriting)<br />
       * Version 0.3, 08.06.2007 (URL rewriting is now outsourced to the filters)<br />
       * Version 0.4, 20.06.2008 (Replaced the usage of "APPS__URL_REWRITING" with a registry call)<br />
       * Version 0.5, 20.10.2008 (Removed second parameter due to registry introduction in 1.7-beta)<br />
       * Version 0.6, 11.12.2008 (Switched to the new input filter concept)<br />
       */
      public function Page(){

         // set internal attributes
         $this->__ObjectID = XmlParser::generateUniqID();

         // apply input filter if desired (e.g. front controller is not used)
         $reg = &Singleton::getInstance('Registry');
         $filterDef = $reg->retrieve('apf::core::filter','PageControllerInputFilter');

         if($filterDef !== null){
            
            $inputFilter = FilterFactory::getFilter($filterDef);

            if($inputFilter !== null){
               $inputFilter->filter(null);
             // end if
            }
            
          // end if
         }

       // end function
      }

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
      public function &getRootDocument(){
         return $this->__Document;
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
       * Version 0.5, 08.03.2009 (Bugfix: protected variable __ParentObject might not be used)<br />
       */
      public function loadDesign($namespace,$design){

         $this->__Document = new Document();

         // set the current context
         if(empty($this->__Context)){
            $this->__Document->setContext($namespace);
          // end if
         }
         else{
            $this->__Document->setContext($this->__Context);
          // end else
         }

         // set the current language
         $this->__Document->setLanguage($this->__Language);

         // load the design
         $this->__Document->loadDesign($namespace,$design);
         $this->__Document->setObjectId(XmlParser::generateUniqID());
         $this->__Document->setParentObject($this);

       // end function
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
      public function transform(){

         // transform the current document
         $content = $this->__Document->transform();

         // apply output filter if desired
         $reg = &Singleton::getInstance('Registry');
         $filterDef = $reg->retrieve('apf::core::filter','OutputFilter');

         if($filterDef !== null){

            $outputFilter = FilterFactory::getFilter($filterDef);

            if($outputFilter !== null){
               $content = $outputFilter->filter($content);
             // end if
            }

          // end if
         }

         return $content;

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class Document
    *
    * Represents a node within the APF DOM tree. Each document can compose several other documents
    * by use of the $__Children property (composite tree).
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   class Document extends APFObject {

      /**
       * @protected
       * @var string The content of the tag. Example:
       * <pre>&lt;foo:bar&gt;This is the content of the tag.&lt;/foo:bar&gt;</pre>
       */
      protected $__Content;

      /**
       * @protected
       * @var string The name of the document controller to use at transformation time.
       */
      protected $__DocumentController = null;

      /**
       * @protected
       * @var TagLib[] List of known taglibs.
       */
      protected $__TagLibs;

      /**
       * @protected
       * @var int The maximum number of parser loops to protect against infinit loops.
       */
      protected $__MaxLoops = 100;

      /**
       * @public
       *
       * Initializes the built-in taglibs, used to create the APF DOM tree.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
       */
      public function Document(){

         // set the object id
         $this->__ObjectID = XmlParser::generateUniqID();

         // add the known taglibs (core taglibs!)
         $this->__TagLibs[] = new TagLib('core::pagecontroller','core','addtaglib');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','core','importdesign');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','html','template');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','html','placeholder');

       // end function
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
      public function getContent(){
         return $this->__Content;
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
      public function setContent($content){
         $this->__Content = $content;
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
      public function getDocumentController(){
         return $this->__DocumentController;
      }

      /**
       * @public
       *
       * This method is used to add more known taglibs to a document.
       *
       * @param string $namespace The namespace of the taglib
       * @param string $prefix The prefix of the taglib
       * @param string $class The class name of the taglib
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
       */
      public function addTagLib($namespace,$prefix,$class){

         // add the taglib to the current node
         $this->__TagLibs[] = new TagLib($namespace,$prefix,$class);

         // import taglib class
         $moduleName = $this->__getModuleName($prefix,$class);
         if(!class_exists($moduleName)){
            import($namespace,$moduleName);
          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Returns the full name of the taglib class file. The name consists of the prefix followed by
       * the string "_taglib_" ans the suffix (=class).
       *
       * @param string $prefix The prefix of the taglib
       * @param string $class The class name of the taglib
       * @return string The full file name of the taglib class
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      protected function __getModuleName($prefix,$class){
         return $prefix.'_taglib_'.$class;
       // end function
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
      public function loadDesign($namespace,$design){

         // read the content of the template
         $this->__loadContentFromFile($namespace,$design);

         // analyze document controller definitions
         $this->__extractDocumentController();

         // parse known taglibs
         $this->__extractTagLibTags();

       // end function
      }

      /**
       * @protected
       *
       * Loads a template from a given namespace. The convention says, that the name of the template
       * is equal to the file body plus the ".html" extentions. The namespace is a APF namespace.
       *
       * @param string $namespace The namespace of the template.
       * @param string $design The name of the template (a.k.a. design).
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 01.01.2007<br />
       * Version 0.3, 03.11.2008 (Added code of the responsible template to the error message to ease debugging)<br />
       */
      protected function __loadContentFromFile($namespace,$design){

         // sanitize the design name to avoid xss or code injection
         $design = preg_replace('/[^A-Za-z0-9\-_]/','',$design);

         $file = APPS__PATH.'/'.str_replace('::','/',$namespace).'/'.$design.'.html';

         if(!file_exists($file)){

            // get template code from parent object, if the parent exists
            $code = (string)'';
            if($this->__ParentObject !== null){
               $code = ' Please check your template code ('.htmlentities($this->__ParentObject->getContent()).').';
             // end if
            }

            throw new IncludeException('[Document::__loadContentFromFile()] Design "'.$design.'" not existent in namespace "'.$namespace.'"!'.$code,E_USER_ERROR);
            exit();

          // end if
         }
         else{
            $this->__Content = file_get_contents($file);
          // end else
         }

       // end function
      }

      /**
       * @protected
       *
       * Parses the content of the current APF DOM node. Extracts all known taglibs listed in
       * the <em>$this->__TagLibs</em> property. Each taglib is converted into a child document
       * of the current tree element. The tag definition place is reminded by a marker tag using
       * the internal id of the DOM node.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 21.01.2007 (Bugfix: a mixture of self- and exclusivly closing tags lead to wrong parsing)<br />
       * Version 0.3, 31.01.2007 (Added context injection)<br />
       * Version 0.4, 09.04.2007 (Removed double attributes setting, added language injection)<br />
       * Version 0.5, 02.04.2008 (Bugfix: the token is now displayed in the HTML error page)<br />
       * Version 0.6, 06.06.2009 (Improvement: content is not copied during parsing any more)<br />
       * Version 0.7, 30.12.2009 (Introduced benchmark marks for the onParseTime() event.)<br />
       */
      protected function __extractTagLibTags(){

         $tagLibLoops = 0;
         $i = 0;

         $t = &Singleton::getInstance('BenchmarkTimer');

         // Parse the known taglibs. Here, we have to use a while loop, because one parser loop
         // can result in an increasing amount of known taglibs (core:addtaglib!).
         while($i < count($this->__TagLibs)){

            if($tagLibLoops > $this->__MaxLoops){
               throw new ParserException('[Document::__extractTagLibTags()] Maximum numbers of '
                       .'parsing loops reached!',E_USER_ERROR);
               exit();
             // end if
            }

            $prefix = $this->__TagLibs[$i]->getPrefix();
            $class = $this->__TagLibs[$i]->getClass();
            $module = $this->__getModuleName($prefix, $class);
            $token = $prefix.':'.$class;
            $tagLoops = 0;

            while(substr_count($this->__Content,'<'.$token) > 0){

               if($tagLoops > $this->__MaxLoops){
                  throw new ParserException('['.get_class($this).'::__extractTagLibTags()] Maximum numbers of parsing loops reached!',E_USER_ERROR);
                  exit();
                // end if
               }

               // Find start and end position of the tag. "Normally" a
               // explicitly closing tag is expected.
               $tagStartPos = strpos($this->__Content,'<'.$token);
               $tagEndPos = strpos($this->__Content,'</'.$token.'>',$tagStartPos);
               $closingTagLength = strlen('</'.$token.'>');

               // in case a explictly-closing tag could not be found, seach for self-closing tag
               if($tagEndPos === false){

                  $tagEndPos = strpos($this->__Content,'/>',$tagStartPos);
                  $closingTagLength = 2;

                  if($tagEndPos === false){
                     throw new ParserException('['.get_class($this).'::__extractTagLibTags()] No closing tag '
                             .'found for tag "&lt;'.$token.' /&gt;"!',E_USER_ERROR);
                     exit(1);
                   // end if
                  }

                // end if
               }

               // extract the complete tag string from the current content
               $tagStringLength = ($tagEndPos - $tagStartPos) + $closingTagLength;
               $tagString = substr($this->__Content,$tagStartPos,$tagStringLength);

               // NEW (bugfix for errors while mixing self- and exclusivly closing tags):
               // First, check if a opening tag was found within the previously taken tag string.
               // If yes, the tag string must be redefined.
               if(substr_count($tagString,'<'.$token) > 1){

                  // find position of the self-colising tag
                  $tagEndPos = strpos($this->__Content,'/>',$tagStartPos);
                  $closingTagLength = 2;

                  // extract the complete tag string from the current content
                  $tagStringLength = ($tagEndPos - $tagStartPos) + $closingTagLength;
                  $tagString = substr($this->__Content,$tagStartPos,$tagStringLength);

                // end if
               }

               // get the tag attributes of the current tag
               $attributes = XmlParser::getTagAttributes($tagString);
               $object = new $module();

               // inject context of the parent object
               $object->setContext($this->__Context);

               // inject language of the parent object
               $object->setLanguage($this->__Language);

               // add the tag's atributes
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
               $this->__Content = substr_replace($this->__Content,'<'.$objectId.' />',$tagStartPos,$tagStringLength);

               // advertise the parent object
               $object->setParentObject($this);

               // add the content to the current APF DOM node
               $object->setContent($attributes['content']);

               // call onParseTime() to enable the taglib to initialize itself
               $benchId = '('.get_class($this).') '.$this->__ObjectID.'::__Children[('
                       .get_class($object).') '.$objectId.']::onParseTime()';
               $t->start($benchId);
               $object->onParseTime();
               $t->stop($benchId);

               // add current object to the APF DOM tree (no reference, because this leads to NPEs!)
               $this->__Children[$objectId] = $object;

               $tagLoops++;

               // delete current object to avoid interference.
               unset($object);

             // end while
            }

            $i++;

          // end while
         }

         // call onAfterAppend() on each child to enable the taglib to interact with
         // other APF DOM nodes to do extended initialization.
         if(count($this->__Children) > 0){

            $benchId = '('.get_class($this).') '.$this->__ObjectID.'::__Children[]::onAfterAppend()';
            $t->start($benchId);

            foreach($this->__Children as $objectId => $DUMMY){
               $this->__Children[$objectId]->onAfterAppend();
             // end for
            }

            $t->stop($benchId);

          // end if
         }

       // end function
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
       */
      protected function __extractDocumentController(){

         // define start and end tag
         $controllerStartTag = '<@controller';
         $controllerEndTag = '@>';

         if(substr_count($this->__Content,$controllerStartTag) > 0){

            $tagStartPos = strpos($this->__Content,$controllerStartTag);
            $tagEndPos = strpos($this->__Content,$controllerEndTag,$tagStartPos);
            $controllerTag = substr($this->__Content,$tagStartPos + strlen($controllerStartTag),
               ($tagEndPos - $tagStartPos) - 1 - strlen($controllerStartTag));
            $controllerAttributes = XmlParser::getAttributesFromString($controllerTag);

            // check for class definition
            if(!isset($controllerAttributes['class'])){
               throw new ParserException('[Document::__extractDocumentController()] Document controller '
                       .'specification does not contain a valid controller class definition. '
                       .'Please double check the template code and consult the documentation. '
                       .'Template code: '.htmlentities($this->__Content));
               exit(1);
            }

            // lazily import document controller class
            if(!class_exists($controllerAttributes['class'])){
               import($controllerAttributes['namespace'],$controllerAttributes['file']);
             // end if
            }

            // remark controller class
            $this->__DocumentController = $controllerAttributes['class'];

            // remove definition from content to be not displayed
            $this->__Content = substr_replace($this->__Content,'',
                    $tagStartPos,($tagEndPos - $tagStartPos) + strlen($controllerEndTag));

          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Implements the method, that is called at transformation time (see DOM node life cycle). If
       * you want to add custom logic in your taglib, overwrite this method. The page controller
       * expects the method to return the content of the transformed node.
       *
       * @return string The transformed content of the currend DOM node.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 21.01.2007 (Now, the attributes of the current node are injected the document controller)<br />
       * Version 0.3, 31.01.2007 (Added context injection)<br />
       * Version 0.4, 24.02.2007 (Switched timer inclusion to common benchmarker usage)<br />
       * Version 0.5, 09.04.2007 (Added language injection)<br />
       */
      public function transform(){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('('.get_class($this).') '.$this->__ObjectID.'::transform()');

         // create copy, to preserve it!
         $content = $this->__Content;

         // execute the document controller if applicable
         if(!empty($this->__DocumentController)){

            $id = '('.$this->__DocumentController.') '.(XmlParser::generateUniqID()).'::transformContent()';
            $t->start($id);

            if(!class_exists($this->__DocumentController)){
               throw new InvalidArgumentException('['.get_class($this).'::transform()] DocumentController "'.$this->__DocumentController.'" cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
               exit();
             // end if
            }

            $docCon = new $this->__DocumentController;

            // inject context
            $docCon->setContext($this->__Context);

            // inject current language
            $docCon->setLanguage($this->__Language);

            // inject document reference to be able to access the current DOM document
            $docCon->setDocument($this);

            // inject the content to be able to access it
            $docCon->setContent($content);

            // inject the current DOM node's attributes to easily access them
            if(is_array($this->__Attributes) && count($this->__Attributes) > 0){
               $docCon->setAttributes($this->__Attributes);
             // end if
            }

            // execute the document controller by using a standard method
            $docCon->transformContent();

            // retrieve the content
            $content = $docCon->getContent();

            $t->stop($id);

          // end if
         }

         // transform child nodes and replace XML marker to place the output at the right position
         if(count($this->__Children) > 0){
            foreach($this->__Children as $objectId => $DUMMY){
               $content = str_replace('<'.$objectId.' />',$this->__Children[$objectId]->transform(),$content);
             // end foreach
            }
          // end if
         }

         $t->stop('('.get_class($this).') '.$this->__ObjectID.'::transform()');

         return $content;

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class core_taglib_importdesign
    *
    * This class implements the functionality of the core::importdesign tag. It generates a sub node
    * from the template specified by the tag's attributes within the current APF DOM tree. Each
    * importdesign tag can compose further tags.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   class core_taglib_importdesign extends Document {

      /**
       * @public
       *
       * Constructor of the class. Sets the known taglibs.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function core_taglib_importdesign(){
         parent::Document();
       // end function
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
      public function onParseTime(){

         // get attributes
         $namespace = trim($this->__Attributes['namespace']);
         $template = trim($this->__Attributes['template']);

         // read context
         if(isset($this->__Attributes['context'])){
            $this->__Context = trim($this->__Attributes['context']);
          // end if
         }

         // manager inc param
         $incParam = null;
         if(isset($this->__Attributes['incparam'])){
            $incParam = $this->__Attributes['incparam'];
          // end if
         }
         else{
            $incParam = 'pagepart';
          // end else
         }

         // check, if the inc param is present in the current request
         if(substr_count($template,'[') > 0){

            if(isset($_REQUEST[$incParam]) && !empty($_REQUEST[$incParam])){
               $template = $_REQUEST[$incParam];
             // end if
            }
            else{

               // read template attribute from inc param
               $pagepartStartPos = strpos($template,'=');
               $pagepartEndPos = strlen($template) - 1;
               $template = trim(substr($template,$pagepartStartPos + 1,($pagepartEndPos - $pagepartStartPos) - 1));

             // end else
            }

          // end if
         }

         // get content
         $this->__loadContentFromFile($namespace,$template);

         // parse document controller statements
         $this->__extractDocumentController();

         // extract further xml tags
         $this->__extractTagLibTags();

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class core_taglib_addtaglib
    *
    * Represents the functionality of the core:addtaglib tag. Adds a further taglib to the known
    * taglibs of the tag's parent object. This can be used to enhance the known tag list if a
    * desired APF DOM node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   class core_taglib_addtaglib extends Document {

      public function core_taglib_addtaglib(){
      }

      /**
       * @public
       *
       * Implements the onParseTime() method of the Document class. Adds the desired taglib to the
       * parent object.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 10.11.2008 (Changed implementation. We now use getAttribute() instead of direct internal attribute addressing)<br />
       */
      public function onParseTime(){
         $this->__ParentObject->addTagLib($this->getAttribute('namespace'),$this->getAttribute('prefix'),$this->getAttribute('class'));
       // end function
      }

      /**
       * @public
       *
       * Implements the Document's transform() method. Returns an empty string, because the addtaglib
       * tag should not generate output.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 21.04.2007<br />
       */
      public function transform(){
         return (string)'';
       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class html_taglib_placeholder
    *
    * Represents a place holder within a template file. Can be filled within a documen controller
    * using the setPlaceHolder() method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   class html_taglib_placeholder extends Document {

      public function html_taglib_placeholder(){
      }

      /**
       * @public
       *
       * Implements the transform() method. Returns the content of the tag, that is set by a
       * document controller using the base_controller's setPlaceHolder() method.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function transform(){
         return $this->__Content;
       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class html_taglib_template
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
   class html_taglib_template extends Document {

      /**
       * @protected
       * Indicates, if the template should be transformed on the place of definition. Default is false.
       */
      protected $__TransformOnPlace = false;

      /**
       * @public
       *
       * Constructor of the class. Inituializes the known taglibs.
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
       */
      public function html_taglib_template(){
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','placeholder');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','addtaglib');
       // end function
      }

      /**
       * @public
       *
       * Implements the onParseTime() method from the APFObject class. Uses the __extractTagLibTags()
       * function to parse the known taglibs.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2006<br />
       * Version 0.2, 31.12.2006<br />
       */
      public function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }

      /**
       * @public
       *
       * API method to set a place holder's content within a document controller.
       *
       * @param string $Name name of the place holder
       * @param string $Value value of the place holder
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2006<br />
       * Version 0.2, 10.11.2008 (Removed check, if taglib class exists)<br />
       */
      public function setPlaceHolder($name,$value){

         // declare the name of the place holder taglib to be flexible to future changes
         $tagLibClass = 'template_taglib_placeholder';

         // initialize place holder count for further checks
         $placeHolderCount = 0;

         // check, if tag has children
         if(count($this->__Children) > 0){

            // check, if template place holder exists within the children list
            foreach($this->__Children as $objectID => $DUMMY){

               // check, if current child is a plece holder
               if(get_class($this->__Children[$objectID]) == $tagLibClass){

                  // check, if current child is the desired place holder
                  if($this->__Children[$objectID]->getAttribute('name') == $name){

                     // set content of the placeholder
                     $this->__Children[$objectID]->setContent($value);
                     $placeHolderCount++;

                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // trow error, if no place holder with the desired name was found
            throw new InvalidArgumentException('[html_taglib_template::setPlaceHolder()] No placeholder object with name "'.$name.'" composed in current template for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library template:placeHolder is not loaded in template "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

         // throw error, if no children are composed under the current tag
         if($placeHolderCount < 1){
            throw new InvalidArgumentException('[html_taglib_template::setPlaceHolder()] There are no placeholders found for name "'.$name.'" in template "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Returns the content of the template. Can be used to generate the template output
       * within a document controller. Usage:
       * <pre>
       * $template = &$this->__getTemplate('MyTemplate');
       * $template->setPlaceHolder('URL','http://adventure-php-framework.org');
       * echo $template->transformTemplate();
       * </pre>
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2006<br />
       * Version 0.2, 31.12.2006 (Removed parameter $this->__isVisible, because the parent object automatically removes the XML positioning tag on ransformation now)<br />
       * Version 0.3, 02.02.2007 (Renamed method to transformTemplate() umbenannt. Removed visible marking finally from the class)<br />
       * Version 0.4, 05.01.2007 (Added the template:addtaglib tag)<br />
       */
      public function transformTemplate(){

         // create copy of the tag's content
         $content = $this->__Content;

         // transform children
         if(count($this->__Children) > 0){

            foreach($this->__Children as $objectId => $DUMMY){
               $content = str_replace('<'.$objectId.' />',$this->__Children[$objectId]->transform(),$content);
             // end foreach
            }

          // end if
         }

         return $content;

       // end function
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
      public function transformOnPlace(){
         $this->__TransformOnPlace = true;
       // end function
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
      public function transform(){

         // checks, if transformOnPlace is activated
         if($this->__TransformOnPlace === true){
            return $this->transformTemplate();
          // end if
         }

         // return empty string
         return (string)'';

       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class template_taglib_placeholder
    *
    * Implements the place holder tag with in a html:template tag. The tag does not hav further children.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2006<br />
    */
   class template_taglib_placeholder extends Document {

      public function template_taglib_placeholder(){
      }

      /**
       * @public
       *
       * Implements the transform() method. Returns the content of the tag, that is set by a
       * document controller using the html_taglib_template's setPlaceHolder() method.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2006<br />
       */
      public function transform(){
         return $this->__Content;
       // end function
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class template_taglib_addtaglib
    *
    * Represents the core:addtaglib functionality for the html:template tag. Includes further
    * tag libs into the scope. Please see class core_taglib_addtaglib for more details.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.04.2007<br />
    * Version 0.2, 10.11.2008 (Removed the registerTagLibModule() logic of the templates. Now the functionality is the same as core_taglib_addtaglib)<br />
    */
   class template_taglib_addtaglib extends core_taglib_addtaglib {

      public function template_taglib_addtaglib(){
      }

    // end class
   }

   /**
    * @package core::pagecontroller
    * @class base_controller
    * @abstract
    *
    * Defines the base class for all document controller classes. To add custom logic, implement
    * the {@link transformContent} method, that is declared abstract, too.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 04.11.2007 (Removed the isButtonPushed() method)<br />
    */
   abstract class base_controller extends Document {

      /**
       * @protected
       * @var Document References the document, the document controller is responsible for transformation.
       */
      protected $__Document;

      public function base_controller(){
      }

      /**
       * @public
       * @abstract
       *
       * Abstract method, that is called on transformation time of the current document. To add
       * custom behavior, implement this method!
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       */
      public function transformContent(){
      }

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
      public function setDocument(&$document){
         $this->__Document = &$document;
      }

      /**
       * @protected
       *
       * Sets the given value as the content of the specified place holder.
       *
       * @param string $name The name of the plae holder to fill.
       * @param string $value The value to insert into the place holder.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
       */
      protected function setPlaceHolder($name,$value){

         $tagLibClass = 'html_taglib_placeholder';

         if(!class_exists($tagLibClass)){
            throw new InvalidArgumentException('['.get_class($this).'::setPlaceHolder()] TagLib module '
               .$tagLibClass.' is not loaded!',E_USER_ERROR);
          // end if
         }

         $placeHolderCount = 0;

         $children = &$this->__Document->getChildren();
         if(count($children) > 0){

            foreach($children as $objectID => $DUMMY){

               if(get_class($children[$objectID]) == $tagLibClass){

                  if($children[$objectID]->getAttribute('name') == $name){
                     $children[$objectID]->setContent($value);
                     $placeHolderCount++;
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{
            throw new InvalidArgumentException('['.get_class($this).'::setPlaceHolder()] No placeholder object with name "'
               .$name.'" composed in current document for document controller "'.get_class($this)
               .'"! Perhaps tag library html:placeholder is not loaded in current template!',E_USER_ERROR);
            exit();
          // end else
         }

         // warn, if no place holder is found
         if($placeHolderCount < 1){
            throw new InvalidArgumentException('['.get_class($this).'::setPlaceHolder()] There are no placeholders found for name "'
               .$name.'" in document controller "'.get_class($this).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Returns the instance of the form specified by the given name. This method can be used to
       * access a form object within a document controller.
       *
       * @param string $formName The name of the form to return.
       * @return html_taglib_form The instance of the desired form.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.01.2007<br />
       * Version 0.2, 14.06.2008 (Improved error handling.)<br />
       */
      protected function &__getForm($formName){

         $tagLibClass = 'html_taglib_form';
         if(!class_exists($tagLibClass)){
            throw new InvalidArgumentException('['.get_class($this).'::__getForm()] TagLib "'.$tagLibClass
               .'" is not loaded! Please add the form taglib using the &lt;core:addtaglib /&gt; tag',
               E_USER_ERROR);
          // end if
         }

         $children = &$this->__Document->getChildren();
         if(count($children) > 0){

            foreach($children as $objectID => $DUMMY){

               if(get_class($children[$objectID]) == $tagLibClass){

                  if($children[$objectID]->getAttribute('name') == $formName){
                     return $children[$objectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{
            throw new InvalidArgumentException('['.get_class($this).'::__getForm()] No form object with name "'
               .$formName.'" composed in current document for document controller "'.get_class($this)
               .'"! Perhaps tag library html:form is not loaded in current document!',E_USER_ERROR);
            exit();
          // end else
         }

         throw new InvalidArgumentException('['.get_class($this).'::__getForm()] Form with name "'
            .$formName.'" cannot be found in document controller "'.get_class($this).'"!',
            E_USER_ERROR);
         exit();

       // end function
      }

      /**
       * @protected
       *
       * Returns the instance of the template specified by the given name. This method can be used
       * to access a html template object within a document controller.
       *
       * @param string $name The name of the template to return.
       * @return html_taglib_template The desired template instance.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 28.12.2006<br />
       * Version 0.2, 03.01.2007 (Bugfix: now not only the first template is returned)<br />
       * Version 0.3, 12.01.2006 (Renamed from "__getContentTemplate" to "__getTemplate" due to the introduction of "__getForm")<br />
       * Version 0.4, 23.04.2009 (Corrected PHP4 style object access)<br />
       */
      protected function &__getTemplate($name){

         $tagLibClass = 'html_taglib_template';
         if(!class_exists($tagLibClass)){
            throw new InvalidArgumentException('['.get_class($this).'::__getTemplate()] TagLib module "'
               .$tagLibClass.'" is not loaded!',E_USER_ERROR);
          // end if
         }

         $children = &$this->__Document->getChildren();
         if(count($children) > 0){

            foreach($children as $objectID => $DUMMY){

               if(get_class($children[$objectID]) == $tagLibClass){

                  if($children[$objectID]->getAttribute('name') == $name){
                     return $children[$objectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{
            throw new InvalidArgumentException('['.get_class($this).'::__getTemplate()] No template object with name "'
               .$name.'" composed in current document for document controller "'.get_class($this)
               .'"! Perhaps tag library html:template is not loaded in current template!',E_USER_ERROR);
            exit();
          // end else
         }

         throw new InvalidArgumentException('['.get_class($this).'::__getTemplate()] Template with name "'
            .$name.'" cannot be found!',E_USER_ERROR);
         exit();

       // end function
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
       */
      protected function __placeholderExists($name){

         $children = &$this->__Document->getChildren();

         foreach($children as $objectID => $DUMMY){
            if(get_class($children[$objectID]) == 'html_taglib_placeholder'){
               if($children[$objectID]->getAttribute('name') == $name){
                  return true;
                // end if
               }
             // end if
            }
          // end foreach
         }

         return false;

       // end function
      }

      /**
       * @protected
       *
       * Checks, if a place holder exists within the given template.
       *
       * @param html_taglib_template $template The instance of the template to check.
       * @param string $name The name of the place holder.
       * @return bool True if yes, false otherwise.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 11.03.2007<br />
       * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
       */
      protected function __templatePlaceholderExists(&$template,$name){

         $children = &$template->getChildren();

         foreach($children as $objectID => $DUMMY){
            if(get_class($children[$objectID]) == 'template_taglib_placeholder'){
               if($children[$objectID]->getAttribute('name') == $name){
                  return true;
                // end if
               }
             // end if
            }
          // end foreach
         }

         return false;

       // end function
      }

    // end class
   }
?>