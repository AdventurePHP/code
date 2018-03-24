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
namespace APF\tools\form;

/**
 * Declares the interface of a validation listener tag used to inform users about invalid input.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2018<br />
 */
interface ValidationListener {

   /**
    * Notifies the listener to output the content of the taglib on transform time.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function notify();

   /**
    * @return bool <em>True</em>, in case listener is notified, <em>false</em> otherwise.
    */
   public function isNotified(): bool;

}
