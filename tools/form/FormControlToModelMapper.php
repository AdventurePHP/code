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
 * A FormControlToModelMapper serves as a translator between a form and it's controls and a model that
 * should be filled with the corresponding form control values.
 * <p/>
 * To customize the mapping capabilities please add your custom implementation using
 * <em>HtmlForm::addFormControlToModelMapper()</em> and/or <em>HtmlForm::clearFormControlToModelMappers()</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2016 (ID#275: introduced value data mappers to be able to customize form to model mappings)<br />
 */
interface FormControlToModelMapper {

   /**
    * This method is used by <em>HtmlForm::fillModel()</em> to determine whether or not a specific
    * mapper implementation applies for the current form control. If yes, this method should return
    * <em>true</em> and the current mapping is conducted using it's <em>getValue()</em> method.
    *
    * @param FormControl $control The form control to retrieve the "real" value from.
    *
    * @return bool True in case this mapping applies, false otherwise.
    */
   public static function applies(FormControl $control);

   /**
    * Returns the "real" value of the form control that should be stored within the model instance.
    *
    * @param FormControl $control The form control to retrieve the "real" value from.
    *
    * @return mixed The "real" value of the form control to be stored within the model.
    */
   public static function getValue(FormControl $control);

}
