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
 * Defines the structure of a context and language aware object.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2016<br />
 */
interface ApplicationContext {

   /**
    * Sets the context of the current APF object.
    *
    * @param string $context The context.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setContext($context);

   /**
    * Returns the context of the current APF object.
    *
    * @return string The context.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getContext();

   /**
    * Sets the language of the current APF object.
    *
    * @param string $lang The language.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setLanguage($lang);

   /**
    * Returns the language of the current APF object.
    *
    * @return string The language.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function getLanguage();

}
