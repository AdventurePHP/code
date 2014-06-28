<?php
namespace APF\modules\comments\pres\controller;

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
use APF\core\pagecontroller\BaseDocumentController;

/**
 * Provides basic functionality for the document controllers of the comment module.
 *
 * @author Christian W. Schäfer
 * @version
 * Version 0.1, 21.08.2007<br />
 */
abstract class CommentBaseDocumentController extends BaseDocumentController {

   /**
    * Returns the category key that has been defined within the parent's
    * node's attribute list of the &lt;core:importdesign /&gt; tag.
    *
    * @return string The current category key.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.08.2007<br />
    * Version 0.2, 02.05.2011 (Refactored to return the value to have a more clean API)<br />
    */
   protected function getCategoryKey() {
      $parent = & $this->getDocument()->getParentObject();

      return $parent->getAttribute('categorykey', 'standard');
   }

}
