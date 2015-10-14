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
namespace APF\core\pagecontroller;

/**
 * Defines the scheme of a template expression. Includes both detection whether the
 * current expression matches the token within the template (see <em>applies()</em>)
 * and an concrete factory method to create the document representation for the
 * given template expression token (see <em>getDocument()</em>).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.08.2014<br />
 */
interface TemplateExpression {

   /**
    * Allows the APF parser to check whether the expression implementation matches
    * the currently processed token. In case your implementation is responsible for creating
    * the respective DOM node implementation this method must return <em>true</em>. If
    * it is not responsible return <em>false</em> instead.
    *
    * @param string $token The extended templating syntax token currently being processed.
    *
    * @return bool <em>True</em> in case the token matches the given implementation, <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2014<br />
    */
   public static function applies($token);

   /**
    * This method acts as a DOM node producer (concrete factory) for the DOM node representation of
    * the applied extending templating syntax token.
    * <p/>
    * Implementing a custom templating expression return the initialized DOM node representing the
    * given token. Please note, that the expression parser takes care of providing native APF
    * eco system for the node. This is also the reason why the method only takes the token rather
    * than context, language, parent, etc.
    *
    * @param string $token The extended templating syntax token currently being processed.
    *
    * @return DomNode The DOM node representation of the given token.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2014<br />
    */
   public static function getDocument($token);

}
