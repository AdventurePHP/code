<?php
namespace APF\core\pagecontroller;

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
 * This class represents a taglib and thus is used as a taglib definition. Each time,
 * you add a known taglib to a DOM node, an instance of the TagLib class is added to
 * the node.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 */
final class TagLib {

   /**
    * The class name of the tag implementation .
    *
    * @var string $class
    */
   private $class;

   /**
    * The prefix of the tag (e.g. <em>core</em> for tag <em>&lt;core:importdesign /&gt;</em>).
    *
    * @var string $prefix
    */
   private $prefix;

   /**
    * The name of the tag (e.g. <em>importdesign</em> for tag <em>&lt;core:importdesign /&gt;</em>).
    *
    * @var string $name
    */
   private $name;

   /**
    * Defines a taglib.
    *
    * @param string $class The fully qualified class name of the tag implementation.
    * @param string $prefix The prefix of the tag  (e.g. <em>core</em> for tag <em>&lt;core:importdesign /&gt;</em>).
    * @param string $name The name of the tag  (e.g. <em>importdesign</em> for tag <em>&lt;core:importdesign /&gt;</em>).
    *
    * @author Christian Schäfer, Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 21.03.2013 (Tag classes are now namespace-aware)<br />
    */
   public function __construct($class, $prefix, $name) {
      $this->class = $class;
      $this->prefix = $prefix;
      $this->name = $name;
   }

   /**
    * Returns the prefix of the taglib.
    *
    * @return string The prefix of the taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function getPrefix() {
      return $this->prefix;
   }

   /**
    * Returns the class of the taglib.
    *
    * @return string The class of the taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function getClass() {
      return $this->class;
   }

   /**
    * @return string The name of the tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.06.2012<br />
    */
   public function getName() {
      return $this->name;
   }

}
