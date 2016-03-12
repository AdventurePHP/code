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

use APF\core\service\APFDIService;
use InvalidArgumentException;

/**
 * Defines the structure of a node within the APF DOM tree. Each document can compose several other documents
 * by use of the $children property (composite tree).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.10.2015 (ID#265: introduced interface to clearly define the API of an APF DOM node)<br />
 */
interface DomNode extends APFDIService {

   /**
    * This method adds a given tag to the <em>global</em> list of known tags for the APF parser.
    *
    * @param string $class The fully-qualified name of the tag implementation.
    * @param string $prefix The tag prefix.
    * @param string $name The tag name.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.03.2007 (Removed the "&" in front of "new")<br />
    * Version 0.3, 14.02.2011 (Refactored method signature to be more type safe)<br />
    * Version 0.4, 11.07.2014 (Removed TagLib to gain performance and simplify API)<br />
    */
   public static function addTagLib($class, $prefix, $name);

   /**
    * Add a template expressions to the <em>global</em> list of known expressions.
    *
    * @param string $expression The fully qualified class name of the template expression (e.g. <em>APF\core\pagecontroller\PlaceHolderTemplateExpression</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2014 (ID#229: introduced template expressions)<br />
    */
   public static function addTemplateExpression($expression);

   /**
    * Injects the parent node of the current APF object.
    *
    * @param DomNode $parentObject The parent node.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setParentObject(DomNode &$parentObject);

   /**
    * Returns the parent node of the current APF object.
    *
    * @return DomNode The parent node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getParentObject();

   /**
    * Sets the object id of the current APF object.
    *
    * @param string $objectId The object id.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setObjectId($objectId);

   /**
    * Returns the object id of the current APF object.
    *
    * @return string The object id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getObjectId();

   /**
    * Returns the object's attribute.
    * <p/>
    * PLEASE NOTE: attributes defined with value <em>null</em> will not be recognized
    * as XML string attributes that are really <em>null</em> don't make sense. Defining
    * an attribute such as
    * <code>
    * empty=""
    * </code>
    * will return <em>false</em> for
    * <code>
    * $attributes = XmlParser::getAttributesFromString('filled="foo" empty=""');
    * var_dump($attributes['empty'] === null)
    * </code>
    * For this reason, XML attributes can only be null when not existing.
    *
    * @param string $name The name of the desired attribute.
    * @param string $default The default value for the attribute.
    *
    * @return string Returns the value or null in case of errors.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 02.02.2007 (Added default value handling)<br />
    */
   public function getAttribute($name, $default = null);

   /**
    * Allows you to check whether an attribute has been defined or not.
    * <p/>
    * Similar to <em>getAttribute()</em> this method only returns <em>true</em>
    * in case there is an attribute defined and it's value is unlike <em>null</em>.
    * <p/>
    * PLEASE NOTE: this is intentional as described in <em>getAttribute()</em>.
    *
    * @param string $name The name of the desired attribute.
    *
    * @return bool <em>True</em> in case
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2015<br />
    */
   public function hasAttribute($name);

   /**
    * Let's you retrieve a tag attribute expressing to other developers that it is a mandatory attribute.
    *
    * @param string $name The name of the desired attribute.
    *
    * @return string Returns the value.
    * @throws InvalidArgumentException In case the attribute is not present/defined.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.05.2014<br />
    */
   public function getRequiredAttribute($name);

   /**
    * Sets an object's attribute.
    *
    * @param string $name Name of the attribute.
    * @param string $value Value of the attribute.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function &setAttribute($name, $value);

   /**
    * Returns an object's attributes.
    *
    * @return string[] Returns the list of attributes of the current object.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function getAttributes();

   /**
    * Deletes an attribute.
    *
    * @param string $name The name of the attribute to delete.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function &deleteAttribute($name);

   /**
    * Sets an object's attributes.
    *
    * @param array $attributes The attributes list.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function &setAttributes(array $attributes = array());

   /**
    * Let's you add the applied value to the given attribute. The glue parameter
    * can be used to specify how pieces are joined together.
    * <p/>
    * Implicitly creates the attribute in case it doesn't exist.
    *
    * @param string $name The name of the attribute to add a value to.
    * @param string $value The value to add to the current attribute value.
    * @param string $glue The glue string to join the attribute content.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 09.01.2007<br />
    * Version 0.2, 09.02.2013 (Moved to APFObject to avoid multiple implementations)<br />
    * Version 0.3, 21.07.2014 (Added option to define the glue)<br />
    */
   public function &addAttribute($name, $value, $glue = '');

   /**
    * Returns the textual content of the current node.
    *
    * @return string The content of the current node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getContent();

   /**
    * Sets the textual content of the current node.
    *
    * @param string $content The content of the current node.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setContent($content);

   /**
    * Returns the list of the current node's children.
    *
    * @return DomNode[] The current node's children.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getChildren();

   /**
    * Let's you retrieve a child node of the current document by specifying a selector
    * (attribute name and attribute value) and the expected node type (name of the taglib
    * class).
    *
    * @param string $attributeName The name of the attribute to match against the given value.
    * @param string $value The value of the attribute to select the desired node.
    * @param string $tagLibClass The expected class name of the node.
    *
    * @return DomNode The desired child node.
    * @throws InvalidArgumentException In case the node has no children or no child node can be found with the given selectors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.12.2011<br />
    * Version 0.2, 09.02.2013 (Now public access since DocumentController is now derived from APFObject instead of Document)<br />
    */
   public function &getChildNode($attributeName, $value, $tagLibClass);

