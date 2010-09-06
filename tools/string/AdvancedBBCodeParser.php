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
    * @package tools::string
    * @class BBCodeParserDefinition
    *
    * Internal representation of an BBCodeParserProvider. Nearly the same as the TagLib class for
    * each Document class.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   final class BBCodeParserDefinition extends APFObject {

      /**
       * @private
       * @var string Represents the namespace of the parser provider.
       */
      private $namespace;

      /**
       * @private
       * @var string Represents the class name of the parser provider an thus the file name
       * (without extentsion!).
       */
      private $class;

      /**
       * @public
       *
       * Initializes the BBCodeParser definition.
       *
       * @param string $namespace the namespace of the parser
       * @param string $class the class name of the parser
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
      public function __construct($namespace,$class){
         $this->namespace = $namespace;
         $this->class = $class;
      }

      public function getNamespace() {
         return $this->namespace;
      }

      public function getClass() {
         return $this->class;
      }

    // end class
   }

   /**
    * @abstract
    * @package tools::string
    * @class BBCodeParserProvider
    *
    * The interface for all concrete implementations of the parser provider.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   abstract class BBCodeParserProvider extends APFObject {

      /**
       * @public
       * @abstract
       *
       * Interface method for each BBCodeParserProvider, that is called by the AdvancedBBCodeParser.
       *
       * @param string $string the content to parse
       * @return string $parsedString the parsed contebt
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
      public abstract function getOutput($string);

    // end class
   }

   /**
    * @package tools::string
    * @class AdvancedBBCodeParser
    *
    * Implements the advanced bbcode parser for the adventure php framework.
    * Usage:
    * <pre>$bP = &$this->__getServiceObject('tools::string','AdvancedBBCodeParser');
    * [$bP->addProvider(...,...);]
    * [$bP->removeProvider(...);]
    * $string = '...';
    * echo $bP->parseCode($string);</pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   class AdvancedBBCodeParser extends APFObject {

      /**
      *  @private
      *  Represents the list of provider.
      */
      private $provider = array();

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
      public function __construct(){
         $this->provider['standard.font.style'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontStyleProvider');
         $this->provider['standard.font.size'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontSizeProvider');
         $this->provider['standard.font.color'] = new BBCodeParserDefinition('tools::string::bbcpprovider','FontColorProvider');
         $this->provider['standard.newline'] = new BBCodeParserDefinition('tools::string::bbcpprovider','NewLineProvider');
      }

      /**
       * @public
       *
       * This method enables you to add/modify a parsing provider. If a provider already exists with
       * the desired name it will be overwritten.
       *
       * @param string $name short name of the provider, that is intended to address it
       * @param BBCodeParser $provider a provider representation object
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
      public function addProvider($name,$provider){
         $this->provider[$name] = $provider;
      }

      /**
       * @public
       *
       * This method removes a parsing provider.
       *
       * @param string $name short name of the provider, that is intended to address it
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
      public function removeProvider($name){
         unset($this->provider[$name]);
      }

      /**
       * @private
       *
       * Sets up the provider to be ready for use. Convertes each BBCodeParserDefinition into
       * it's real implementation. If a provider was added / removed the converter is going to
       * notive this fact.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
      private function setUpProvider(){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('AdvancedBBCodeParser::__setUpProvider()');

         foreach($this->provider as $providerName => $DUMMY){

            if(get_class($this->provider[$providerName]) == 'BBCodeParserDefinition'){

               // include class
               $namespace = $this->provider[$providerName]->getNamespace();
               $class = $this->provider[$providerName]->getClass();
               import($namespace,$class);

               // convert definition and initialize provider
               $this->provider[$providerName] = &$this->__getServiceObject($namespace,$class,'NORMAL');

             // end if
            }

          // end foreach
         }

         $t->stop('AdvancedBBCodeParser::__setUpProvider()');

       // end function
      }

      /**
       * @public
       *
       * Parses the given string using the provider configured before.
       *
       * @param string $string the string, that should be parsed
       * @return string $parsedString the parsed string
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.10.2008
       */
     public function parseCode($string){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('AdvancedBBCodeParser::parseCode()');

         // set up the desired parser
         $this->setUpProvider();

         // parse string
         foreach($this->provider as $providerName => $DUMMY){
            $string = $this->provider[$providerName]->getOutput($string);
          // end function
         }

         // stop timer
         $t->stop('AdvancedBBCodeParser::parseCode()');

         return $string;

       // end function
      }

    // end class
   }
?>