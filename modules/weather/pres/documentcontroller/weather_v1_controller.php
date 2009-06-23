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
   *  @namespace modules::weather::pres::documentcontroller
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
   class weather_v1_controller extends baseController
   {

      /**
      *  @protected
      *  Version von PHP
      */
      protected $__PHPVersion;



      /**
      *  @public
      *
      *  Gathers the PHP version used.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      function weather_v1_controller(){
         $this->__PHPVersion = (int)phpversion();
       // end function
      }


      /**
      *  @public
      *
      *  Implements the abstract method transformContent() of the baseController class.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.04.2008<br />
      *  Version 0.2, 26.04.2008 (Transformation for PHP4 and PHP5 implemented separately)<br />
      */
      function transformContent(){

         // Fetch XML string
         $XML = $this->__getRSSFeed();

         // Check weather XML string is present
         if($XML != null){

            if($this->__PHPVersion > 4){
               $this->__transformPHP5($XML);
             // end if
            }
            else{
               $this->__transformPHP4($XML);
             // end else
            }

          // end if
         }
         else{

            // Display error message
            $Template__NoEntries = &$this->__getTemplate('NoEntries_'.$this->__Language);
            $Template__NoEntries->setPlaceHolder('Source',$this->__getRSSSource());
            $this->setPlaceHolder('Content',$Template__NoEntries->transformTemplate());

          // end else
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Transforms the content in PHP 4 environments.<br />
      *
      *  @param string $XML; XML string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      protected function __transformPHP4($XML){

         // Get references on the templates used
         $Template__Channel = &$this->__getTemplate('Channel');
         $Template__Item = &$this->__getTemplate('Item');

         // Create DOM document and get an reference on the channel node
         $DomDoc = domxml_open_mem($XML);
         $RSS = &$this->__getChildNodesByNodeName($DomDoc,'rss');
         $Channel = &$this->__getChildNodesByNodeName($RSS[0],'channel');

         // Title
         $Titles = &$this->__getChildNodesByNodeName($Channel[0],'title');
         $Template__Channel->setPlaceHolder('Title',utf8_decode($this->__getFirstChildContent($Titles[0])));

         // Link
         $Links = &$this->__getChildNodesByNodeName($Channel[0],'link');
         $Template__Channel->setPlaceHolder('Link',utf8_decode($this->__getFirstChildContent($Links[0])));

         // Description
         $Descriptions = &$this->__getChildNodesByNodeName($Channel[0],'description');
         $Template__Channel->setPlaceHolder('Description',utf8_decode($this->__getFirstChildContent($Descriptions[0])));

         // Get and display items of current channel
         $Items = &$this->__getChildNodesByNodeName($Channel[0],'item');
         $count = count($Items);
         $Buffer = (string)'';

         for($i = 0; $i < $count; $i++){

            // Title
            $Titles = &$this->__getChildNodesByNodeName($Items[$i],'title');
            $Template__Item->setPlaceHolder('Title',utf8_decode($this->__getFirstChildContent($Titles[0])));


            // Link
            $Links = &$this->__getChildNodesByNodeName($Items[$i],'link');
            $Template__Item->setPlaceHolder('Link',utf8_decode($this->__getFirstChildContent($Links[0])));


            // Description
            $Descriptions = &$this->__getChildNodesByNodeName($Items[$i],'description');
            $Template__Item->setPlaceHolder('Description',utf8_decode($this->__getFirstChildContent($Descriptions[0])));

            $Buffer .= $Template__Item->transformTemplate();

          // end for
         }


         // Insert items in channel template
         $Template__Channel->setPlaceHolder('Items',$Buffer);

         // Transform and display channel
         $this->setPlaceHolder('Content',$Template__Channel->transformTemplate());

       // end function
      }


      /**
      *  @protected
      *
      *  Transforms the content in PHP 5 environments.<br />
      *
      *  @param string $XML; XML string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      protected function __transformPHP5($XML){

         // Get references on the templates used
         $Template__Channel = &$this->__getTemplate('Channel');
         $Template__Item = &$this->__getTemplate('Item');

         // Create DOM document and get an reference on the channel node
         $DomDoc = simplexml_load_string($XML);
         $RSS = $DomDoc->xpath('/rss/channel');
         $Channel = $DomDoc->xpath('/rss/channel/item');


         // Title
         $Template__Channel->setPlaceHolder('Title',utf8_decode($RSS[0]->title));

         // Link
         $Template__Channel->setPlaceHolder('Link',utf8_decode($RSS[0]->link));

         // Description
         $Template__Channel->setPlaceHolder('Description',utf8_decode($RSS[0]->description));

         // Display items of current channel
         $count = count($Channel);
         $Buffer = (string)'';

         for($i = 0; $i < $count; $i++){

            // Title
            $Template__Item->setPlaceHolder('Title',utf8_decode($Channel[$i]->title));

            // Link
            $Template__Item->setPlaceHolder('Link',utf8_decode($Channel[$i]->link));

            // Description
            $Template__Item->setPlaceHolder('Description',utf8_decode($Channel[$i]->description));

            $Buffer .= $Template__Item->transformTemplate();

          // end for
         }


         // Insert items in channel template
         $Template__Channel->setPlaceHolder('Items',$Buffer);

         // Transform and display channel
         $this->setPlaceHolder('Content',$Template__Channel->transformTemplate());

       // end function
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
      protected function __getFirstChildContent($Node){

         // Get first child
         $FirstChild = $Node->first_child();

         // Return content
         return $FirstChild->node_value();

       // end function
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
      protected function &__getChildNodesByNodeName(&$Node,$Name){

         // Get all child nodes
         $AllChildNodes = $Node->child_nodes();
         $count = count($AllChildNodes);
         $ChildNodes = array();

         // Search for desired child nodes
         for($i = 0; $i < $count; $i++){

            if($AllChildNodes[$i]->node_name() == $Name){
               $ChildNodes[] = &$AllChildNodes[$i];
             // end if
            }

          // end foreach
         }

         // Return list
         return $ChildNodes;

       // end function
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
      protected function __getRSSFeed(){

         // Fetch RSS source
         $RSS_Source = $this->__getRSSSource();

         if($RSS_Source != null){

            // Parse url
            $URL_Info = parse_url($RSS_Source);

            // Declare host
            $Host = $URL_Info['host'];

            // Declare Query-String
            $Query = $URL_Info['path'];
            if(isset($URL_Info['query'])){
               $Query .= '?'.$URL_Info['query'];
             // end if
            }

            // Create socket
            $Socket = fsockopen($Host,80,$errno,$errstr,10);

            // Check if socket was successfully created
            if($Socket){

               // Query service
               $Query = 'GET '.$Query.' HTTP/1.0'.PHP_EOL;
               $Query .= 'Connection: close'.PHP_EOL.PHP_EOL;
               fwrite($Socket,$Query);

               // Fetch response
               $Response = (string)'';
               while(!feof($Socket)){
                 $Response .= fgets($Socket, 128);
                // end while
               }

               // Close socket
               fclose($Socket);

             // end if
            }
            else{
               trigger_error('Socket cannot be created');
               $Response = (string)'';
             // end else
            }

            // Remove header and return string
            $Response = substr($Response,strpos($Response,'<'));

            // Check if xml is valid
            if(preg_match('/<\?xml version="1\.0" encoding="iso-8859-1" \?>/i',$Response)){
               return $Response;
             // end if
            }
            else{
               return null;
             // end else
            }

          // end if
         }
         else{
            trigger_error('[weather_v1_controller::__getRSSFeed()] Attribute "rss_source" not present in "core:importdesign" tag for weather module!');
            return null;
          // end else
         }

       // end function
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
      protected function __getRSSSource(){
         return $this->__Document->getAttribute('rss_source');
       // end function
      }

    // end class
   }
?>