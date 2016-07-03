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
 * A FormControlToModelMapper serves as a translator between a model and a form and it's controls that
 * should be used to filled.
 * <p/>
 * To customize the mapping capabilities please add your custom implementation using
 * <em>HtmlForm::addFormControlToModelMapper()</em> and/or <em>HtmlForm::clearFormControlToModelMappers()</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.06.2016 (ID#297: introduced data mappers to be able to customize model to form mappings)<br />
 */
interface ModelToFormControlMapper {

   /**
    * This method is used by <em>HtmlForm::fillForm()</em> to determine whether or not a specific
    * mapper implementation applies for the current form control. If yes, this method should return
    * <em>true</em> and the current mapping is conducted using it's <em>setValue()</em> method.
    *
    * @param FormControl $control The form control to inject the "real" value to.
    *
    * @return bool True in case this mapping applies, false otherwise.
    */
   public static function applies(FormControl $control);

   /**
    * Sets the "real" value of the model into the form control.
    *
    * @param FormControl $control The form control to inject the "real" value from.
    * @param mixed $value mixed The "real" value of the model to inject into the form control.
    */
   public static function setValue(FormControl &$control, $value);

}
