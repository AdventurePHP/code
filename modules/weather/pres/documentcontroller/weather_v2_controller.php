<?php
namespace APF\modules\weather\pres\documentcontroller;

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
 * @package modules::weather::pres::documentcontroller
 * @class weather_v2_controller
 *
 * Implements an xml consumer to display current weather information.
 * Please provide the attribute "xml_source" in the core:importdesign tag that
 * includes the weather module.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 17.08.2010<br />
 */
class weather_v2_controller extends BaseDocumentController {

   /**
    * @public
    *
    * Implements the abstract method transformContent() of the BaseDocumentController class.<br />
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 17.08.2010<br />
    */
   public function transformContent() {

      // Fetch XML string
      $XML = $this->getXML();

      // Check weather XML string is present
      if ($XML != null) {
         $this->transformXml($XML);
      } else {

         // Display error message
         $templateNoEntries = &$this->getTemplate('NoEntries_' . $this->language);
         $templateNoEntries->setPlaceHolder('Source', $this->getXMLSource());
         $templateNoEntries->transformOnPlace();
      }
   }

   /**
    * @protected
    *
    * Transforms the content<br />
    *
    * @param string $XML XML string
    * @return string The information section HTML code.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 17.08.2010<br />
    */
   protected function transformXml($XML) {

      // Get references on the templates used
      $Template__Information = &$this->getTemplate('Information');

      // Create DOM document and get an reference on the channel node
      $DomDoc = simplexml_load_string($XML);
      $Weather = $DomDoc->xpath('/weather');

      // Title
      $Template__Information->setPlaceHolder('City', $Weather[0]->city);

      // Link
      $Template__Information->setPlaceHolder('Condition', $Weather[0]->condition);

      // Description
      $Template__Information->setPlaceHolder('Temp', $Weather[0]->temp);

      // Transform and display channel
      $Template__Information->transformOnPlace();
   }

   /**
    * @protected
    *
    * Helper method to get the XML string.<br />
    *
    * @return string Content of the XML stream of the source or null.
    * @throws \InvalidArgumentException In case of XSLT errors.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 17.08.2010<br />
    */
   protected function getXML() {

      // fetch RSS source
      $xmlSource = $this->getXMLSource();

      if ($xmlSource != null) {
         $plainXml = file_get_contents($xmlSource);

         if ($plainXml !== false) {
            $xsltNamespace = $this->getDocument()->getAttribute('xslt_namespace');
            $xsltFile = $this->getDocument()->getAttribute('xslt_file');
            if ($xsltNamespace != null && $xsltFile != null) {

               // create XML source
               $xml = new DOMDocument();
               $xml->loadXML($plainXml);

               $xsl = new DOMDocument();
               $xsl->load(APPS__PATH . '/' . str_replace('::', '/', $xsltNamespace) . '/' . $xsltFile . '.xsl');

               // configure transformer
               $proc = new XSLTProcessor();
               $proc->importStyleSheet($xsl); // import XSL document

               $result = $proc->transformToXML($xml);
               if ($result) {
                  return $result;
               } else {
                  throw new \InvalidArgumentException('[weather_v2_controller::__getXML()] XSLT Error!');
               }
            } else {
               return $plainXml;
            }
         } else {
            return null;
         }
      } else {
         throw new \InvalidArgumentException('[weather_v2_controller::__getXML()] Attribute "xml_source" not present in "core:importdesign" tag for weather module!');
      }
   }

   /**
    * @protected
    *
    * Helper method to read the attribute "xml_source" from the current object<br />
    * (core:importdesign), that is used to include the module.<br />
    *
    * @return string Source URL of the XML stream
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 17.08.2010<br />
    */
   protected function getXMLSource() {
      return $this->getDocument()->getAttribute('xml_source');
   }

}
