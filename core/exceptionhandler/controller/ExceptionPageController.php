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
namespace APF\core\exceptionhandler\controller;

use APF\core\exceptionhandler\model\ExceptionPageViewModel;
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\singleton\Singleton;

/**
 * Implements the exception page's document controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.02.2009<br />
 */
class ExceptionPageController extends BaseDocumentController {

   /**
    * Displays the exception page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   public function transformContent() {

      // get the exception trace, init output buffer
      /* @var $model ExceptionPageViewModel */
      $model = Singleton::getInstance(ExceptionPageViewModel::class);

      $exceptions = $model->getExceptionTrace();
      $buffer = '';

      // get template
      $templateExceptionEntry = $this->getTemplate('ExceptionEntry');

      // generate stacktrace
      for ($i = 0; $i < count($exceptions); $i++) {

         if (isset($exceptions[$i]['function'])) {
            $templateExceptionEntry->setPlaceHolder('Function', $exceptions[$i]['function']);
         }

         if (isset($exceptions[$i]['line'])) {
            $templateExceptionEntry->setPlaceHolder('Line', $exceptions[$i]['line']);
         }

         if (isset($exceptions[$i]['file'])) {
            $templateExceptionEntry->setPlaceHolder('File', $exceptions[$i]['file']);
         }

         if (isset($exceptions[$i]['class'])) {
            $templateExceptionEntry->setPlaceHolder('Class', $exceptions[$i]['class']);
         }

         if (isset($exceptions[$i]['type'])) {
            $templateExceptionEntry->setPlaceHolder('Type', $exceptions[$i]['type']);
         }

         $buffer .= $templateExceptionEntry->transformTemplate();
      }

      $this->setPlaceHolder('Stacktrace', $buffer);

      $this->setData('model', $model);
   }

}
