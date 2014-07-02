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
namespace APF\tools\html\model;

/**
 * @package APF\tools\html\model
 * @class FrontControllerImportTemplateModel
 *
 * Defines the model structure that your implementation must comply with using
 * the <em>FrontControllerImportTemplateTag</em> tag in combination with a
 * front controller action.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.06.2014<br />
 */
interface FrontControllerImportTemplateModel {

   /**
    * @return string The namespace of the template to load.
    */
   public function getTemplateNamespace();

   /**
    * @return string The
    */
   public function getTemplateName();

}