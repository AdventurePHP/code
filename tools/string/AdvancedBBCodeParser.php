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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @class BBCodeParserDefinition
   *
   *  Internal representation of an BBCodeParserProvider. Nearly the same as the TagLib class for
   *  each Document class.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class BBCodeParserDefinition extends coreObject
   {

      /**
      *  @private
      *  Represents the namespace of the parser provider.
      */
      var $__Namespace;


      /**
      *  @private
      *  Represents the class name of the parser provider an thus the file name (without extentsion!).
      */
      var $__Class;


      /**
      *  @public
      *
      *  Initializes the BBCodeParser definition.
      *
      *  @param string $namespace the namespace of the parser
      *  @param string $class the class name of the parser
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function BBCodeParserDefinition($namespace,$class){
         $this->__Namespace = $namespace;
         $this->__Class = $class;
       // end function
      }

    // end class
   }


   /**
   *  @abstract
   *  @class BBCodeParserProvider
   *
   *  The interface for all concrete implementations of the parser provider.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class BBCodeParserProvider extends coreObject
   {

      function BBCodeParserProvider(){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Interface method for each BBCodeParserProvider, that is called by the AdvancedBBCodeParser.
      *
      *  @param string $string the content to parse
      *  @return string $parsedString the parsed contebt
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function getOutput($string){
      }

    // end class
   }


   /**
   *  @class AdvancedBBCodeParser
   *
   *  Implements the advanced bbcode parser for the adventure php framework.
   *  Usage:
   *  <pre>$bP = &$this->__getServiceObject('tools::string','AdvancedBBCodeParser');
   *  [$bP->addProvider(...,...);]
   *  [$bP->removeProvider(...);]
   *  $string = '...';
   *  echo $bP->parseCode($string);</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2008
   */
   class AdvancedBBCodeParser extends coreObject
   {


      /**
      *  @private
      *  Represents the list of provider.
      */
      var $__Provider = array();


      /**
      *  @public
      *
      *  Sets up the default parser provider. To modify / delete them use the addProvider() or
      *  removeProvider() methods.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function AdvancedBBCodeParser(){
         $this->__Provider['standard.font.style'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontStyleProvider');
         $this->__Provider['standard.font.size'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontSizeProvider');
         $this->__Provider['standard.font.color'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontColorProvider');
         $this->__Provider['standard.newline'] = new BBCodeParserDefinition('tools::string::bbcpprovider','NewLineProvider');
       // end function
      }


      /**
      *  @public
      *
      *  This method enables you to add/modify a parsing provider. If a provider already exists with
      *  the desired name it will be overwritten.
      *
      *  @param string $name short name of the provider, that is intended to address it
      *  @param BBCodeParser $provider a provider representation object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function addProvider($name,$provider){
         $this->__Provider[$name] = $provider;
       // end function
      }


      /**
      *  @public
      *
      *  This method removes a parsing provider.
      *
      *  @param string $name short name of the provider, that is intended to address it
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function removeProvider($name){
         unset($this->__Provider[$name]);
       // end function
      }


      /**
      *  @private
      *
      *  Sets up the provider to be ready for use. Convertes each BBCodeParserDefinition into
      *  it's real implementation. If a provider was added / removed the converter is going to
      *  notive this fact.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function __setUpProvider(){

         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('AdvancedBBCodeParser::__setUpProvider()');

         foreach($this->__Provider as $providerName => $DUMMY){

            if(get_class($this->__Provider[$providerName]) == 'BBCodeParserDefinition'){

               // include class
               $namespace = $this->__Provider[$providerName]->get('Namespace');
               $class = $this->__Provider[$providerName]->get('Class');
               import($namespace,$class);

               // convert definition and initialize provider
               $this->__Provider[$providerName] = &$this->__getServiceObject($namespace,$class,'NORMAL');

             // end if
            }

          // end foreach
         }

         $T->stop('AdvancedBBCodeParser::__setUpProvider()');

       // end function
      }


      /**
      *  @public
      *
      *  Parses the given string using the provider configured before.
      *
      *  @param string $string the string, that should be parsed
      *  @return string $parsedString the parsed string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2008
      */
      function parseCode($string){

         // start timer
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('AdvancedBBCodeParser::parseCode()');

         // set up the desired parser
         $this->__setUpProvider();

         // parse string
         foreach($this->__Provider as $providerName => $DUMMY){
            $string = $this->__Provider[$providerName]->getOutput($string);
          // end function
         }

         // stop timer
         $T->stop('AdvancedBBCodeParser::parseCode()');

         // return parsed string
         return $string;

       // end function
      }

    // end class
   }
?>