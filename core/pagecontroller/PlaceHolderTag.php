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
 * @package APF\core\pagecontroller
 * @class PlaceHolderTag
 *
 * Represents a place holder within a template file. Can be filled within a document controller
 * using the setPlaceHolder() method.
 *
 * @author Christian Achatz, Jan Wiese
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 02.01.2013 (Introduced string place holder mechanism)<br />
 */
class PlaceHolderTag extends Document {

   /**
    * @since 1.17
    * @var string[] Replacement strings for string place holders.
    */
   protected $stringReplacement = array();

   public function __construct() {
      // do nothing, especially not initialize tag libs
   }

   /**
    * @public
    *
    * Let's you set a string replacement to the current place holder instance.
    * <p/>
    * Please note, that the keys must be specified in uppercase letters.
    *
    * @param string $key Name of the string place holder.
    * @param string $value Replacement value.
    *
    * @since 1.17
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   public function setStringReplacement($key, $value) {
      $this->stringReplacement[strtoupper($key)] = $value;
   }

   /**
    * @public
    *
    * Implements the transform() method. Returns the content of the tag, that is set by a
    * document controller using the BaseDocumentController's setPlaceHolder() method.
    *
    * @return string The content of the place holder.
    *
    * @author Christian Schäfer, Jan Wiese
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 06.02.2013 (Added string place holder support)<br />
    */
   public function transform() {
      // preserve content to allow multiple transformation
      $content = $this->content;
      foreach ($this->stringReplacement as $key => $value) {
         $content = str_replace('{' . $key . '}', $value, $content);
      }

      return $content;
   }

}
