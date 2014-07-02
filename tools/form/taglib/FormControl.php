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
namespace APF\tools\form\taglib;

/**
 * @package APF\tools\form\taglib
 * @class FormControl
 *
 * Defines the basic structure/functionality of an APF form control.
 * <p/>
 * It contains the basic methods the APF form tag needs to operate on.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.11.2012 <br />
 */
interface FormControl {

   /**
    * @public
    *
    * Returns the sending status of a form control. Since the framework does not know about
    * custom tags, this status is queried from all controls but it is only relevant for
    * buttons.
    *
    * @return bool True in case the control is sent, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.11.2012<br />
    */
   public function isSent();

   /**
    * @public
    *
    * Returns the validity status of a form control. Since the framework does not know about
    * custom tags, this status is queried from all controls but it is only relevant for
    * input controls.
    *
    * @return bool True in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.11.2012<br />
    */
   public function isValid();

}