   /**
    * Same functionality as <em>getChildNode()</em> except returning null in case no matching node
    * has been found instead of throwing an exception.
    *
    * @param string $attributeName The name of the attribute to match against the given value.
    * @param string $value The value of the attribute to select the desired node.
    * @param string $tagLibClass The expected class name of the node.
    *
    * @return DomNode|null The desired child node or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.12.2011<br />
    * Version 0.2, 09.02.2013 (Now public access since DocumentController is now derived from APFObject instead of Document)<br />
    */
   public function &getChildNodeIfExists($attributeName, $value, $tagLibClass);

   /**
    * Let's you retrieve a list of child nodes of the current document by specifying a selector
    * (attribute name and attribute value) and the expected node type (name of the taglib
    * class).
    *
    * @param string $attributeName The name of the attribute to match against the given value.
    * @param string $value The value of the attribute to select the desired node.
    * @param string $tagLibClass The expected class name of the nodes.
    *
    * @return DomNode[] The desired list of child nodes.
    * @throws InvalidArgumentException In case the node has no children or no child node can be found with the given selectors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.07.2012<br />
    * Version 0.2, 09.02.2013 (Now public access since DocumentController is now derived from APFObject instead of Document)<br />
    */
   public function &getChildNodes($attributeName, $value, $tagLibClass);

   /**
    * API method to set a place holder's content within a document.
    *
    * @param string $name name of the place holder.
    * @param string $value value of the place holder.
    * @param bool $append True in case the applied value should be appended, false otherwise.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz, Jan Wiese
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 10.11.2008 (Removed check, if taglib class exists)<br />
    * Version 0.3, 07.02.2013 (Moved to Document to avoid multiple implementations)<br />
    * Version 0.4, 05.08.2013 (Added support to append content to place holders)<br />
    */
   public function &setPlaceHolder($name, $value, $append = false);

   /**
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
    * @param bool $append True in case the applied values should be appended, false otherwise.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    * Version 0.2, 09.02.2013 (Moved to Document to avoid multiple implementations)<br />
    * Version 0.3, 06.08.2013 (Added support for appending content to place holders)<br />
    */
   public function &setPlaceHolders(array $placeHolderValues, $append = false);

   /**
    * Returns the list of registered place holders for the current document.
    *
    * @return string[][]
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2016 (ID#287)<br />
    */
   public function getPlaceHolders();

   /**
    * Returns the value of a single place holder.
    *
    * @param string $name The name of the place holder to return it's value.
    * @param string $default The value to return in case the place holder is not registered (default: null).
    *
    * @return string The value of the desired place holder.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2016 (ID#287)<br />
    */
   public function getPlaceHolder($name, $default = null);

   /**
    * Clears the list of place holders for the current document. Can be used to reset place holders within templates.
    * <p/>
    * See DefaultTemplateTagClearApproach for details.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2016 (ID#287)<br />
    */
   public function &clearPlaceHolders();

   /**
    * Returns the name of the document controller in case the document should
    * be transformed using an MVC controller. In case no controller is defined
    * <em>null</em> is returned instead.
    *
    * @return string|null The name of the document controller.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getDocumentController();

   /**
    * This method adds a given tag to the <em>local</em> list of known tags for the APF parser.
    * <p/>
    * Using this method, you can override globally defined tags for this particular instance.
    *
    * @param string $class The fully-qualified name of the tag implementation.
    * @param string $prefix The tag prefix.
    * @param string $name The tag name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.02.2011 (ID#185, ID#1786: introduced local override mechanism)<br />
    * Version 0.2, 11.07.2014 (Removed TagLib to gain performance and simplify API)<br />
    */
   public function addInstanceTagLib($class, $prefix, $name);

   /**
    * Allows you to set data attributes to the current DOM node (similar to Java Script for HTML nodes).
    *
    * @param string $name The reference name of the data field to set/add.
    * @param mixed $data The data to inject to the current node.
    *
    * @return DomNode This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.01.2014<br />
    */
   public function &setData($name, $data);

   /**
    * Allows you to retrieve a data attribute from the current DOM node (similar to Java Script for HTML nodes).
    *
    * @param string $name The reference name of the data field to set/add.
    * @param mixed $default The desired default value (optional).
    *
    * @return mixed The desired data field content or the default value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.01.2014<br />
    */
   public function getData($name, $default = null);

   /**
    * Interface definition of the onParseTime() method. This function is called after the creation
    * of a new DOM node. It must be implemented by derived classes.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function onParseTime();

   /**
    * Interface definition of the onAfterAppend() method. This function is called after the DOM
    * node is appended to the DOM tree. It must be implemented by derived classes.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 27.06.2014 (Added expression tag analysis to "standard" document to allow expression place holders in initial document)<br />
    */
   public function onAfterAppend();

   /**
    * Implements the method, that is called at transformation time (see DOM node life cycle). If
    * you want to add custom logic in your taglib, overwrite this method. The page controller
    * expects the method to return the content of the transformed node.
    *
    * @return string The transformed content of the current DOM node.
    * @throws InvalidArgumentException In case the document controller class is missing.
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
    * Version 0.8, 01.04.2014 (Removed content handling passing the current document's content to the document controller)<br />
    */
   public function transform();

   /**
    * Let's you access a document within the <em>entire APF DOM tree</em> by a given <em>dom-id</em>.
    *
    * @param string $id The id of the DOM node to return.
    *
    * @return DomNode The desired DOM node within the tree.
    * @throws InvalidArgumentException In case no DOM node exists with the given id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.08.2014<br />
    */
   public function &getNodeById($id);

   /**
    * Same functionality as <em>getNodeById()</em> except returning null in case no matching node
    * has been found instead of throwing an exception.
    *
    * @param string $id The id of the DOM node to return.
    *
    * @return DomNode|null The desired DOM node within the tree or null in case it does not exist.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.08.2014<br />
    */
   public function &getNodeByIdIfExists($id);

}
