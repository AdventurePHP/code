<?php
namespace APF\extensions\news\pres\documentcontroller;

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
use APF\extensions\news\biz\NewsManager;

/**
 * @package APF\extensions\news\pres\documentcontroller
 * @class NewsBaseController
 *
 * Implements basic functionality for the news extension's document controllers.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.06.2011<br />
 */
abstract class NewsBaseController extends BaseDocumentController {

   /**
    * @return string The application identifier (for login purposes).
    */
   protected function getAppKey() {
      return $this->getDocument()->getAttribute('app-ident', $this->getContext());
   }

   /**
    * @return NewsManager The instance of the business component.
    */
   protected function getNewsManager() {
      return $this->getDIServiceObject('APF\extensions\news', 'NewsManager');
   }

}
