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
namespace APF\tools\form;

/**
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

   /**
    * Allows to reset a form or form control within a document controller. In case of form and
    * form group the reset() event is propagated to all child elements.
    * <p/>
    * What happens when resetting a form control is up to the dedicated implementation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.09.2014 (ID#206: Added interface enhancement to allow resetting)<br />
    */
   public function reset();

   /**
    * Hides a form control from the HTML output of the form it is contained in. Together with
    * it's <em>dependent controls</em> you can hide entire parts of a form from being displayed
    * on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return FormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function &hide();

   /**
    * Shows a previously hidden form control from the HTML output of the form it is contained in.
    * Together with it's <em>dependent controls</em> you can show entire parts of a form from being
    * displayed on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return FormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function &show();

}
