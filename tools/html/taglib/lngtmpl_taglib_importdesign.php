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
 * @package tools::html::taglib
 * @class langdep_taglib_importdesign
 *
 * Implements a special importdesign tag, that can handle language files like eZComponents. The
 * APF implementation includes the following principles:
 * <ul>
 *   <li>The global translation files (dictionary) reside in the config namespace.</li>
 *   <li>The namespace of the dictionary files can be configured using the "dict_namespace" attribute.</li>
 *   <li>
 *     To be able to make those templates reusable in multiple environments the dictionary is
 *     located in the context subpath.
 *   <li>
 *   <li>The translation files contain only one language.</li>
 *   <li>The local translation file must be located in te same directory as the template itself.</li>
 * </ul>
 * Requires PHP > 5.0.0 because of SimpleXML usage.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2008<br />
 */
class lngtmpl_taglib_importdesign extends Document {

   /**
    * @public
    *
    *  Reimplements the onParseTime() method. Loads the desired template and parses the language
    *  marker. By default the tag searches for a file with name "<template>.<language>.xml". If
    *  this file is not found, the "dictionary.xml" is loaded, that must be located in parallel to
    *  the template file.
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 08.11.2008<br />
    *  Version 0.2, 19.11.2008 (Added some enhancement)<br />
    */
   public function onParseTime() {

      // invoke timer
      $T = &Singleton::getInstance('BenchmarkTimer');
      $id = '(lngtmpl_taglib_importdesign) ' . $this->__ObjectID . '::onParseTime()';
      $T->start($id);

      // check attributes
      $lang = $this->getAttribute('lang');
      if ($lang === null) {
         $lang = $this->__Language;
      }

      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new InvalidArgumentException('[lngtmpl_taglib_importdesign::onParseTime()] No attribute "namespace" given!', E_USER_ERROR);
      }

      $template = $this->getAttribute('template');
      if ($template === null) {
         throw new InvalidArgumentException('[lngtmpl_taglib_importdesign::onParseTime()] No attribute "template" given!', E_USER_ERROR);
      }

      // load content
      $this->__loadContentFromFile($namespace, $template);

      // parse for translations
      $this->parseTranslations($namespace, $template);

      // parse document controller directives
      $this->__extractDocumentController();

      // parse known tags
      $this->__extractTagLibTags();

      $T->stop($id);

   }

   /**
    * @protected
    *
    *  Replaces the language keys surrounded by "{{{" and "}}}" by a given XML string containing
    *  the translations.
    *
    * @param string $content the content to be translated
    * @param string $xml the xml containing the translations
    * @return string $content the translated content
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 18.11.2008<br />
    */
   protected function parseTranslations($namespace, $template) {

      // use dictionary from the config namespace if applicable
      $dict_namespace = $this->getAttribute('dict_namespace');
      if ($dict_namespace !== null) {
         $this->parseLanguageFile('config::' . $dict_namespace . '::' . $this->__Context, 'dictionary');
      }

      // use local language file
      $this->parseLanguageFile($namespace, $template);

   }

   /**
    * @protected
    *
    *  Parses a dedicated language file defined by it's namespace and language file name.
    *
    * @param string $namespace the namespace of the language file
    * @param string $filename the name of the language file
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 19.11.2008<br />
    */
   protected function parseLanguageFile($namespace, $filename) {

      // create file name
      $fileName = APPS__PATH . '/' . str_replace('::', '/', $namespace) . '/' . $filename . '.' . $this->__Language . '.xml';

      if (file_exists($fileName)) {

         // create a DOM document from the xml string
         $domDoc = @simplexml_load_string(file_get_contents($fileName));

         // translate tokens or throw error
         if ($domDoc instanceof SimpleXMLElement) {
            $this->__Content = $this->parseLanguageTokens($this->__Content, $domDoc);
         } else {
            throw new InvalidArgumentException('[lngtmpl_taglib_importdesign::__parseLanguageFile()] The translation file ("' . $filename . '.xml") in namespace "' . $namespace . '" does not contain a valid XML document! The content is returned without translation.');
         }

      } else {
         throw new InvalidArgumentException('[lngtmpl_taglib_importdesign::__parseLanguageFile()] The desired translation file ("' . $filename . '.xml") does not exist in namespace "' . $namespace . '". Please check your tag definition or your configurarion! The content is returned without translation.');
      }

   }

   /**
    * @protected
    *
    *  Replaces the language keys surrounded by "{{{" and "}}}" by a given XML string containing
    *  the translations.
    *
    * @param string $content the content to be translated
    * @param string $domDoc the DOM document created from the translation file
    * @return string $content the translated content
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 18.11.2008<br />
    *  Version 0.2, 19.11.2008 (Simplifyed the XML structure and thus the implementation)<br />
    */
   protected function parseLanguageTokens($content, $domDoc) {

      $translations = $domDoc->xpath('/translations/translation');

      for ($i = 0; $i < count($translations); $i++) {
         $content = str_replace('{{{' . $this->getAttributeByName($translations[$i], 'key') . '}}}', $translations[$i][0], $content);
      }

      return $content;

   }

   /**
    * @protected
    *
    *  Returns the value of a desired attribute of the given SimpleXMLElement.
    *
    * @param SimpleXMLElement $element the XML node
    * @param string $name the name of the desired node attribute
    * @return string $attributeValue the value of the node attribute
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 18.11.2008<br />
    */
   protected function getAttributeByName($element, $name) {

      $attributeValue = null;
      foreach ($element->attributes() as $key => $value) {
         if ($key == $name) {
            $attributeValue = $value;
         }
      }
      return $attributeValue;

   }

}