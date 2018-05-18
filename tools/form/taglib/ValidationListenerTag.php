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
namespace APF\tools\form\taglib;

use APF\tools\form\ValidationListener;

/**
 * Implements a taglib, that outputs it's content, in case you notify
 * the taglib to do so. This mechanism can be used within validator
 * implementations to inject special style or class information into the
 * form's markup on validation errors. For this reason, you can use
 * <pre>
 * $this->control->notifyValidatorListeners();
 * </pre>
 * within the <code>notify()</code> method of the
 * <code>FormValidator</code> implementation. The definition of
 * the tag is as follows:
 * <pre>
 * &lt;form:listener control="..."&gt;
 *   The content to display, in case the form is sent, but the control is invalid!
 *   [&lt;html:getstring namespace="" config="" key="" /&gt;]
 *   [&lt;html:placeholder name="" /&gt;]
 *   [&lt;core:addtaglib namespace="" class="" prefix="" name="" /&gt;]
 * &lt;/form:listener&gt;
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.08.2009<br />
 */
class ValidationListenerTag extends AbstractFormControl implements ValidationListener {

   /**
    * Indicates, whether the listener is notified or not.
    *
    * @var boolean $isNotified
    */
   protected $isNotified = false;

   public function notify() {
      $this->isNotified = true;
   }

   public function isNotified(): bool {
      return $this->isNotified;
   }

   /**
    * Overwrites the parent's method, because there is nothing to do here.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * Outputs the content of the tag, if notified.
    *
    * @return string The content of the tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function transform() {
      if ($this->isNotified === true) {
         $this->transformChildren();

         return $this->content;
      }

      return '';
   }

   public function reset() {
      // nothing to do as listener tags contain no user input
      return $this;
   }

}
