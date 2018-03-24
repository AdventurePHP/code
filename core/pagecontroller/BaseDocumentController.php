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
namespace APF\core\pagecontroller;

use APF\core\http\mixins\GetRequestResponse;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\html\Iterator;
use APF\tools\html\taglib\HtmlIteratorTag;
use InvalidArgumentException;

/**
 * Defines the base class for all document controller classes. To add custom logic, implement
 * the {@link transformContent} method.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 04.11.2007 (Removed the isButtonPushed() method)<br />
 * Version 0.3, 09.02.2013 (Introduced the DocumentController interface)<br />
 * Version 0.4, 01.04.2014 (Removed content handling passing the current document's content to the document controller)<br />
 */
abstract class BaseDocumentController extends APFObject implements DocumentController {

   use GetRequestResponse;

   /**
    * References the document, the document controller is responsible for transformation.
    *
    * @var DomNode $document
    */
   protected $document;

   /**
    * Sets the given value as the content of the specified place holder.
    *
    * @param string $name The name of the place holder to fill.
    * @param string $value The value to insert into the place holder.
    * @param bool $append True in case the applied values should be appended, false otherwise.
    *
    * @author Christian Schäfer, Jan Wiese
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 09.02.2013 (Switched to Document::setPlaceHolder() implementation)<br />
    * Version 0.4, 06.08.2013 (Added support for appending content to place holders)<br />
    */
   protected function setPlaceHolder(string $name, string $value, bool $append = false) {
      $this->getDocument()->setPlaceHolder($name, $value, $append);
   }

   public function getDocument() {
      return $this->document;
   }

   public function setDocument(DomNode $document) {
      $this->document = $document;
   }

   /**
    * This method is for convenient setting of multiple place holders. The applied
    * array must contain a structure like this:
    * <code>
    * [
    *    'key-a' => 'value-a',
    *    'key-b' => 'value-b',
    *    'key-c' => 'value-c',
    *    'key-d' => 'value-d',
    *    'key-e' => 'value-e',
    * ]
    * </code>
    * Thereby, the <em>key-*</em> offsets define the name of the place holders, their
    * values are used as the place holder's values.
    *
    * @param string[] $placeHolderValues Key-value-couples to fill place holders.
    * @param bool $append True in case the applied values should be appended, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    * Version 0.2, 06.08.2013 (Added support for appending content to place holders)<br />
    */
   protected function setPlaceHolders(array $placeHolderValues, bool $append = false) {
      $this->getDocument()->setPlaceHolders($placeHolderValues, $append);
   }

   /**
    * Returns the instance of the form specified by the given name. This method can be used to
    * access a form object within a document controller.
    *
    * @param string $formName The name of the form to return.
    *
    * @return HtmlFormTag The instance of the desired form.
    * @throws InvalidArgumentException In case the form cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 14.06.2008 (Improved error handling.)<br />
    * Version 0.3, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function getForm(string $formName) {
      try {
         return $this->getDocument()->getChildNode('name', $formName, HtmlForm::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getForm()] No form object with name "'
               . $formName . '" composed in current document for document controller "' . get_class($this)
               . '"! Perhaps tag library html:form is not loaded in current document!', E_USER_ERROR, $e);
      }
   }

   /**
    * Returns the instance of the template specified by the given name. This method can be used
    * to access a html template object within a document controller.
    *
    * @param string $name The name of the template to return.
    *
    * @return TemplateTag The desired template instance.
    * @throws InvalidArgumentException In case the template cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.01.2007 (Bug fix: now not only the first template is returned)<br />
    * Version 0.3, 12.01.2006 (Renamed from "__getContentTemplate" to "__getTemplate" due to the introduction of "__getForm")<br />
    * Version 0.4, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.5, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function getTemplate(string $name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, Template::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getTemplate()] No template with name "'
               . $name . '" composed in current document for document controller "' . get_class($this)
               . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * Let's you retrieve an instance of the LanguageLabelTag label instance to
    * fill a place holder.
    *
    * @param string $name The content of the tag's "name" attribute to select the node.
    *
    * @return LanguageLabelTag The instance of the desired label node.
    * @throws InvalidArgumentException In case no label node can be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 2012<br />
    * Version 0.2, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function getLabel(string $name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, LanguageLabel::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getLabel()] No label with name "'
               . $name . '" composed in current document for document controller "' . get_class($this)
               . '"! Perhaps tag library html:getstring is not loaded in current template!', E_USER_ERROR, $e);
      }
   }

   /**
    * Checks, if a place holder exists within the current document.
    *
    * @param string $name The name of the place holder.
    *
    * @return bool True if yes, false otherwise.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 11.03.2007<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 02.07.2011 (Renaming to fit the APF naming convention)<br />
    * Version 0.4, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function placeHolderExists(string $name) {
      try {
         $this->getDocument()->getChildNode('name', $name, PlaceHolder::class);

         return true;
      } catch (InvalidArgumentException $e) {
         return false;
      }
   }

   /**
    * Checks, if a place holder exists within the given template.
    *
    * @param TemplateTag $template The instance of the template to check.
    * @param string $name The name of the place holder.
    *
    * @return bool True if yes, false otherwise.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 11.03.2007<br />
    * Version 0.2, 23.04.2009 (Corrected PHP4 style object access)<br />
    * Version 0.3, 02.07.2011 (Renaming to fit the APF naming convention)<br />
    * Version 0.4, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function templatePlaceHolderExists(TemplateTag $template, string $name) {
      try {
         $template->getChildNode('name', $name, PlaceHolder::class);
         return true;
      } catch (InvalidArgumentException $e) {
         return false;
      }
   }

   /**
    * Returns a reference on the desired iterator.
    *
    * @param string $name Name of the iterator.
    *
    * @return HtmlIteratorTag The desired iterator.
    * @throws IncludeException In case the iterator taglib is not loaded.
    * @throws InvalidArgumentException In case the desired iterator cannot be returned.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.06.2008<br />
    * Version 0.2, 13.08.2014 (ID#231: Allow custom form tag while still using this method to obtain an instance)<br />
    */
   protected function getIterator(string $name) {
      try {
         return $this->getDocument()->getChildNode('name', $name, Iterator::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getIterator()] No iterator with name "'
               . $name . '" composed in current document for document controller "' . get_class($this) . '"! '
               . 'Perhaps tag library html:iterator is not loaded in current template!', E_USER_ERROR, $e);
      }
   }

   /**
    * Allows you to set data attributes to the DOM node this document controller is responsible for.
    *
    * @param string $name The reference name of the data field to set/add.
    * @param mixed $data The data to inject to the current node.
    *
    * @return DomNode The document instance this controller is responsible to transform.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.01.2014<br />
    */
   protected function setData(string $name, $data) {
      return $this->getDocument()->setData($name, $data);
   }

   /**
    * Allows you to retrieve a data attribute from the DOM node this document controller is responsible for.
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
   protected function getData(string $name, $default = null) {
      return $this->getDocument()->getData($name, $default);
   }

   /**
    * Convenience method to access a DOM node via it's <em>dom-id</em> attribute.
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
   protected function getNodeById(string $id) {
      try {
         return $this->getDocument()->getNodeById($id);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[' . get_class($this) . '::getDomById()] No DOM node with id "'
               . $id . '" found in current document for document controller "' . get_class($this) . '"! '
               . 'Perhaps review your template setup!', E_USER_ERROR, $e);
      }
   }

}
