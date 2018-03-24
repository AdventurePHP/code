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

/**
 * Implements the extended templating syntax processing functionality for place holders.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.08.2014<br />
 */
class PlaceHolderTemplateExpression implements TemplateExpression {

   public static function applies(string $token) {
      return preg_match('#^[A-Za-z\-0-9_\.]+$#', $token);
   }

   public static function getDocument(string $token) {
      $placeHolder = new PlaceHolderTag();
      $placeHolder->setAttribute('name', $token);

      return $placeHolder;
   }

}
