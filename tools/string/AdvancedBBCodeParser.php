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
namespace APF\tools\string;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\core\singleton\Singleton;
use APF\tools\string\bbcpprovider\FontColorProvider;
use APF\tools\string\bbcpprovider\FontSizeProvider;
use APF\tools\string\bbcpprovider\FontStyleProvider;
use APF\tools\string\bbcpprovider\NewLineProvider;

/**
 * Internal representation of an BBCodeParserProvider. Nearly the same as the TagLib class for
 * each Document class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.10.2008
 */
final class BBCodeParserDefinition {

   /**
    * Represents the class name of the parser provider an thus the file name (without extension!).
    *
    * @var string $class
    */
   private $class;

   /**
    * Initializes the BBCodeParser definition.
    *
    * @param string $class the class name of the parser
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public function __construct($class) {
      $this->class = $class;
   }

   public function getClass() {
      return $this->class;
   }

}

/**
 * The interface for all concrete implementations of the parser provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.10.2008
 */
abstract class BBCodeParserProvider extends APFObject {

   /**
    * Interface method for each BBCodeParserProvider, that is called by the AdvancedBBCodeParser.
    *
    * @param string $string the content to parse
    *
    * @return string $parsedString the parsed content
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public abstract function getOutput($string);

}

/**
 * Implements the advanced bbcode parser for the adventure php framework.
 * Usage:
 * <pre>$bP = &$this->getServiceObject(AdvancedBBCodeParser::class);
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
    *  Represents the list of provider.
    */
   private $provider = [];

   /**
    *  Sets up the default parser provider. To modify / delete them use the addProvider() or
    *  removeProvider() methods.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public function __construct() {
      $this->provider['standard.font.style'] = new BBCodeParserDefinition(FontStyleProvider::class);
      $this->provider['standard.font.size'] = new BBCodeParserDefinition(FontSizeProvider::class);
      $this->provider['standard.font.color'] = new BBCodeParserDefinition(FontColorProvider::class);
      $this->provider['standard.newline'] = new BBCodeParserDefinition(NewLineProvider::class);
   }

   /**
    * This method enables you to add/modify a parsing provider. If a provider already exists with
    * the desired name it will be overwritten.
    *
    * @param string $name short name of the provider, that is intended to address it
    * @param BBCodeParserProvider $provider a provider representation object
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public function addProvider($name, $provider) {
      $this->provider[$name] = $provider;
   }

   /**
    * This method removes a parsing provider.
    *
    * @param string $name short name of the provider, that is intended to address it
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public function removeProvider($name) {
      unset($this->provider[$name]);
   }

   /**
    * Parses the given string using the provider configured before.
    *
    * @param string $string the string, that should be parsed
    *
    * @return string $parsedString the parsed string
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   public function parseCode($string) {

      $t = &Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('AdvancedBBCodeParser::parseCode()');

      // lazily set up the desired parser
      $this->setUpProvider();

      foreach ($this->provider as $provider) {
         /* @var $provider BBCodeParserProvider */
         $string = $provider->getOutput($string);
      }

      $t->stop('AdvancedBBCodeParser::parseCode()');

      return $string;
   }

   /**
    * Sets up the provider to be ready for use. Converts each BBCodeParserDefinition into
    * it's real implementation. If a provider was added / removed the converter is going to
    * notice this fact.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.10.2008
    */
   private function setUpProvider() {

      $t = &Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('AdvancedBBCodeParser::setUpProvider()');

      foreach ($this->provider as $providerName => $providerDefinition) {

         if ($providerDefinition instanceof BBCodeParserDefinition) {
            /* @var $providerDefinition BBCodeParserDefinition */

            $class = $providerDefinition->getClass();

            // convert definition and initialize provider
            $this->provider[$providerName] = $this->getServiceObject($class, [], APFService::SERVICE_TYPE_NORMAL);
         }

      }

      $t->stop('AdvancedBBCodeParser::setUpProvider()');
   }

}
