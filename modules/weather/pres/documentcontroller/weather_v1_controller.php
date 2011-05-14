<?php
/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

/**
 *  @package modules::weather::pres::documentcontroller
 *  @class weather_v1_controller
 *
 *  Implements an rss consumer to display current weather information.
 *  Please provide the attribute "rss_source" in the core:importdesign tag that
 *  includes the weather module.
 *
 *  @author Christian Achatz
 *  @version
 *  Version 0.1, 20.04.2008<br />
 */
class weather_v1_controller extends base_controller {

   /**
    *  @public
    *
    *  Implements the abstract method transformContent() of the base_controller class.<br />
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 20.04.2008<br />
    *  Version 0.2, 26.04.2008 (Transformation for PHP4 and PHP5 implemented separately)<br />
    */
   public function transformContent() {

      // Fetch XML string
      $XML = $this->getRSSFeed();

      // Check weather XML string is present
      if ($XML != null) {

         // Get references on the templates used
         $Template__Channel = &$this->getTemplate('Channel');
         $Template__Item = &$this->getTemplate('Item');

         // Create DOM document and get an reference on the channel node
         $DomDoc = simplexml_load_string($XML);
         $RSS = $DomDoc->xpath('/rss/channel');
         $Channel = $DomDoc->xpath('/rss/channel/item');

         // Title
         $Template__Channel->setPlaceHolder('Title', utf8_decode($RSS[0]->title));

         // Link
         $Template__Channel->setPlaceHolder('Link', utf8_decode($RSS[0]->link));

         // Description
         $Template__Channel->setPlaceHolder('Description', utf8_decode($RSS[0]->description));

         // Display items of current channel
         $count = count($Channel);
         $Buffer = (string) '';

         for ($i = 0; $i < $count; $i++) {

            // Title
            $Template__Item->setPlaceHolder('Title', utf8_decode($Channel[$i]->title));

            // Link
            $Template__Item->setPlaceHolder('Link', utf8_decode($Channel[$i]->link));

            // Description
            $Template__Item->setPlaceHolder('Description', utf8_decode($Channel[$i]->description));

            $Buffer .= $Template__Item->transformTemplate();
         }

         // Insert items in channel template
         $Template__Channel->setPlaceHolder('Items', $Buffer);

         // Transform and display channel
         $this->setPlaceHolder('Content', $Template__Channel->transformTemplate());
      } else {

         // Display error message
         $Template__NoEntries = &$this->getTemplate('NoEntries_' . $this->__Language);
         $Template__NoEntries->setPlaceHolder('Source', $this->getRSSSource());
         $this->setPlaceHolder('Content', $Template__NoEntries->transformTemplate());
      }
   }

   /**
    *  @protected
    *
    *  Helper method to get the content of the first child of a given DOM node.<br />
    *
    *  @param DOMNode $Node; Desired DOM node
    *  @return string Content of the first node
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 20.04.2008<br />
    */
   protected function getFirstChildContent($Node) {
      $FirstChild = $Node->first_child();
      return $FirstChild->node_value();
   }

   /**
    *  @protected
    *
    *  Helper method to get the child nodes of a given node type.<br />
    *
    *  @param DOMNode $Node; Desired DOM node
    *  @param string $Name; Name of the nodes to be returned
    *  @return Document[] List of child nodes
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 20.04.2008<br />
    */
   protected function &getChildNodesByNodeName(&$Node, $Name) {

      // Get all child nodes
      $AllChildNodes = $Node->child_nodes();
      $count = count($AllChildNodes);
      $ChildNodes = array();

      // Search for desired child nodes
      for ($i = 0; $i < $count; $i++) {

         if ($AllChildNodes[$i]->node_name() == $Name) {
            $ChildNodes[] = &$AllChildNodes[$i];
         }
      }

      return $ChildNodes;
   }

   /**
    *  @protected
    *
    *  Helper method to get the RSS string.<br />
    *
    *  @return string Content of the RSS stream of wetter.com or null
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 20.04.2008<br />
    */
   protected function getRSSFeed() {

      // Fetch RSS source
      $RSS_Source = $this->getRSSSource();

      if ($RSS_Source != null) {

         // Parse url
         $URL_Info = parse_url($RSS_Source);

         // Declare host
         $Host = $URL_Info['host'];

         // Declare Query-String
         $Query = $URL_Info['path'];
         if (isset($URL_Info['query'])) {
            $Query .= '?' . $URL_Info['query'];
         }

         // Create socket
         $Socket = fsockopen($Host, 80, $errno, $errstr, 10);

         // Check if socket was successfully created
         if ($Socket) {

            // Query service
            $Query = 'GET ' . $Query . ' HTTP/1.0' . PHP_EOL;
            $Query .= 'Connection: close' . PHP_EOL . PHP_EOL;
            fwrite($Socket, $Query);

            // Fetch response
            $response = (string) '';
            while (!feof($Socket)) {
               $response .= fgets($Socket, 128);
            }

            // Close socket
            fclose($Socket);
         } else {
            throw new Exception('Socket cannot be created');
            $response = (string) '';
         }

         // Remove header and return string
         $response = substr($response, strpos($response, '<'));

         // Check if xml is valid
         if (preg_match('/<\?xml version="1\.0" encoding="iso-8859-1" \?>/i', $response)) {
            return $response;
         } else {
            return null;
         }
      } else {
         throw new InvalidArgumentException('[weather_v1_controller::getRSSFeed()] Attribute "rss_source" not present in "core:importdesign" tag for weather module!');
         return null;
      }
   }

   /**
    *  @protected
    *
    *  Helper method to read the attribute "rss_source" from the current object<br />
    *  (core:importdesign), that is used to include the module.<br />
    *
    *  @return string Source URL of the RSS stream
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 20.04.2008<br />
    */
   protected function getRSSSource() {
      return $this->__Document->getAttribute('rss_source');
   }

}
?>