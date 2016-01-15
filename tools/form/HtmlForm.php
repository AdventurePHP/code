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
 * Represents a APF form element (DOM node).
 * <p/>
 * Allows custom implementations to be used with <em>BaseDocumentController::getForm()</em>.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 12.01.2007 (Form is now handled as a template)<br />
 * Version 0.3, 13.01.2007 (Added mode taglibs)
 * Version 0.4, 15.01.2007 (Added the "form:multiselect" taglib)<br />
 * Version 0.5, 11.02.2007 (Added the "form:validate" taglib)<br />
 * Version 0.6, 25.06.2007 (Replaced "form:validate" with "form:valgroup")<br />
 * Version 0.7, 14.04.2007 (Added "isSent" attribute)<br />
 * Version 0.8, 22.09.2007 (Added the generic validator)<br />
 * Version 0.9, 01.06.2008 (Added the getFormElementsByType() method)<br />
 * Version 1.0, 16.06.2008 (API change: added getFormElementsByTagName())<br />
 * Version 1.1, 30.12.2009 (Added the form:success tag)<br />
 * Version 1.2, 15.12.2012 (Separated from form_control and refactored tag naming to 1.16 concept)<br />
 */
interface HtmlForm extends FormControlFinder, FormElement {

   const METHOD_ATTRIBUTE_NAME = 'method';
   const METHOD_POST_VALUE_NAME = 'post';
   const METHOD_GET_VALUE_NAME = 'get';

   /**
    * Sets the action url of the form.
    *
    * @param string $action The action URL of the form.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    */
   public function setAction($action);

   /**
    * Returns the content of the transformed form.
    *
    * @return string The content of the transformed form.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 20.01.2007 (Changed action attribute handling)<br />
    * Version 0.3, 27.07.2009 (Attribute "name" is not rendered into HTML tag, because of XHTML 1.1 strict)<br />
    */
   public function transformForm();

}
