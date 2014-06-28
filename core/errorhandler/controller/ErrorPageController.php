<?php
namespace APF\core\errorhandler\documentcontroller;

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
use APF\core\registry\Registry;

/**
 * @package APF\core\errorhandler\documentcontroller
 * @class ErrorPageController
 *
 * Implements the error page's document controller.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 21.01.2007<br />
 */
class ErrorPageController extends BaseDocumentController {

   /**
    * @public
    *
    * Displays the error page.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.01.2007<br />
    * Version 0.2, 26.12.2008 (Messages after the trigger_error() are not displayed any more)<br />
    */
   public function transformContent() {

      // build stack trace
      $errors = array_reverse(debug_backtrace());
      $buffer = (string)'';

      $errorEntry = & $this->getTemplate('ErrorEntry');

      // generate stack trace
      for ($i = 0; $i < count($errors); $i++) {

         // don't display any further messages, because these belong to the error handler
         if (isset($errors[$i]['function']) && preg_match('/handleError|handleFatalError/i', $errors[$i]['function'])) {
            break;
         }

         if (isset($errors[$i]['function'])) {
            $errorEntry->setPlaceHolder('Function', $errors[$i]['function']);
         }

         if (isset($errors[$i]['line'])) {
            $errorEntry->setPlaceHolder('Line', $errors[$i]['line']);
         }

         if (isset($errors[$i]['file'])) {
            $errorEntry->setPlaceHolder('File', $errors[$i]['file']);
         }

         if (isset($errors[$i]['class'])) {
            $errorEntry->setPlaceHolder('Class', $errors[$i]['class']);
         }

         if (isset($errors[$i]['type'])) {
            $errorEntry->setPlaceHolder('Type', $errors[$i]['type']);
         }

         $buffer .= $errorEntry->transformTemplate();
      }

      $this->setPlaceHolder('Stacktrace', $buffer);

      $document = $this->getDocument();
      $this->setPlaceHolder('ErrorID', $document->getAttribute('id'));
      $this->setPlaceHolder('ErrorMessage', htmlspecialchars($document->getAttribute('message'), ENT_QUOTES, Registry::retrieve('APF\core', 'Charset'), false));
      $this->setPlaceHolder('ErrorNumber', $document->getAttribute('number'));
      $this->setPlaceHolder('ErrorFile', $document->getAttribute('file'));
      $this->setPlaceHolder('ErrorLine', $document->getAttribute('line'));

      $this->setPlaceHolder('GenerationDate', date('r'));
   }

}
